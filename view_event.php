<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: gallery.php");
    exit();
}

$event_id = $_GET['id'];

// Fetch Event Details
$stmt = $conn->prepare("SELECT id, category, title, description, event_date, cover_image FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stmt->bind_result($evt_id, $evt_category, $evt_title, $evt_description, $evt_date, $evt_cover);
$event = null;
if ($stmt->fetch()) {
    $event = [
        'id' => $evt_id,
        'category' => $evt_category,
        'title' => $evt_title,
        'description' => $evt_description,
        'event_date' => $evt_date,
        'cover_image' => $evt_cover
    ];
}
$stmt->close();

if (!$event) {
    die("Event not found.");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($event['title']); ?> - Social Security Board</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <style>
        :root {
            --navy-blue: #0a192f;
            --navy-blue-light: #112240;
            --accent-color: #64ffda;
            --text-color: #e6f1ff;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
        }

        /* Top contact bar */
        .contact-bar {
            background-color: var(--navy-blue);
            color: var(--text-color);
            padding: 10px 0;
            font-size: 14px;
        }

        .contact-bar i {
            color: var(--accent-color);
            margin-right: 6px;
        }

        .contact-item {
            margin-right: 20px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            color: var(--accent-color);
        }

        /* Main header */
        .main-header {
            background-color: var(--navy-blue-light);
            padding: 10px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 100;
        }

        .logo-section img {
            max-height: 100px;
            transition: all 0.3s ease;
        }

        .logo-section img:hover {
            transform: scale(1.05);
        }

        /* ---------- NAVBAR BASE ---------- */
        .navbar {
            background-color: var(--navy-blue-light);
            padding: 0;
            border-top: 1px solid rgba(230, 241, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 9999;
        }

        .nav-item {
            position: relative;
            z-index: 1000;
        }

        .nav-link {
            color: var(--text-color) !important;
            font-weight: 500;
            padding: 20px 18px !important;
            transition: all 0.3s ease;
            position: relative;
            font-size: 15px;
            letter-spacing: 0.5px;
        }

        /* prevent pseudo elements from capturing clicks */
        .nav-link::before,
        .nav-link::after {
            pointer-events: none;
        }

        /* underline pseudo element */
        .nav-link:before {
            content: "";
            position: absolute;
            width: 0;
            height: 3px;
            bottom: 0;
            left: 0;
            background-color: var(--accent-color);
            transition: all 0.3s ease;
            opacity: 0;
        }

        .nav-link:hover {
            color: var(--accent-color) !important;
        }

        .nav-link:hover:before {
            width: 100%;
            opacity: 1;
        }

        /* ---------- DROPDOWN (default desktop styling) ---------- */
        .dropdown-menu {
            background-color: var(--navy-blue);
            border: none;
            border-radius: 4px;
            margin-top: 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            padding: 0;
            min-width: 200px;

            /* desktop hidden-by-default transition */
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: all 0.3s ease;
            display: block;
            /* keep in flow for JS toggling */
            z-index: 1000;
            pointer-events: auto;
            /* ensure clickable by default */
        }

        .dropdown-item {
            color: var(--text-color);
            padding: 12px 20px;
            font-size: 14px;
            border-bottom: 1px solid rgba(230, 241, 255, 0.05);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1001;
            background-color: #0f2a3e;
            /* ensure items are above menu background */
        }

        .dropdown-item:hover {
            background-color: var(--navy-blue-light);
            color: var(--accent-color);
            padding-left: 25px;
        }

        .dropdown-toggle::after {
            margin-left: 5px;
            vertical-align: middle;
            transition: transform 0.3s ease;
        }

        /* Utility and search */
        .utility-section {
            display: flex;
            align-items: center;
        }

        .search-btn {
            background: transparent;
            border: none;
            color: var(--text-color);
            font-size: 18px;
            margin-right: 15px;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            color: var(--accent-color);
            transform: scale(1.1);
        }

        .lang-selector .dropdown-toggle {
            background-color: transparent;
            border: 1px solid rgba(230, 241, 255, 0.2);
            color: var(--text-color);
            padding: 8px 15px;
            border-radius: 4px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .lang-selector .dropdown-toggle:hover {
            background-color: rgba(230, 241, 255, 0.1);
            border-color: var(--accent-color);
        }

        /* mobile toggle hide/show default */
        .mobile-menu-toggle {
            color: var(--text-color);
            font-size: 24px;
            cursor: pointer;
            display: none;
        }

        /* ---------- DESKTOP HOVER ONLY (>=992px) ---------- */
        @media (min-width: 992px) {
            .desktop-nav {
                display: block;
                position: static;
            }

            .dropdown:hover .dropdown-menu {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                pointer-events: auto;
            }

            .dropdown:hover .dropdown-toggle::after {
                transform: rotate(180deg);
            }
        }

        /* ---------- MOBILE STYLES (<992px) ---------- */
        @media (max-width: 991.98px) {

            /* main nav (collapsed) */
            .desktop-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background-color: var(--navy-blue);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
                z-index: 10050;
                /* raised to ensure nothing overlays it */
                -webkit-overflow-scrolling: touch;
            }

            .desktop-nav.show {
                display: block;
            }

            .mobile-menu-toggle {
                display: block;
            }

            .nav-item {
                display: block;
                margin: 0;
                border-bottom: 1px solid rgba(230, 241, 255, 0.05);
            }

            .nav-link {
                padding: 15px 20px !important;
            }

            /* Mobile dropdown collapse using max-height for smooth animation */
            .desktop-nav .dropdown-menu {
                position: relative;
                /* keep in flow and avoid overlay issues */
                background-color: rgba(0, 0, 0, 0.04);
                /* subtle background */
                opacity: 1;
                visibility: visible;
                transform: none;
                box-shadow: none;
                margin: 0;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.28s ease;
                display: block;
                /* keep in flow */
                pointer-events: auto !important;
                /* ensure clicks pass through */
                z-index: 10060;
                /* above other content */
            }

            /* Open state (either parent .dropdown.show or the menu itself .show) */
            .desktop-nav .dropdown.show>.dropdown-menu,
            .desktop-nav .dropdown-menu.show {
                max-height: 1200px;
                /* larger to accommodate many items if needed */
            }

            /* ensure hover rules don't interfere on mobile */
            .dropdown:hover .dropdown-menu {
                pointer-events: none;
            }

            .utility-section {
                padding: 20px;
                flex-direction: column;
                align-items: flex-start;
            }

            .search-btn {
                margin-bottom: 15px;
            }

            /* language dropdown placement */
            .lang-selector .dropdown-menu {
                min-width: 120px;
                right: 0;
                left: auto;
            }

            /* Make sure anchor tags inside dropdown fill the area and are clickable */
            .desktop-nav .dropdown-menu a,
            .desktop-nav .dropdown-item {
                display: block;
                width: 100%;
                cursor: pointer;
                pointer-events: auto;
            }

            /* If some other element overlays the nav, this helps ensure nav is top-most */
            body> :not(.navbar):not(.desktop-nav) {
                position: relative;
                z-index: 0;
            }

            .logo-section img {
                max-width: 280%;
                align-self: center;
            }
        }



        /* Animation classes */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        .slide-down {
            animation: slideDown 0.5s ease forwards;
        }

        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slide {
            min-width: 100%;
            display: none;
        }

        .slide.active {
            display: block;
        }

        .theme {
            position: relative;
            width: 100%;
            height: 150px;
            overflow: hidden;
            background: linear-gradient(#0a192f, #112240);
            /* background-image: url('s4.jpg'); */
        }

        .theme::before {
            background-image: url('assets/images/gallery.jpg');
            display: flex;
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            /* z-index: -1; */
            animation: zoom 20s infinite alternate;
        }

        .theme-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;
            z-index: 2;
        }

        .theme-title {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
            letter-spacing: 2px;
            animation: fadeInUp 1.2s ease-out forwards, glow 3s infinite alternate;
        }

        .theme-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, rgba(0, 90, 170, 0.7) 0%, rgba(0, 40, 85, 0.7) 50%);
            z-index: 1;
        }

        .theme-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        @keyframes zoom {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.1);
            }
        }

        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes glow {
            0% {
                text-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
            }

            100% {
                text-shadow: 0 0 15px rgba(255, 255, 255, 1), 0 0 30px rgba(0, 150, 255, 0.8);
            }
        }

        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-500px) translateX(100px);
                opacity: 0;
            }
        }


        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modern-box {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 2rem;
            height: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            flex: 1;
            margin-bottom: 2rem;
        }

        .modern-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        }

        .modern-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #64ffda 0%, #0a192f 100%);
        }

        .modern-box p {
            color: #555;
            line-height: 1.7;
            margin-bottom: 0;
        }

        .containerr {
            /* display: flex;
            justify-content: space-between;
            gap: 30px; */
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 1s ease;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* --- Search section --- */
        .utility-section {
            align-items: center;
        }

        .search-container {
            position: relative;
        }

        /* 🔍 Larger clickable area */
        .search-btn {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            color: white;
            font-size: 1.4rem;
            cursor: pointer;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .search-btn:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .search-form {
            position: absolute;
            top: 55px;
            right: 0;
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
            opacity: 0;
            transform: scale(0.9);
            pointer-events: none;
            transition: all 0.3s ease;
            width: 260px;
            z-index: 1000;
        }

        .search-form.active {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }

        .search-input {
            width: 100%;
            border: 1px solid #ccc;
            padding: 7px 10px;
            border-radius: 5px;
            outline: none;
        }

        .search-results {
            margin-top: 8px;
            border-top: 1px solid #ddd;
            max-height: 180px;
            overflow-y: auto;
        }

        .search-result-item {
            display: block;
            padding: 8px 10px;
            text-decoration: none;
            color: #333;
            border-radius: 5px;
            transition: background 0.2s ease;
        }

        .search-result-item:hover {
            background: #f0f0f0;
            text-decoration: none;
        }

        .no-results {
            padding: 8px;
            color: #888;
            font-size: 0.9rem;
        }

        @media (max-width: 992px) {
            .footer-links li {
                margin-bottom: 2px;
            }
        }

        @media (max-width: 576px) {
            .footer-links li {
                margin-bottom: 2px;
            }

            .theme-title {
                font-size: 30px;
            }
        }

        /* --- Gallery Section --- */
        .modern-gallery {
            width: 100%;
            margin: 0 auto;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            align-items: stretch;
        }

        @media (max-width: 992px) {
            .gallery-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: linear-gradient(180deg, #ffffff, #f6fbff);
            box-shadow: 0 8px 24px rgba(10, 25, 47, 0.08);
            cursor: pointer;
            transition: transform .35s cubic-bezier(.2, .9, .2, 1), box-shadow .35s;
            display: flex;
            align-items: center;
            justify-content: center;
            aspect-ratio: 4 / 3;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .6s ease;
        }

        .gallery-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 18px 40px rgba(10, 25, 47, 0.12);
        }

        .gallery-item:hover img {
            transform: scale(1.08);
        }

        .gallery-caption {
            position: absolute;
            left: 12px;
            bottom: 12px;
            background: rgba(0, 0, 0, 0.55);
            color: #fff;
            padding: 8px 10px;
            border-radius: 8px;
            font-size: 0.95rem;
            backdrop-filter: blur(4px);
        }

        /* Modal custom look */
        .modal-gallery .modal-content {
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .modal-gallery .modal-body {
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.45));
            border-radius: 12px;
        }

        .modal-gallery img {
            max-width: 100%;
            max-height: 80vh;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .modal-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            transform: translateY(-50%);
            display: flex;
            justify-content: space-between;
            pointer-events: none;
        }

        .modal-controls button {
            pointer-events: auto;
            background: rgba(255, 255, 255, 0.06);
            border: none;
            color: #fff;
            width: 48px;
            height: 48px;
            border-radius: 8px;
            margin: 0 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s, transform .15s;
        }

        .modal-controls button:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: scale(1.05);
        }

        .modal-caption {
            color: #fff;
            padding: 10px 14px;
            font-size: 0.95rem;
            text-align: center;
        }

        .modal {
            z-index: 11000 !important;
        }

        .modal-backdrop {
            z-index: 10900 !important;
        }

        .modal-gallery .modal-dialog {
            margin-top: 0;
        }

        body.modal-open .navbar {
            z-index: 1 !important;
        }

        /* Footer Styles */
        .footer {
            background-color: var(--navy-blue);
            color: var(--text-color);
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .footer-top {
            background-color: var(--navy-blue-light);
            padding: 40px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .footer-widget h5 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 25px;
            position: relative;
            color: var(--accent-color);
        }

        .footer-widget h5:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 40px;
            height: 2px;
            background-color: var(--accent-color);
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 14px;
            position: relative;
            padding-left: 15px;
        }

        .footer-links a:before {
            content: '•';
            position: absolute;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            opacity: 0.7;
        }

        .footer-links a:hover {
            color: var(--accent-color);
            padding-left: 20px;
        }

        .footer-links a:hover:before {
            left: 5px;
            opacity: 1;
        }

        .contact-info {
            margin-bottom: 20px;
        }

        .contact-info i {
            color: var(--accent-color);
            margin-right: 10px;
            font-size: 16px;
            width: 20px;
            text-align: center;
        }

        .contact-info p {
            margin-bottom: 10px;
            color: white;
            font-size: 14px;
        }

        .social-links {
            margin-top: 20px;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.05);
            color: var(--text-color);
            margin-right: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            background-color: var(--accent-color);
            color: var(--navy-blue);
            transform: translateY(-3px);
        }

        .footer-bottom {
            padding: 20px 0;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            font-size: 14px;
        }

        .copyright {
            color: white;
        }

        .powered-by {
            color: white;
            text-align: center;
        }

        .made-with {
            text-align: right;
            color: white;
        }

        .made-with i {
            color: #e25555;
            animation: heartbeat 1.5s infinite;
        }

        @keyframes heartbeat {
            0% {
                transform: scale(1);
            }

            25% {
                transform: scale(1.1);
            }

            50% {
                transform: scale(1);
            }

            75% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }

        .footer-logo {
            max-height: 60px;
            margin-bottom: 20px;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 40px;
            height: 40px;
            background-color: var(--accent-color);
            color: var(--navy-blue);
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 99;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            transform: translateY(-5px);
        }

        /* Make sure we load Orbitron font for the clock */
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
    </style>
