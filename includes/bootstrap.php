<?php
/**
 * Core bootstrap: starts the session, loads DB, and defines shared helpers.
 * Every page includes this first.
 */
session_start();

require_once __DIR__ . '/../config/db.php';

/* ---------- Path / URL helpers ---------- */

// Base URL of the app (folder under the web root). Auto-detected.
function base_url() {
    static $base = null;
    if ($base === null) {
        $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        // index.php lives at project root, so dirname of any page is the root.
        // Normalise trailing slash.
        $base = rtrim($script, '/');
    }
    return $base;
}

// Build a route URL handled by index.php front controller.
function url($page, $params = []) {
    $q = array_merge(['page' => $page], $params);
    return base_url() . '/index.php?' . http_build_query($q);
}

// Asset (css/js/images bundled with the app).
function asset($path) {
    return base_url() . '/assets/' . ltrim($path, '/');
}

// Uploaded media (product / category images).
function media($path) {
    return base_url() . '/uploads/' . ltrim($path, '/');
}

/* ---------- Output helpers ---------- */

function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function rupees($amount) {
    return '₹' . number_format((float)$amount, 2);
}

function redirect($page, $params = []) {
    header('Location: ' . url($page, $params));
    exit;
}

/* ---------- Auth helpers ---------- */

function current_user() {
    if (empty($_SESSION['user_id'])) return null;
    static $cache = null;
    if ($cache === null) {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $cache = $stmt->fetch() ?: null;
    }
    return $cache;
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash'] = 'Please log in to continue.';
        redirect('login');
    }
}

/* ---------- CSRF protection ---------- */

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function check_csrf() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'])) {
            http_response_code(400);
            die('Invalid CSRF token. Go back and try again.');
        }
    }
}

/* ---------- Cart helpers ---------- */

function cart_count() {
    if (!is_logged_in()) return 0;
    $stmt = db()->prepare('SELECT COALESCE(SUM(quantity),0) FROM cart_items WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return (int)$stmt->fetchColumn();
}

/* ---------- Compare list helpers (session based, no DB changes) ---------- */

const COMPARE_MAX = 4;

function compare_ids() {
    return $_SESSION['compare'] ?? [];
}

function compare_count() {
    return count(compare_ids());
}

function in_compare($id) {
    return in_array((int)$id, compare_ids(), true);
}

// Toggle a product in/out of the compare list. Returns the new state.
function compare_toggle($id) {
    $id = (int)$id;
    $list = compare_ids();
    if (($k = array_search($id, $list, true)) !== false) {
        unset($list[$k]);
    } elseif (count($list) < COMPARE_MAX) {
        $list[] = $id;
    }
    $_SESSION['compare'] = array_values($list);
    return in_compare($id);
}
