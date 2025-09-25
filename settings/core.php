<?php
// Settings/core.php

// Output buffering for header redirection
ob_start();

// -----------------------------------------
// Session configuration and helpers
// -----------------------------------------

// Configure secure cookie params BEFORE starting session
$secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => $secure,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Session lifetime controls
define('SESSION_IDLE_TIMEOUT', 1800); // 30 minutes
define('SESSION_ABSOLUTE_LIFETIME', 86400); // 24 hours

// Role constants (align with application semantics)
// 1 = admin, 2 = customer, 3 = owner
if (!defined('ROLE_ADMIN')) define('ROLE_ADMIN', 1);

function is_logged_in(): bool
{
    if (!isset($_SESSION)) return false;

    // Idle timeout enforcement
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_IDLE_TIMEOUT)) {
        logout_session();
        return false;
    }

    // Absolute lifetime enforcement
    if (isset($_SESSION['created_at']) && (time() - $_SESSION['created_at'] > SESSION_ABSOLUTE_LIFETIME)) {
        logout_session();
        return false;
    }

    // Update last activity timestamp when active
    if (isset($_SESSION['id']) || isset($_SESSION['user_id']) || isset($_SESSION['customer_id'])) {
        $_SESSION['last_activity'] = time();
        return true;
    }
    return false;
}

function has_role($requiredRole): bool
{
    if (!is_logged_in()) return false;
    if (!isset($_SESSION['user_role'])) return false;
    return intval($_SESSION['user_role']) === intval($requiredRole);
}

function is_admin(): bool
{
    return has_role(ROLE_ADMIN);
}

function require_login(): void
{
    if (is_logged_in()) return;

    $expectsJson = (
        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    );

    if ($expectsJson) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
    } else {
        header('Location: ../login/login.php');
    }
    exit;
}

function require_role($requiredRole): void
{
    if (has_role($requiredRole)) return;

    $expectsJson = (
        (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
    );

    if ($expectsJson) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Insufficient privileges']);
    } else {
        http_response_code(403);
        echo 'Forbidden: insufficient privileges';
    }
    exit;
}

function logout_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}

?>