</head>

<body>
    <!-- Top Contact Bar -->
    <div class="contact-bar">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <span class="contact-item"><i class="fas fa-phone-alt"></i>+94 112 886 585 - 86</span>
                    <span class="contact-item"><i class="fas fa-envelope"></i>info@ssb.gov.lk</span>
                    <span class="contact-item d-none d-md-inline-block"><i class="fas fa-clock"></i>Mon - Fri: 8:30 AM -
                        4:15
                        PM</span>
                </div>
                <div class="col-md-4 d-none d-md-block">
                    <span class="contact-item float-end" id="time">
                        <i class="fas fa-clock"></i>
                    </span>
                    <script>
                        const dateElement = document.getElementById('time');
                        setInterval(() => {
                            const now = new Date();
                            const time = now.toLocaleTimeString();
                            dateElement.innerHTML = '<i class="fas fa-clock"></i> ' + time;
                        }, 1000);
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header with Logo -->
    <header class="main-header fade-in">
        <div class="container">
            <div class="row align-items-center d-flex d-lg-block">

                <!-- Logo (Center - 40%) -->
                <div class="col-lg-12 text-center logo-section d-lg-block d-flex align-items-center justify-content-center mx-auto"
                    style="flex: 0 0 40%; max-width: 50%; align-self: center;">
                    <img src="assets/images/ssbwithtxt.png" alt="Sri Lanka Social Security Board Logo"
                        class="img-fluid">
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg slide-down">
        <div class="container position-relative">
            <!-- Mobile Brand and Toggle -->
            <div class="d-flex justify-content-between align-items-center w-100 d-lg-none">
                <a class="navbar-brand text-white text-sm-start" href="#">Home</a>
                <div class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="desktop-nav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">Home</a>
                    </li>

                    <li class="nav-item dropdown d-lg-block">
                        <a class="nav-link dropdown-toggle" href="#" role="button">
                            About
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="vision_mission.html">Vision & Mission</a></li>
                            <li><a class="dropdown-item" href="gov_leaders.html">VIP</a></li>
                            <li><a class="dropdown-item" href="board_of_directors.html">Board of Directors</a></li>
                            <li><a class="dropdown-item" href="management_team.html">Management Team</a></li>
                            <li><a class="dropdown-item" href="social_security_div.html">Social Security Division</a>
                            </li>
                            <li><a class="dropdown-item" href="admin_div.html">Administration Division</a></li>
                            <li><a class="dropdown-item" href="finance_div.html">Finance Division</a></li>
                            <li><a class="dropdown-item" href="internal_audit.html">Internal Audit Division</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button">
                            Services
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="services.html">Progress</a></li>
                            <li><a class="dropdown-item" href="scheme.html">Schemes</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="application.html">Downloads</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link active" href="gallery.php">Gallery</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="right_to_informarion.html">Right To Information</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="news.html">News</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="careers.html">Careers</a>
                    </li>
                </ul>
            </div>

            <!-- Search Section (Desktop only) -->
            <div class="utility-section d-none d-lg-flex">
                <div class="search-container">
                    <button class="search-btn" onclick="toggleSearch()">
                        <i class="fas fa-search"></i>
                    </button>
                    <div class="search-form" id="search-form">
                        <input type="text" class="search-input" id="search-input" placeholder="Search..."
                            autocomplete="off">
                        <div class="search-results" id="search-results"></div>
                    </div>
                </div>
            </div>


            <div class="dropdown lang-selector d-none d-lg-block">
                <button class="btn dropdown-toggle" type="button" id="desktopLangDropdown" data-bs-toggle="dropdown">
                    <i class="fas fa-globe me-1"></i> English
                </button>
            </div>

        </div>
    </nav>
    <br>
    <!--Banner -->
    <div class="theme">
        <div class="theme-overlay"></div>
        <div class="theme-particles" id="particles"></div>
        <div class="silhouette"></div>
        <div class="theme-content">
            <h1 class="theme-title"><?php echo htmlspecialchars($event['title']); ?> <br> <span style="font-size: 0.5em;"><?php echo htmlspecialchars($event['event_date']); ?></span></h1>
        </div>
    </div><br>
    <!-- Content -->
    <div class="container">
        <div class="containerr">
            <?php if (!empty($event['description'])): ?>
                <div class="modern-box">
                    <p style="text-align: justify;"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                </div>
            <?php endif; ?>
        </div>
        <br>
        <div class="containerr"
            <!-- Modern Gallery -->
            <div class="modern-gallery">
                <div class="gallery-grid">
                    <?php
                    // Fetch Event Images
                    $img_stmt = $conn->prepare("SELECT id, event_id, image_path FROM event_images WHERE event_id = ?");
                    $img_stmt->bind_param("i", $event_id);
                    $img_stmt->execute();
                    $img_stmt->bind_result($img_id, $img_event_id, $img_path);
                    $images = [];
                    while ($img_stmt->fetch()) {
                        $images[] = [
                            'id' => $img_id,
                            'event_id' => $img_event_id,
                            'image_path' => $img_path
                        ];
                    }
                    $img_stmt->close();

                    if (count($images) > 0) {
                        $index = 0;
                        foreach ($images as $img) {
                            echo '<figure class="gallery-item" data-index="' . $index . '" title="' . htmlspecialchars($event['title']) . '">';
                            echo '<img src="' . htmlspecialchars($img['image_path']) . '" alt="' . htmlspecialchars($event['title']) . ' Image ' . ($index + 1) . '">';
                            echo '</figure>';
                            $index++;
                        }
                    } else {
                        // Display placeholder or message if no images
                        echo '<p>No images available for this event yet.</p>';
                    }
                    ?>
                </div>
            </div>

            <!-- Bootstrap Modal Lightbox -->
            <div class="modal fade modal-gallery" id="galleryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body position-relative">
                            <div class="modal-controls">
                                <button type="button" class="btn-prev" aria-label="Previous"><i
                                        class="fas fa-chevron-left"></i></button>
                                <button type="button" class="btn-next" aria-label="Next"><i
                                        class="fas fa-chevron-right"></i></button>
                            </div>
                            <img id="modalImage" src="" alt="Expanded image">
                        </div>
                        <div class="modal-caption" id="modalCaption"></div>
                    </div>
                </div>
            </div>
        </div>
    </div><br><br>
    <footer class="footer">
        <!-- Footer Top Area -->
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <!-- About Widget -->
                    <div class="col-lg-3 col-md-6 footer-widget">
                        <img src="assets/images/logo.jpg" alt="Sri Lanka Social Security Board Logo"
                            class="footer-logo" style="width: 80px; height:200px;">
                        <p class="text mb-4" style="text-align: justify;">The Sri Lanka Social Security Board is
                            dedicated to
                            ensuring social protection and welfare for all citizens through sustainable and innovative
                            social security
                            programs.</p>
                        <div class="social-links">
                            <a href="https://web.facebook.com/ssb.gov.lk?mibextid=ZbWKwL&_rdc=1&_rdr#"
                                target="_blank"><i class="fab fa-facebook-f"></i></a>
                            <a href="mailto:info@ssb.gov.lk" target="_blank"><i class="fas fa-envelope"></i></a>
                            <a href="https://www.youtube.com/@slsosebo" target="_blank"><i
                                    class="fab fa-youtube"></i></a>
                            <a href="https://www.instagram.com/sri_lanka_social_security_boar/" target="_blank"><i
                                    class="fab fa-instagram"></i></a>
                        </div>
                    </div>

                    <!-- Quick Links Widget -->
                    <div class="col-lg-3 col-md-6 footer-widget">
                        <h5>Quick Links</h5>
                        <ul class="footer-links">
                            <li><a href="about.html">About Us</a></li>
                            <li><a href="scheme.html">Our Services</a></li>
                            <li><a href="scheme.html#surekuma-scheme">Surekuma Program</a></li>
                            <li><a href="scheme.html#arassawa-scheme">Arassawa Program</a></li>
                            <li><a href="scheme.html#manusavi-scheme">Vigamanika Program</a></li>
                            <li><a href="news.html">News & Events</a></li>
                        </ul>
                    </div>

                    <!-- Useful Links Widget -->
                    <div class="col-lg-3 col-md-6 footer-widget">
                        <h5>Useful Links</h5>
                        <ul class="footer-links">
                            <li><a href="right_to_informarion.html">Right To Information</a></li>
                            <li><a href="careers.html">Careers</a></li>
                            <li><a href="about.html">FAQ</a></li>
                            <!-- <li><a href="#">Privacy Policy</a></li> -->
                            <li><a href="scheme.html">Terms of Service</a></li>
                            <li><a href="contact.html">Site Map</a></li>
                            <li><a href="https://stateminsamurdhi.gov.lk/" target="_blank">Ministry Website</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info Widget -->
                    <div class="col-lg-3 col-md-6 footer-widget">
                        <h5>Contact Us</h5>
                        <div class="contact-info">
                            <p><i class="fas fa-map-marker-alt"></i> No.18, "Samaja Arakshan Piyasa" <br>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Rajagiriya Road, <br>
                                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Rajagiriya.</p>
                            <p><i class="fas fa-phone-alt"></i> +94 112 886 585 - 86</p>
                            <p><i class="fas fa-envelope"></i> info@ssb.gov.lk</p>
                            <p><i class="fas fa-clock"></i> Mon - Fri: 8:30 AM - 4:15 PM</p>
                        </div>
                        <div class="newsletter-form">
                            <a href="https://cleansrilanka.gov.lk/" target="_blank" rel="noopener noreferrer">
                                <img src="assets/images/Clean Sri Lanka Logo.png" alt="NewsLetter Image"
                                    class="img-thumbnail" style="width: 200px; height: auto; margin-top: 10px;"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Area -->
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="copyright">
                            Copyright © 2025 Sri Lanka Social Security Board
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="powered-by">
                            Powered by Sri Lanka Social Security Board
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="made-with">
                            Made with <i class="fas fa-heart"></i> by Department of Information Technology, Faculty of
                            Humanities &
                            Social Sciences, University of Ruhuna
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- chat bot -->
        <script id="messenger-widget-b" src="https://cdn.botpenguin.com/website-bot.js"
            defer>
            68 ef1a6c163de854554fecda, 68 ef19b7cb64f686dfb5b548
        </script>
        <!-- <script src="//code.tidio.co/suydaosyuma05gayu5j175mdvusvk0bh.js" async></script> -->
        <!-- Back to Top Button -->
        <div class="back-to-top">
            <i class="fas fa-arrow-up"></i>
        </div>


    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Gallery Modal Lightbox Functionality
        (function() {
            const figures = Array.from(document.querySelectorAll('.gallery-item'));
            if (figures.length === 0) return;

            const images = figures.map(f => f.querySelector('img').src);
            const captions = figures.map(f => f.querySelector('.gallery-caption')?.textContent || f.querySelector('img').alt || '');
            const modalEl = document.getElementById('galleryModal');
            const modalInstance = new bootstrap.Modal(modalEl);
            const modalImage = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            let current = 0;

            function open(index) {
                current = index;
                modalImage.src = images[current];
                modalCaption.textContent = captions[current] || '';
                modalInstance.show();
            }

            function showNext(step = 1) {
                current = (current + step + images.length) % images.length;
                modalImage.src = images[current];
                modalCaption.textContent = captions[current] || '';
            }

            figures.forEach((fig, i) => {
                fig.addEventListener('click', () => open(i));
                // keyboard accessibility
                fig.setAttribute('tabindex', '0');
                fig.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        open(i);
                    }
                });
            });

            modalEl.querySelector('.btn-next').addEventListener('click', () => showNext(1));
            modalEl.querySelector('.btn-prev').addEventListener('click', () => showNext(-1));

            // keyboard navigation inside modal
            document.addEventListener('keydown', (e) => {
                if (!document.querySelector('.modal.show')) return;
                if (e.key === 'ArrowRight') showNext(1);
                if (e.key === 'ArrowLeft') showNext(-1);
                if (e.key === 'Escape') modalInstance.hide();
            });
        })();

        // Adding functionality for staggered animation of elements
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.section-container');

            sections.forEach((section, index) => {
                section.style.animationDelay = (0.2 + (index * 0.2)) + 's';
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile menu toggle
            const mobileToggle = document.querySelector('.mobile-menu-toggle');
            const desktopNav = document.querySelector('.desktop-nav');

            if (mobileToggle && desktopNav) {
                mobileToggle.addEventListener('click', function() {
                    desktopNav.classList.toggle('show');
                    this.innerHTML = desktopNav.classList.contains('show') ?
                        '<i class="fas fa-times"></i>' :
                        '<i class="fas fa-bars"></i>';
                });
            }

            // Handle dropdowns on mobile
            if (window.innerWidth < 992) {
                const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

                dropdownToggles.forEach(toggle => {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const dropdownMenu = this.nextElementSibling;
                        dropdownMenu.classList.toggle('show');
                    });
                });
            }

            // Search button animation
            const searchBtn = document.querySelector('.search-btn');
            let isSearchOpen = false;

            if (searchBtn) {
                searchBtn.addEventListener('click', function() {
                    if (!isSearchOpen) {
                        this.innerHTML = '<i class="fas fa-times"></i>';
                        isSearchOpen = true;
                        // Here you would typically show a search form
                    } else {
                        this.innerHTML = '<i class="fas fa-search"></i>';
                        isSearchOpen = false;
                        // Here you would typically hide the search form
                    }
                });
            }

            // Highlight active menu item
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    navLinks.forEach(item => item.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });

        function changeLanguage(language) {
            document.getElementById('desktopLangDropdown').innerHTML = '<i class="fas fa-globe me-1"></i> ' + language;
            document.getElementById('mobileLangDropdown').innerHTML = '<i class="fas fa-globe me-1"></i> ' + language;
        }
        document.addEventListener('DOMContentLoaded', function() {
            // Back to top button functionality
            const backToTopBtn = document.querySelector('.back-to-top');

            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.add('visible');
                } else {
                    backToTopBtn.classList.remove('visible');
                }
            });

            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }

            // Newsletter form animation
            const newsletterForm = document.querySelector('.newsletter-form');

            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const input = this.querySelector('input');
                    const button = this.querySelector('button');

                    if (input.value.trim() !== '') {
                        button.innerHTML = '<i class="fas fa-check"></i>';
                        setTimeout(() => {
                            button.innerHTML = 'Subscribe';
                            input.value = '';
                        }, 2000);
                    }
                });
            }
        });

        const searchData = [{
                title: "Home Page",
                url: "index.html"
            },
            {
                title: "About Us",
                url: "about.html"
            },
            {
                title: "Services",
                url: "services.html"
            },
            {
                title: "Contact Us",
                url: "contact.html"
            },
            {
                title: "Gallery",
                url: "gallery.php"
            }
        ];

        const searchForm = document.getElementById("search-form");
        const searchInput = document.getElementById("search-input");
        const searchResults = document.getElementById("search-results");

        // Show/hide search form
        function toggleSearch() {
            if (searchForm) {
                searchForm.classList.toggle("active");
                if (searchForm.classList.contains("active")) {
                    searchInput.focus();
                }
            }
        }

        // Live search function
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                const query = this.value.toLowerCase();
                searchResults.innerHTML = "";

                if (query.trim() === "") return;

                const filtered = searchData.filter(item =>
                    item.title.toLowerCase().includes(query)
                );

                if (filtered.length === 0) {
                    searchResults.innerHTML = `<div class="no-results">No results found</div>`;
                    return;
                }

                filtered.forEach(item => {
                    const link = document.createElement("a");
                    link.classList.add("search-result-item");
                    link.textContent = item.title;
                    link.href = item.url; // ✅ clickable link
                    searchResults.appendChild(link);
                });
            });
        }

        // Close search form when clicking outside
        document.addEventListener("click", function(e) {
            if (searchForm && !searchForm.contains(e.target) && !e.target.closest(".search-btn")) {
                searchForm.classList.remove("active");
            }
        });
    </script>

</body>

</html>