<?php
/**
 * vzyunIDC - 管理后台 登录页
 */

// 检查是否已有session登录
$inAdminPath = dirname($_SERVER['SCRIPT_NAME']);
session_start();
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/init.php';
    
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = Auth::instance()->adminLogin($username, $password);
    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['msg'];
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理后台 - <?= defined('SITE_NAME') ? SITE_NAME : 'vzyunIDC' ?></title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f1f5f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box { background: white; border-radius: 16px; padding: 40px; width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .login-box .logo { text-align: center; margin-bottom: 32px; }
        .login-box .logo h3 { background: linear-gradient(135deg, #2563eb, #0ea5e9); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 800; }
        .form-control { padding: 12px 16px; }
        .btn-primary { background: linear-gradient(135deg, #2563eb, #0ea5e9); border: none; padding: 12px; width: 100%; font-size: 16px; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo"><h3>vzyunIDC 管理后台</h3></div>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">用户名</label>
                <input type="text" name="username" class="form-control" required placeholder="请输入管理员用户名">
            </div>
            <div class="mb-3">
                <label class="form-label">密码</label>
                <input type="password" name="password" class="form-control" required placeholder="请输入密码">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> 登录</button>
        </form>
    </div>
</body>
</html>
