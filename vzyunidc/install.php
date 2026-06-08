<?php
/**
 * vzyunIDC - 安装向导
 */

// 检测是否已安装
$configFile = __DIR__ . '/includes/config.php';
$installed = file_exists($configFile);

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($step == 2) {
        // 数据库配置
        $host = $_POST['db_host'] ?? 'localhost';
        $port = $_POST['db_port'] ?? '3306';
        $name = $_POST['db_name'] ?? 'vzyunidc';
        $user = $_POST['db_user'] ?? 'root';
        $pass = $_POST['db_pass'] ?? '';
        
        // 测试连接
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            
            // 导入数据库
            $sql = file_get_contents(__DIR__ . '/install/database.sql');
            $pdo->exec($sql);
            
            // 写入配置
            $config = <<<PHP
<?php
define('DB_HOST', '{$host}');
define('DB_PORT', '{$port}');
define('DB_NAME', '{$name}');
define('DB_USER', '{$user}');
define('DB_PASS', '{$pass}');
define('DB_CHARSET', 'utf8mb4');
define('DB_PREFIX', '');
define('SITE_URL', '{$_POST['site_url']}');
define('SITE_NAME', '{$_POST['site_name']}');
define('AUTH_KEY', '{$_POST['auth_key']}');
define('TOKEN_EXPIRE', 86400);
define('ADMIN_PATH', 'admin');
define('PAGE_SIZE', 20);
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('UPLOAD_MAX_SIZE', 5242880);
define('UPLOAD_ALLOW_TYPES', 'jpg,jpeg,png,gif,ico,zip,rar,doc,docx,pdf');
define('CACHE_ENABLED', false);
define('CACHE_PATH', __DIR__ . '/../cache/');
define('DEFAULT_THEME', 'default');
function getDB() {
    static \$db = null;
    if (\$db === null) {
        try {
            \$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            \$db = new PDO(\$dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException \$e) {
            die('数据库连接失败: ' . \$e->getMessage());
        }
    }
    return \$db;
}
PHP;
            file_put_contents($configFile, $config);
            
            $success = '安装成功！';
            $step = 3;
        } catch (Exception $e) {
            $error = '安装失败：' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>vzyunIDC - 安装向导</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .install-box { background: white; border-radius: 16px; padding: 40px; max-width: 600px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .logo { text-align: center; margin-bottom: 32px; }
        .logo h2 { background: linear-gradient(135deg, #2563eb, #0ea5e9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; }
        .step-indicator { display: flex; justify-content: center; gap: 8px; margin-bottom: 32px; }
        .step-dot { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; background: #e2e8f0; color: #94a3b8; }
        .step-dot.active { background: #2563eb; color: white; }
        .step-dot.done { background: #10b981; color: white; }
        .step-line { width: 40px; height: 2px; background: #e2e8f0; align-self: center; }
        .step-line.done { background: #10b981; }
        .form-label { font-weight: 500; font-size: 14px; }
        .btn-primary { background: linear-gradient(135deg, #2563eb, #0ea5e9); border: none; padding: 12px 32px; font-size: 16px; }
    </style>
</head>
<body>
<div class="install-box">
    <div class="logo"><h2>vzyunIDC 安装向导</h2></div>
    
    <div class="step-indicator">
        <div class="step-dot <?= $step >= 2 ? 'done' : ($step == 1 ? 'active' : '') ?>">1</div>
        <div class="step-line <?= $step >= 2 ? 'done' : '' ?>"></div>
        <div class="step-dot <?= $step >= 3 ? 'done' : ($step == 2 ? 'active' : '') ?>">2</div>
        <div class="step-line <?= $step >= 3 ? 'done' : '' ?>"></div>
        <div class="step-dot <?= $step >= 3 ? 'done' : ($step == 3 ? 'active' : '') ?>">3</div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($step == 1 || ($step == 2 && !$_SERVER['REQUEST_METHOD'] === 'POST')): ?>
    <div class="text-center">
        <p style="color:#64748b;margin-bottom:24px;">欢迎使用 vzyunIDC 财务管理系统，安装前请确保：</p>
        <ul style="text-align:left;list-style:none;padding:0;">
            <li style="padding:8px 0;">✓ PHP 7.4+ (推荐 8.0+)</li>
            <li style="padding:8px 0;">✓ MySQL 5.7+ (推荐 8.0+)</li>
            <li style="padding:8px 0;">✓ PDO/MySQLi 扩展已启用</li>
            <li style="padding:8px 0;">✓ JSON/CURL/GD/OpenSSL 扩展已启用</li>
            <li style="padding:8px 0;">✓ uploads/ 和 cache/ 目录可写</li>
        </ul>
        <form method="get">
            <input type="hidden" name="step" value="2">
            <button type="submit" class="btn btn-primary">开始安装</button>
        </form>
    </div>

    <?php elseif ($step == 2): ?>
    <form method="post">
        <input type="hidden" name="step" value="2">
        <h5 style="margin-bottom:16px;">数据库配置</h5>
        <div class="row mb-3">
            <div class="col"><label class="form-label">数据库地址</label><input type="text" name="db_host" class="form-control" value="localhost" required></div>
            <div class="col"><label class="form-label">端口</label><input type="text" name="db_port" class="form-control" value="3306" required></div>
        </div>
        <div class="mb-3"><label class="form-label">数据库名</label><input type="text" name="db_name" class="form-control" value="vzyunidc" required></div>
        <div class="row mb-3">
            <div class="col"><label class="form-label">用户名</label><input type="text" name="db_user" class="form-control" value="root" required></div>
            <div class="col"><label class="form-label">密码</label><input type="password" name="db_pass" class="form-control"></div>
        </div>

        <h5 style="margin:24px 0 16px;">站点配置</h5>
        <div class="mb-3"><label class="form-label">站点地址</label><input type="text" name="site_url" class="form-control" placeholder="http://你的域名" required></div>
        <div class="mb-3"><label class="form-label">站点名称</label><input type="text" name="site_name" class="form-control" value="vzyunIDC" required></div>
        <div class="mb-3"><label class="form-label">加密密钥</label><input type="text" name="auth_key" class="form-control" value="<?= bin2hex(random_bytes(16)) ?>" required></div>
        <p class="text-muted" style="font-size:13px;">加密密钥用于密码安全，安装后请勿随意修改</p>

        <button type="submit" class="btn btn-primary w-100">开始安装</button>
    </form>

    <?php elseif ($step == 3): ?>
    <div class="text-center">
        <i class="fas fa-check-circle" style="font-size:48px;color:#10b981;margin-bottom:16px;display:block;"></i>
        <h5>安装完成！</h5>
        <div style="margin:24px 0;">
            <a href="/admin/index.php" class="btn btn-primary" target="_blank">进入管理后台</a>
            <a href="/" class="btn btn-outline-secondary" style="margin-left:8px;">访问首页</a>
        </div>
        <p class="text-muted" style="font-size:14px;">
            默认管理员账号：admin<br>
            默认密码：vzyun2026<br>
            <span class="text-danger">请立即登录后台修改密码！</span>
        </p>
    </div>
    <?php endif; ?>
</div>
<script src="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
</body>
</html>
