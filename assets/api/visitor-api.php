<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Use Sri Lankan timezone for all date operations
date_default_timezone_set('Asia/Colombo');

$dataFile = __DIR__ . '/../data/visitor-data.json';


/* ========= SAFE READ (with shared lock) ========= */

function readJson($file) {

    // Create default data if file is missing
    if (!file_exists($file)) {
        $default = [
            'totalVisits'  => 0,
            'dailyVisits'  => 0,
            'lastVisit'    => null,
            'lastVisitDate'=> null,
            'firstVisit'   => date('Y-m-d\TH:i:s.v\Z')
        ];
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($file, json_encode($default, JSON_PRETTY_PRINT));
        return $default;
    }

    $content = file_get_contents($file);
    if ($content === false || trim($content) === '') {
        return [
            'totalVisits'  => 0,
            'dailyVisits'  => 0,
            'lastVisit'    => null,
            'lastVisitDate'=> null,
            'firstVisit'   => date('Y-m-d\TH:i:s.v\Z')
        ];
    }

    $data = json_decode($content, true);

    // If corrupted, return defaults (but do NOT overwrite — let the write path handle it)
    if ($data === null) {
        return [
            'totalVisits'  => 0,
            'dailyVisits'  => 0,
            'lastVisit'    => null,
            'lastVisitDate'=> null,
            'firstVisit'   => date('Y-m-d\TH:i:s.v\Z')
        ];
    }

    return $data;
}


/* ========= ATOMIC READ-MODIFY-WRITE ========= */

function atomicIncrement($file) {

    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    // Open with c+ so we can read and write; file is created if missing
    $fp = fopen($file, 'c+');
    if (!$fp) {
        return false;
    }

    // Exclusive lock — blocks other writers
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return false;
    }

    // Read current data
    $size = filesize($file);
    $content = $size > 0 ? fread($fp, $size) : '';
    $data = json_decode($content, true);

    if ($data === null || !is_array($data)) {
        $data = [
            'totalVisits'  => 0,
            'dailyVisits'  => 0,
            'lastVisit'    => null,
            'lastVisitDate'=> null,
            'firstVisit'   => date('Y-m-d H:i:s')
        ];
    }

    // Today's date in Sri Lankan time (Asia/Colombo)
    $todaySLT = date('Y-m-d');
    $nowSLT   = date('Y-m-d H:i:s');

    // Always increment total
    $data['totalVisits'] = (int)($data['totalVisits'] ?? 0) + 1;

    // Reset daily count if the stored date is not today (Sri Lankan time)
    if (isset($data['lastVisitDate']) && $data['lastVisitDate'] === $todaySLT) {
        $data['dailyVisits'] = (int)($data['dailyVisits'] ?? 0) + 1;
    } else {
        // New day — reset daily counter
        $data['dailyVisits'] = 1;
    }

    $data['lastVisitDate'] = $todaySLT;
    $data['lastVisit']     = $nowSLT;

    // Write back
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);

    flock($fp, LOCK_UN);
    fclose($fp);

    return $data;
}


/* ========= GET — return current stats ========= */

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $data = readJson($dataFile);

    // Check if daily visits need resetting (in case no one has visited yet today)
    $todaySLT = date('Y-m-d');
    if (isset($data['lastVisitDate']) && $data['lastVisitDate'] !== $todaySLT) {
        // Don't modify the file — just return 0 for today's display
        $data['dailyVisits'] = 0;
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


/* ========= POST — record a visit (server-side increment) ========= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $result = atomicIncrement($dataFile);

    if ($result !== false) {
        echo json_encode([
            'success' => true,
            'data'    => $result
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to record visit'
        ]);
    }

    exit;
}


/* ========= METHOD NOT ALLOWED ========= */

http_response_code(405);

echo json_encode([
    'success' => false,
    'message' => 'Method not allowed'
]);
