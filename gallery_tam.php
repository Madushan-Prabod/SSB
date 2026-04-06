<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gallery - Social Security Board</title>
  <link rel="shortcut icon" href="./assets/images/SSB Logo(Color) .png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
  <!-- tamil fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Tamil:wght@100..900&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+Tamil:wght@100..900&family=Noto+Serif+Sinhala:wght@100..900&display=swap"
    rel="stylesheet">
  <style>
    :root {
      --navy-blue: #0a192f;
      --navy-blue-light: #112240;
      --accent-color: #64ffda;
      --text-color: #e6f1ff;
    }

    body {
      font-family: 'Poppins', sans-serif, 'Noto Sans Tamil';
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
      font-size: 13px;
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


    /* Footer Styles */
    .footer {
      background-color: var(--navy-blue);
      color: var(--text-color);
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif, 'Noto Sans Tamil';
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
      color: var(--text-muted);
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
      color: var(--text-muted);
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
    }

    .social-links a:hover {
      background-color: var(--accent-color);
      color: var(--navy-blue);
      transform: translateY(-3px);
    }

    .newsletter-form {
      margin-top: 20px;
    }

    .newsletter-form .input-group {
      border-radius: 50px;
      overflow: hidden;
      background-color: rgba(255, 255, 255, 0.05);
      padding: 3px;
    }

    .newsletter-form input {
      background-color: transparent;
      border: none;
      padding: 10px 15px;
      color: var(--text-color);
      font-size: 14px;
    }

    .newsletter-form input:focus {
      box-shadow: none;
      background-color: transparent;
      color: var(--text-color);
    }

    .newsletter-form button {
      background-color: var(--accent-color);
      border: none;
      color: var(--navy-blue);
      border-radius: 50px;
      padding: 8px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .newsletter-form button:hover {
      background-color: #55d9bb;
      transform: translateX(3px);
    }

    .footer-bottom {
      padding: 20px 0;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
      font-size: 14px;
    }

    .copyright {
      color: var(--text-muted);
    }

    .powered-by {
      color: var(--text-muted);
      text-align: center;
    }

    .made-with {
      text-align: right;
      color: var(--text-muted);
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
      bottom: 100px;
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

    @media (max-width: 767.98px) {
      .footer-widget {
        margin-bottom: 30px;
      }

      .footer-bottom .row div {
        text-align: center !important;
        margin-bottom: 10px;
      }
    }

    .gallery-section {
      margin: 40px 0;
    }

    .section-title {
      text-align: center;
      margin-bottom: 30px;
      position: relative;
    }

    .section-title h2 {
      font-size: 32px;
      font-weight: 500;
    }

    .section-title p {
      font-size: 14px;
      color: #888;
      margin-top: 5px;
    }

    .gallery-container {
      width: 100%;
      overflow-x: auto;
      padding-bottom: 15px;
      /* Space for scrollbar */
      position: relative;
    }

    /* Visible scrollbar styling */
    .gallery-container::-webkit-scrollbar {
      height: 8px;
      display: block;
    }

    .gallery-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .gallery-container::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }

    .gallery-container::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    /* For Firefox */
    .gallery-container {
      scrollbar-width: thin;
      scrollbar-color: #888 #f1f1f1;
    }

    .gallery-row {
      display: flex;
      padding: 0 40px;
      min-width: max-content;
    }

    .gallery-item {
      position: relative;
      margin-right: 20px;
      flex: 0 0 auto;
      overflow: hidden;
      border-radius: 4px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
      width: 350px;
      height: 250px;
    }

    .gallery-item.wide {
      width: 500px;
    }

    .gallery-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .gallery-item-bg {
      width: 100%;
      height: 100%;
      background-color: #ddd;
      position: relative;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #666;
      font-size: 14px;
    }

    .gallery-item-bg img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0;
      left: 0;
    }

    .gallery-item-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 20px;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
      color: white;
      opacity: 0;
      transition: opacity 0.3s;
    }

    .gallery-item:hover .gallery-item-overlay {
      opacity: 1;
    }

    .category-label {
      position: absolute;
      top: 15px;
      right: 15px;
      background-color: rgba(255, 255, 255, 0.8);
      color: #333;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
      z-index: 10;
    }



    .heart {
      color: #ff5555;
    }

    /* Animation for gallery items */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .gallery-item {
      animation: fadeInUp 0.6s forwards;
      opacity: 0;
    }

    /* Different delays for items */
    .gallery-item:nth-child(2) {
      animation-delay: 0.1s;
    }

    .gallery-item:nth-child(3) {
      animation-delay: 0.2s;
    }

    .gallery-item:nth-child(4) {
      animation-delay: 0.3s;
    }

    .gallery-item:nth-child(5) {
      animation-delay: 0.4s;
    }

    /* Color themes for different sections */
    .events-bg {
      background-color: rgba(100, 50, 150, 0.1);
    }

    .meetings-bg {
      background-color: rgba(50, 100, 150, 0.1);
    }

    .awards-bg {
      background-color: rgba(150, 100, 50, 0.1);
    }

    .projects-bg {
      background-color: rgba(50, 150, 100, 0.1);
    }

    /* Background colors for placeholder images */
    .bg-1 {
      background: linear-gradient(135deg, #6a11cb, #2575fc);
    }

    .bg-2 {
      background: linear-gradient(135deg, #ff758c, #ff7eb3);
    }

    .bg-3 {
      background: linear-gradient(135deg, #4facfe, #00f2fe);
    }

    .bg-4 {
      background: linear-gradient(135deg, #fdcb6e, #e17055);
    }

    .bg-5 {
      background: linear-gradient(135deg, #a29bfe, #6c5ce7);
    }

    .bg-6 {
      background: linear-gradient(135deg, #0ba360, #3cba92);
    }

    /* Filter navigation */
    .filter-nav {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 15px;
      padding: 10px;
      margin: 0;
    }

    .filter-btn {
      border: none;
      background: none;
      color: #fdfbfb;
      font-size: 15px;
      font-weight: 500;
      padding: 5px 12px;
      cursor: pointer;
      position: relative;
      transition: all 0.3s ease;
      white-space: nowrap;
    }

    .filter-btn::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: 0;
      left: 50%;
      background-color: var(--accent-color);
      transition: all 0.3s ease;
      transform: translateX(-50%);
    }

    .filter-btn:hover,
    .filter-btn.active {
      color: var(--accent-color);
    }

    .filter-btn:hover::after,
    .filter-btn.active::after {
      width: 80%;
    }

    /* Scroll indicators */
    .scroll-indicator {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      color: #888;
      font-size: 12px;
      opacity: 0.7;
      background-color: rgba(255, 255, 255, 0.7);
      padding: 3px 10px;
      border-radius: 10px;
      pointer-events: none;
    }

    .box {
      background-color: #112240;
      border-bottom: 1px solid rgba(100, 255, 218, 0.1);
      min-height: 60px;
      height: auto;
      display: flex;
      align-items: center;
      justify-content: center;
      /* position: sticky; */
      top: 74px;
      /* Height of navbar approx */
      z-index: 999;
      backdrop-filter: blur(10px);
    }

    @media (max-width: 768px) {
      .filter-nav {
        gap: 10px;
      }

      .filter-btn {
        font-size: 13px;
        padding: 5px 8px;
      }

      .box {
        top: 0;
        /* Adjust if navbar collapses or behaves differently */
      }
    }

    @media (max-width: 480px) {
      .filter-nav {
        gap: 5px;
        justify-content: flex-start;
        overflow-x: auto;
        flex-wrap: nowrap;
        width: 100%;
        padding: 10px 15px;
        -webkit-overflow-scrolling: touch;
      }

      .box {
        display: block;
        padding: 0;
      }

      .filter-nav::-webkit-scrollbar {
        height: 0;
        width: 0;
        display: none;
      }
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
      font-size: 4rem;
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

    .digital-clock-container {
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }

    .digital-clock {
      background: rgba(10, 25, 47, 0.8);
      border-radius: 8px;
      padding: 10px 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2),
        inset 0 0 15px rgba(100, 255, 218, 0.1);
      border: 1px solid rgba(100, 255, 218, 0.3);
      color: var(--accent-color);
      font-family: 'Orbitron', sans-serif;
      line-height: 1.2;
      min-width: 200px;
    }

    .time-section {
      font-size: 1.6rem;
      font-weight: 700;
      text-align: center;
      text-shadow: 0 0 10px rgba(100, 255, 218, 0.6);
      letter-spacing: 2px;
      margin-bottom: 2px;
    }

    .date-section {
      font-size: 0.85rem;
      text-align: center;
      opacity: 0.8;
      letter-spacing: 1px;
    }

    .blink {
      animation: blink 1s infinite;
    }

    #ampm {
      font-size: 0.9rem;
      margin-left: 5px;
      vertical-align: text-top;
      opacity: 0.9;
    }

    @keyframes blink {
      0% {
        opacity: 1;
      }

      50% {
        opacity: 0.3;
      }

      100% {
        opacity: 1;
      }
    }

    @media (max-width: 991px) {
      .digital-clock-container {
        justify-content: center;
        margin-bottom: 15px;
      }

      .row>div {
        flex: 0 0 100% !important;
        max-width: 100% !important;
        text-align: center !important;
        margin-bottom: 15px;
      }

      .digital-clock-container {
        justify-content: center;
      }
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

      .section-title h2 {
        font-size: 24px;
      }

      .section-title p {
        font-size: 14px;
      }
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
          <span class="contact-item d-none d-md-inline-block"><i class="fas fa-clock"></i>Mon - Fri: 8:30 AM - 4:15
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
              dateElement.innerText = time;
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
        <div
          class="col-lg-12 text-center logo-section d-lg-block d-flex align-items-center justify-content-center mx-auto"
          style="flex: 0 0 40%; max-width: 50%; align-self: center;">
          <img src="assets/images/ssbwithtxt.png" alt="Sri Lanka Social Security Board Logo" class="img-fluid">
        </div>
      </div>
    </div>
  </header>

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg slide-down">
    <div class="container position-relative">
      <!-- Mobile Brand and Toggle -->
      <div class="d-flex justify-content-between align-items-center w-100 d-lg-none">
        <a class="navbar-brand text-white text-sm-start" href="#">
          தொகுப்பு</a>
        <div class="mobile-menu-toggle">
          <i class="fas fa-bars"></i>
        </div>
      </div>

      <!-- Desktop Navigation -->
      <div class="desktop-nav">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item">
            <a class="nav-link" href="index_tam.html">
              முகப்பு</a>
          </li>

          <li class="nav-item dropdown d-lg-block">
            <a class="nav-link dropdown-toggle" href="#" role="button">
              பற்றி
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="vision_mission_tam.html">தொலைநோக்கு மற்றும் பணி</a></li>
              <li><a class="dropdown-item" href="gov_leaders_tam.html">அரசாங்க தலைவர்கள்</a></li>
              <li><a class="dropdown-item" href="board_of_directors_tam.html">இயக்குனர்கள் குழு</a></li>
              <li><a class="dropdown-item" href="management_team_tam.html">மேலாண்மை குழு</a></li>
              <li><a class="dropdown-item" href="social_security_div_tam.html">சமூக பாதுகாப்பு பிரிவு</a>
              </li>
              <li><a class="dropdown-item" href="admin_div_tam.html">நிர்வாகப் பிரிவு</a></li>
              <li><a class="dropdown-item" href="finance_div_tam.html">நிதிப் பிரிவு</a></li>
              <li><a class="dropdown-item" href="internal_audit_tam.html">உள் தணிக்கைப் பிரிவு</a></li>
            </ul>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button">
              சேவைகள்
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="services_tam.html">முன்னேற்றம்</a></li>
              <li><a class="dropdown-item" href="scheme_tam.html">திட்டங்கள்</a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="application_tam.html">பதிவிறக்கங்கள்</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="gallery_tam.php">தொகுப்பு</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="right_to_informarion_tam.html">தகவல் உரிமை</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="news_tam.php">செய்தி</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="contact_tam.html">தொடர்பு</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="careers_tam.html">தொழில்கள்</a>
          </li>
        </ul>

        <!-- Search and Language on Mobile -->
        <div class="utility-section d-lg-none">
          <div class="dropdown lang-selector">
            <a class="btn btn-secondary" href="gallery_tam.php">தமிழ்</a>
            <a class="btn btn-secondary" href="gallery.php">English</a>
            <a class="btn btn-secondary" href="gallery_sin.php">සිංහල</a>
          </div>
        </div>
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
          <i class="fas fa-globe me-1"></i> தமிழ்
        </button>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="./gallery.php">English</a></li>
          <li><a class="dropdown-item" href="./gallery_sin.php">සිංහල</a></li>
          <li><a class="dropdown-item" href="./gallery_tam.php">தமிழ்</a></li>
        </ul>
      </div>

    </div>
  </nav>
  <br>
  <!-- Banner -->
  <div class="theme">
    <div class="theme-overlay"></div>
    <div class="theme-particles" id="particles"></div>
    <div class="silhouette"></div>
    <div class="theme-content">
      <h1 class="theme-title">தொகுப்பு</h1>
    </div>
  </div>

  <div class="box">
    <div class="filter-nav">
      <button class="filter-btn active" data-filter="all">அனைத்து</button>
      <button class="filter-btn" data-filter="events">நிகழ்வுகள்</button>
      <button class="filter-btn" data-filter="meetings">கூட்டங்கள்</button>
      <button class="filter-btn" data-filter="awards">விருதுகள்</button>
      <button class="filter-btn" data-filter="projects">திட்டங்கள்</button>
    </div>
  </div>
  <!-- Events Section -->
  <?php
  // Custom result class to mimic mysqli_result
  class CustomResult
  {
    public $num_rows;
    private $data;
    private $current = 0;

    public function __construct($data)
    {
      $this->data = $data;
      $this->num_rows = count($data);
    }

    public function fetch_assoc()
    {
      if ($this->current < $this->num_rows) {
        return $this->data[$this->current++];
      }
      return null;
    }
  }

  // Fetch items function
  function getGalleryItems($conn, $category)
  {
    $sql = "SELECT id, category, title, description, event_date, cover_image FROM events WHERE category = ? ORDER BY event_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $stmt->bind_result($id, $cat, $title, $desc, $event_date, $cover_image);

    $results = [];
    while ($stmt->fetch()) {
      $results[] = [
        'id' => $id,
        'category' => $cat,
        'title' => $title,
        'description' => $desc,
        'event_date' => $event_date,
        'cover_image' => $cover_image
      ];
    }
    $stmt->close();

    return new CustomResult($results);
  }
  ?>

  <!-- Events Section -->
  <section class="gallery-section" id="events-section">
    <div class="section-title">
      <h2><b>சிறப்பு நிகழ்வுகள்</b></h2>
      <p>சமூக மற்றும் அதிகாரப்பூர்வ பாதுகாப்பு நிகழ்வுகள்</p>
    </div>

    <div class="gallery-container">
      <div class="gallery-row">
        <?php
        $result = getGalleryItems($conn, 'events');
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<a href="view_event.php?id=' . $row['id'] . '">';
            echo '<div class="gallery-item wide">';
            echo '<span class="category-label">' . date('Y', strtotime($row['event_date'])) . '</span>';
            echo '<div class="gallery-item-bg bg-1">';
            echo '<img src="' . $row['cover_image'] . '">';
            echo '</div>';
            echo '<div class="gallery-item-overlay">';
            echo '<h3>' . $row['title'] . '</h3>';
            echo '<p>' . $row['event_date'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
          }
        } else {
          echo '<p class="text-center text-muted w-100">நிகழ்வுகள் இல்லை.</p>';
        }
        ?>
      </div>
      <div class="scroll-indicator">மேலும் காண கிடைமட்டமாக உருட்டவும் →</div>
    </div>
  </section>

  <!-- Meetings Section -->
  <section class="gallery-section" id="meetings-section">
    <div class="section-title">
      <h2><b>கூட்டங்கள்</b></h2>
      <p>மூலோபாய மற்றும் அதிகாரப்பூர்வ கூட்டங்கள்</p>
    </div>
    <div class="gallery-container">
      <div class="gallery-row">
        <?php
        $result = getGalleryItems($conn, 'meetings');
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<a href="view_event.php?id=' . $row['id'] . '">';
            echo '<div class="gallery-item wide">';
            echo '<span class="category-label">' . date('Y', strtotime($row['event_date'])) . '</span>';
            echo '<div class="gallery-item-bg bg-6">';
            echo '<img src="' . $row['cover_image'] . '">';
            echo '</div>';
            echo '<div class="gallery-item-overlay">';
            echo '<h3>' . $row['title'] . '</h3>';
            echo '<p>' . $row['event_date'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
          }
        } else {
          echo '<p class="text-center text-muted w-100">கூட்டங்கள் இல்லை.</p>';
        }
        ?>
      </div>
      <div class="scroll-indicator">மேலும் காண கிடைமட்டமாக உருட்டவும் →</div>
    </div>
  </section>

  <!-- Awards Section -->
  <section class="gallery-section" id="awards-section">
    <div class="section-title">
      <h2><b>விருதுகள்</b></h2>
      <p>அங்கீகாரம் மற்றும் கொண்டாட்டங்கள்</p>
    </div>

    <div class="gallery-container">
      <div class="gallery-row">
        <?php
        $result = getGalleryItems($conn, 'awards');
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<a href="view_event.php?id=' . $row['id'] . '">';
            echo '<div class="gallery-item wide">';
            echo '<span class="category-label">' . date('Y', strtotime($row['event_date'])) . '</span>';
            echo '<div class="gallery-item-bg bg-3">';
            echo '<img src="' . $row['cover_image'] . '">';
            echo '</div>';
            echo '<div class="gallery-item-overlay">';
            echo '<h3>' . $row['title'] . '</h3>';
            echo '<p>' . $row['event_date'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
          }
        } else {
          echo '<p class="text-center text-muted w-100">விருதுகள் இல்லை.</p>';
        }
        ?>
      </div>
      <div class="scroll-indicator">மேலும் காண கிடைமட்டமாக உருட்டவும் →</div>
    </div>
  </section>

  <!-- Projects Section -->
  <section class="gallery-section" id="projects-section">
    <div class="section-title">
      <h2><b>திட்டங்கள்</b></h2>
      <p>நடைபெற்று வரும் மற்றும் முடிக்கப்பட்ட முயற்சிகள்</p>
    </div>

    <div class="gallery-container">
      <div class="gallery-row">
        <?php
        $result = getGalleryItems($conn, 'projects');
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo '<a href="view_event.php?id=' . $row['id'] . '">';
            echo '<div class="gallery-item wide">';
            echo '<span class="category-label">' . date('Y', strtotime($row['event_date'])) . '</span>';
            echo '<div class="gallery-item-bg bg-4">';
            echo '<img src="' . $row['cover_image'] . '">';
            echo '</div>';
            echo '<div class="gallery-item-overlay">';
            echo '<h3>' . $row['title'] . '</h3>';
            echo '<p>' . $row['event_date'] . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</a>';
          }
        } else {
          echo '<p class="text-center text-muted w-100">திட்டங்கள் இல்லை.</p>';
        }
        ?>
      </div>
      <div class="scroll-indicator">மேலும் காண கிடைமட்டமாக உருட்டவும் →</div>
    </div>
  </section>
  <!-- Footer -->
  <footer class="footer">
    <!-- Footer Top Area -->
    <div class="footer-top">
      <div class="container">
        <div class="row">
          <!-- About Widget -->
          <div class="col-lg-3 col-md-6 footer-widget">
            <img src="./assets/images/logo.jpg" alt="Sri Lanka Social Security Board Logo"
              class="footer-logo" style="width: 80px; height:200px;">
            <p class="text mb-4" style="text-align: justify;"> நிலையான மற்றும் புதுமையான சமூகப் பாதுகாப்புத்
              திட்டங்கள் மூலம் அனைத்து குடிமக்களுக்கும் சமூகப் பாதுகாப்பு மற்றும் நலனை உறுதி செய்வதற்கு
              இலங்கை சமூகப் பாதுகாப்பு வாரியம் அர்ப்பணிக்கப்பட்டுள்ளது.</p>
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
            <h5>விரைவு இணைப்புகள்</h5>
            <ul class="footer-links">
              <li><a href="about.html">எங்களைப் பற்றி</a></li>
              <li><a href="scheme.html">எங்கள் சேவைகள்</a></li>
              <li><a href="scheme.html#surekuma-scheme">சுரெகும திட்டம்</a></li>
              <li><a href="scheme.html#arassawa-scheme">ஆரஸ்ஸாவ திட்டம்</a></li>
              <li><a href="scheme.html#manusavi-scheme">விகமாணிகா திட்டம்</a></li>
              <li><a href="news.php">செய்திகள் & நிகழ்வுகள்</a></li>
            </ul>
          </div>

          <!-- Useful Links Widget -->
          <div class="col-lg-3 col-md-6 footer-widget">
            <h5>பயனுள்ள இணைப்புகள்</h5>
            <ul class="footer-links">
              <li><a href="right_to_informarion.html">தகவல் உரிமை</a></li>
              <li><a href="careers.html">தொழில்கள்</a></li>
              <li><a href="about.html">அடிக்கடி கேட்கப்படும் கேள்விகள்</a></li>
              <!-- <li><a href="#">Privacy Policy</a></li> -->
              <li><a href="scheme.html">சேவை விதிமுறைகள்</a></li>
              <li><a href="contact.html">தள வரைபடம்</a></li>
              <li><a href="https://stateminsamurdhi.gov.lk/" target="_blank">அமைச்சக வலைத்தளம்</a></li>
            </ul>
          </div>

          <!-- Contact Info Widget -->
          <div class="col-lg-3 col-md-6 footer-widget">
            <h5>எங்களைத் தொடர்பு கொள்ளவும்</h5>
            <div class="contact-info">
              <p><i class="fas fa-map-marker-alt"></i>எண்.18, “சமாஜ அரக்‌ஷன் பியாச” <br>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ராஜகிரிய சாலை, <br>
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;ராஜகிரிய.</p>
              <p><i class="fas fa-phone-alt"></i> +94 112 886 585 - 86</p>
              <p><i class="fas fa-envelope"></i> info@ssb.gov.lk</p>
              <p><i class="fas fa-clock"></i> திங்கள் – வெள்ளி: காலை 8:30 – மாலை 4:15</p>
            </div>
            <div class="newsletter-form">
              <a href="https://cleansrilanka.gov.lk/" target="_blank" rel="noopener noreferrer">
                <img src="./assets/images/Clean Sri Lanka Logo.png" alt="NewsLetter Image"
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
    <script id="messenger-widget-b" src="https://cdn.botpenguin.com/website-bot.js" defer>
      68 ef1a6c163de854554fecda, 68 ef19b7cb64f686dfb5b548
    </script>
    <!-- accessibility -->
    <script src="https://app.embed.im/accessibility.js" defer></script>
    <!-- Back to Top Button -->
    <div class="back-to-top">
      <i class="fas fa-arrow-up"></i>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Filter functionality
      const filterBtns = document.querySelectorAll('.filter-btn');
      const sections = document.querySelectorAll('.gallery-section');

      filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
          // Remove active class from all buttons
          filterBtns.forEach(b => b.classList.remove('active'));
          // Add active class to clicked button
          this.classList.add('active');

          const filter = this.getAttribute('data-filter');

          sections.forEach(section => {
            if (filter === 'all') {
              section.style.display = 'block';
              // Reset animations
              const items = section.querySelectorAll('.gallery-item');
              items.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.animation = 'none';
                setTimeout(() => {
                  item.style.animation = `fadeInUp 0.6s forwards ${index * 0.1}s`;
                }, 10);
              });
            } else {
              if (section.id === `${filter}-section`) {
                section.style.display = 'block';
                // Reset animations
                const items = section.querySelectorAll('.gallery-item');
                items.forEach((item, index) => {
                  item.style.opacity = '0';
                  item.style.animation = 'none';
                  setTimeout(() => {
                    item.style.animation = `fadeInUp 0.6s forwards ${index * 0.1}s`;
                  }, 10);
                });
              } else {
                section.style.display = 'none';
              }
            }
          });
        });
      });

      // Handle scrollbar indicators
      const galleryContainers = document.querySelectorAll('.gallery-container');

      galleryContainers.forEach(container => {
        // Check if scrollable
        function checkScroll() {
          const indicator = container.querySelector('.scroll-indicator');
          if (container.scrollWidth > container.clientWidth) {
            indicator.style.display = 'block';
          } else {
            indicator.style.display = 'none';
          }
        }

        // Check initial state
        checkScroll();

        // Check on resize
        window.addEventListener('resize', checkScroll);

        // Hide indicator when scrolled to end
        container.addEventListener('scroll', function() {
          const indicator = this.querySelector('.scroll-indicator');
          if (this.scrollLeft + this.clientWidth >= this.scrollWidth - 20) {
            indicator.style.opacity = '0';
          } else {
            indicator.style.opacity = '0.7';
          }
        });

        // Enable mouse wheel horizontal scrolling
        container.addEventListener('wheel', function(e) {
          if (e.deltaY !== 0) {
            e.preventDefault();
            this.scrollLeft += e.deltaY;
          }
        });
      });

      // Add hover effect for gallery items
      const galleryItems = document.querySelectorAll('.gallery-item');

      galleryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
          const overlay = this.querySelector('.gallery-item-overlay');
          overlay.style.opacity = '1';
        });

        item.addEventListener('mouseleave', function() {
          const overlay = this.querySelector('.gallery-item-overlay');
          overlay.style.opacity = '0';
        });
      });
    });
    document.addEventListener('DOMContentLoaded', function() {
      // Mobile menu toggle
      const mobileToggle = document.querySelector('.mobile-menu-toggle');
      const desktopNav = document.querySelector('.desktop-nav');

      mobileToggle.addEventListener('click', function() {
        desktopNav.classList.toggle('show');
        this.innerHTML = desktopNav.classList.contains('show') ?
          '<i class="fas fa-times"></i>' :
          '<i class="fas fa-bars"></i>';
      });

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

      backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      });

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
    // Enhanced Digital Clock Function
    function updateBeautifulClock() {
      const now = new Date();

      // Time values
      let hours = now.getHours();
      const ampm = hours >= 12 ? 'PM' : 'AM';
      hours = hours % 12;
      hours = hours ? hours : 12; // the hour '0' should be '12'

      const minutes = String(now.getMinutes()).padStart(2, '0');
      const seconds = String(now.getSeconds()).padStart(2, '0');

      // Date values
      const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
      const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

      const day = days[now.getDay()];
      const date = now.getDate();
      const month = months[now.getMonth()];
      const year = now.getFullYear();

      // Update the clock elements
      document.getElementById('hours').textContent = String(hours).padStart(2, '0');
      document.getElementById('minutes').textContent = minutes;
      document.getElementById('seconds').textContent = seconds;
      document.getElementById('ampm').textContent = ampm;

      document.getElementById('day').textContent = day;
      document.getElementById('date').textContent = date;
      document.getElementById('month').textContent = month;
      document.getElementById('year').textContent = year;

      setTimeout(updateBeautifulClock, 1000);
    }

    // Initialize the clock when the page loads
    document.addEventListener('DOMContentLoaded', function() {
      // Load Orbitron font for the clock (fallback in case the @import doesn't work)
      const fontLink = document.createElement('link');
      fontLink.href = 'https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap';
      fontLink.rel = 'stylesheet';
      document.head.appendChild(fontLink);

      // Start the digital clock
      updateBeautifulClock();

      // Rest of your existing DOMContentLoaded code remains here
    });

    const searchData = [{
        title: "Home Page",
        url: "/home"
      },
      {
        title: "About Us",
        url: "/about"
      },
      {
        title: "Services",
        url: "/services"
      },
      {
        title: "Products",
        url: "/products"
      },
      {
        title: "Contact Us",
        url: "/contact"
      },
      {
        title: "Blog",
        url: "/blog"
      },
      {
        title: "Support",
        url: "/support"
      },
      {
        title: "Frequently Asked Questions",
        url: "/faq"
      }
    ];

    const searchForm = document.getElementById("search-form");
    const searchInput = document.getElementById("search-input");
    const searchResults = document.getElementById("search-results");

    // Show/hide search form
    function toggleSearch() {
      searchForm.classList.toggle("active");
      if (searchForm.classList.contains("active")) {
        searchInput.focus();
      }
    }

    // Live search function
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

    // Close search form when clicking outside
    document.addEventListener("click", function(e) {
      if (!searchForm.contains(e.target) && !e.target.closest(".search-btn")) {
        searchForm.classList.remove("active");
      }
    });
  </script>
</body>

</html>
