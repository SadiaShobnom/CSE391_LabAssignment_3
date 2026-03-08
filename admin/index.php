<?php
session_start();
require_once __DIR__ . '/../db.php';

// ─── AUTHENTICATION ───────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && $password === $admin['password']) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username']  = $admin['username'];
        header("Location: index.php");
        exit;
    } else {
        $loginError = "Invalid username or password.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (empty($_SESSION['admin_logged_in'])) {
    $loginError = $loginError ?? '';
    require __DIR__ . '/views/login.php';
    exit;
}

// ─── ROUTE TAB ────────────────────────────────────────────────────────────────

$tab = $_GET['tab'] ?? 'dashboard';

// ─── POST ACTIONS ─────────────────────────────────────────────────────────────

require __DIR__ . '/actions.php';

// ─── DATA FETCHING ────────────────────────────────────────────────────────────

require __DIR__ . '/data.php';

// ─── RENDER ───────────────────────────────────────────────────────────────────

require __DIR__ . '/views/layout.php';
