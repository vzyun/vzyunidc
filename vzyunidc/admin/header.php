<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? '仪表盘') ?> - vzyunIDC 管理后台</title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=1.0">
</head>
<body>
<div class="admin-wrapper">
    <!-- 侧边栏 -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h4>vzyunIDC</h4>
            <span class="badge bg-success">管理后台</span>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-chart-pie"></i> <span>仪表盘</span>
            </a>
            <a href="products.php" class="<?= strpos($_SERVER['SCRIPT_NAME'], 'product') !== false ? 'active' : '' ?>">
                <i class="fas fa-box"></i> <span>产品管理</span>
            </a>
            <a href="orders.php" class="<?= strpos($_SERVER['SCRIPT_NAME'], 'order') !== false ? 'active' : '' ?>">
                <i class="fas fa-file-invoice"></i> <span>订单管理</span>
            </a>
            <a href="users.php" class="<?= strpos($_SERVER['SCRIPT_NAME'], 'user') !== false ? 'active' : '' ?>">
                <i class="fas fa-users"></i> <span>用户管理</span>
            </a>
            <a href="tickets.php">
                <i class="fas fa-ticket-alt"></i> <span>工单管理</span>
            </a>
            <a href="plugins.php">
                <i class="fas fa-puzzle-piece"></i> <span>插件管理</span>
            </a>
            <a href="settings.php" class="<?= basename($_SERVER['SCRIPT_NAME']) === 'settings.php' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> <span>系统设置</span>
            </a>
            <hr style="border-color:rgba(255,255,255,0.1);margin:12px 0;">
            <a href="logout.php" style="color:var(--gray-400);">
                <i class="fas fa-sign-out-alt"></i> <span>退出登录</span>
            </a>
        </nav>
    </aside>

    <!-- 主内容 -->
    <div class="main-content">
        <header class="topbar">
            <div class="topbar-left">
                <h5 class="mb-0"><?= h($pageTitle ?? '仪表盘') ?></h5>
            </div>
            <div class="topbar-right">
                <span style="font-size:14px;color:var(--gray-500);">
                    <i class="fas fa-user"></i> <?= h($_SESSION['admin_username'] ?? '管理员') ?>
                </span>
            </div>
        </header>
        <div class="content-body">
