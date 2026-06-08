<?php
/**
 * vzyunIDC - 系统初始化
 */

// Session启动
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 错误处理
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) return;
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// 加载核心类
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/hook.php';
require_once __DIR__ . '/theme.php';

// 初始化主题引擎
$theme = Theme::instance();

// 全局变量
$currentUser = null;
$currentAdmin = null;

// 尝试自动登录
try {
    $currentUser = Auth::instance()->checkUser();
} catch (Exception $e) {
    // 数据库未初始化时不报错
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
define('CSRF_TOKEN', $_SESSION['csrf_token']);

/**
 * 验证CSRF Token
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . CSRF_TOKEN . '">';
}

function verifyCsrf() {
    $token = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!hash_equals(CSRF_TOKEN, $token)) {
        jsonError('CSRF Token验证失败');
    }
}
