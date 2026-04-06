<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

$dataFile = __DIR__ . '/../data/visitor-data.json';


/* ========= SAFE READ ========= */

function readJson($file) {

    // Create empty JSON if missing
    if (!file_exists($file)) {

        file_put_contents($file, json_encode(new stdClass()));
        return new stdClass();
    }

    $fp = fopen($file, 'r');

    if (!$fp) {
        echo json_encode(new stdClass());
        exit;
    }

    flock($fp, LOCK_SH);

    $content = filesize($file) > 0
        ? fread($fp, filesize($file))
        : '{}';

    flock($fp, LOCK_UN);
    fclose($fp);

    $data = json_decode($content);

    // If corrupted → reset to empty object
    if ($data === null) {

        file_put_contents($file, json_encode(new stdClass()));
        return new stdClass();
    }

    return $data;
}


/* ========= SAFE WRITE ========= */

function writeJson($file, $data) {

    $dir = dirname($file);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $tempFile = $file . '.tmp';

    $fp = fopen($tempFile, 'w');

    if (!$fp) {
        return false;
    }

    flock($fp, LOCK_EX);

    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    rename($tempFile, $file);

    return true;
}



/* ========= GET ========= */

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $data = readJson($dataFile);

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}



/* ========= POST ========= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $newData = json_decode($input);

    if ($newData === null) {

        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON'
        ]);

        exit;
    }

    if (writeJson($dataFile, $newData)) {

        echo json_encode([
            'success' => true,
            'message' => 'Data saved'
        ]);

    } else {

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Write failed'
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
