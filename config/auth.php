<?php
// config/auth.php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUserRole() {
    return $_SESSION['role'] ?? null;
}

function isRole($role) {
    return isLoggedIn() && $_SESSION['role'] === $role;
}

function requireRole($role) {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
    if ($_SESSION['role'] !== $role && $_SESSION['role'] !== 'admin') {
        header('Location: ../index.php');
        exit;
    }
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}
?>