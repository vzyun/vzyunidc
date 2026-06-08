<?php
/**
 * vzyunIDC - 管理后台配置
 * 从父目录加载核心文件
 */

// 加载项目根目录配置
$rootDir = dirname(__DIR__);
define('IN_VZYUNIDC', true);
require_once $rootDir . '/includes/config.php';

// 启动Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 加载核心库
require_once $rootDir . '/includes/db.php';
require_once $rootDir . '/includes/auth.php';
require_once $rootDir . '/includes/functions.php';
require_once $rootDir . '/includes/hook.php';
require_once $rootDir . '/includes/theme.php';

// 检查管理员登录
$admin = Auth::instance()->checkAdmin();
if (!$admin) {
    header('Location: index.php');
    exit;
}
define('ADMIN_ID', $admin['id']);
