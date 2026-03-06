<?php
session_start();

// Define BASE_URL for root-level files
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$base = preg_replace('#/usine_industriel/.*#', '/usine_industriel/', $script);
define('BASE_URL', $protocol . '://' . $host . $base);

if (!isset($_SESSION['user'])) {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

$role = $_SESSION['user']['role'];

switch ($role) {
    case 'admin':
        header('Location: ' . BASE_URL . 'dashboard/admin_dashboard.php');
        break;
    case 'manager':
        header('Location: ' . BASE_URL . 'dashboard/manager_dashboard.php');
        break;
    case 'employe':
        header('Location: ' . BASE_URL . 'dashboard/employe_dashboard.php');
        break;
    case 'client':
        header('Location: ' . BASE_URL . 'dashboard/client_dashboard.php');
        break;
    default:
        session_destroy();
        header('Location: ' . BASE_URL . 'auth/login.php');
        break;
}
exit;
