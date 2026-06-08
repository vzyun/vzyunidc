<?php
/**
 * vzyunIDC - 系统配置文件
 * 复制本文件为 config.php 并填写实际配置
 */

// 错误报告（生产环境请关闭）
error_reporting(E_ALL);
ini_set('display_errors', '0');

// 时区设置
date_default_timezone_set('Asia/Shanghai');

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'vzyunidc');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_PREFIX', '');

// 站点配置
define('SITE_URL', 'http://localhost');      // 站点地址（末尾不加/）
define('SITE_NAME', 'vzyunIDC');
define('SITE_KEYWORDS', 'IDC,云服务器,VPS,主机,财务系统');
define('SITE_DESCRIPTION', '专业IDC业务管理系统');
define('SITE_ICP', '');                     // 备案号

// 安全配置
define('AUTH_KEY', '请修改为随机字符串');     // 加密密钥（重要！务必修改）
define('TOKEN_EXPIRE', 86400);              // Token过期时间（秒）默认24小时
define('ADMIN_PATH', 'admin');              // 后台路径

// Session配置
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}

// 分页配置
define('PAGE_SIZE', 20);

// 上传配置
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('UPLOAD_MAX_SIZE', 5242880);        // 5MB
define('UPLOAD_ALLOW_TYPES', 'jpg,jpeg,png,gif,ico,zip,rar,doc,docx,pdf');

// 缓存配置
define('CACHE_ENABLED', false);
define('CACHE_PATH', __DIR__ . '/../cache/');

// 默认主题
define('DEFAULT_THEME', 'default');

// 数据库连接
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            $db = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('数据库连接失败: ' . $e->getMessage());
        }
    }
    return $db;
}
