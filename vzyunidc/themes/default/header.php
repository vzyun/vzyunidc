<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? SITE_NAME) ?> - <?= SITE_NAME ?></title>
    <meta name="keywords" content="云服务器,VPS,主机,域名,云计算">
    <meta name="description" content="<?= SITE_NAME ?> - 精品云计算服务商，提供云服务器、VPS、虚拟主机等产品">
    <link rel="icon" href="/themes/default/assets/logo.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/themes/default/css/style.css?v=2.0">
    <?php Hook::action('frontend_head'); ?>
</head>
<body<?= getSetting('site_announcement') ? ' class="has-announcement"' : '' ?>>

<?php $announcement = getSetting('site_announcement'); ?>
<?php if ($announcement): ?>
<div class="announcement-bar">
    <?= $announcement ?>
</div>
<?php endif; ?>

<header class="header" id="mainHeader">
    <div class="header-inner">
        <a href="/" class="logo">
            <img src="/themes/default/assets/logo.svg" alt="<?= SITE_NAME ?>">
            <span><?= SITE_NAME ?></span>
        </a>
        <nav class="nav">
            <a href="/" class="<?= !isset($_GET['route']) || $_GET['route'] === 'index' ? 'active' : '' ?>">首页</a>
            <a href="/?route=products" class="<?= ($_GET['route'] ?? '') === 'products' ? 'active' : '' ?>">产品中心</a>
            <a href="/?route=cart"><i class="fas fa-shopping-cart"></i> 购物车</a>
            <a href="/?route=tickets">工单</a>
            <?php if (isset($currentUser) && $currentUser): ?>
            <a href="/?route=user" class="<?= ($_GET['route'] ?? '') === 'user' ? 'active' : '' ?>"><i class="fas fa-user"></i> 用户中心</a>
            <?php endif; ?>
        </nav>
        <div class="nav-right">
            <?php if (isset($currentUser) && $currentUser): ?>
                <a href="/?route=user" class="btn btn-outline btn-sm"><i class="fas fa-user"></i> <?= h($currentUser['username']) ?></a>
                <a href="/?route=logout" class="btn btn-sm" style="color:var(--gray-500);">退出</a>
            <?php else: ?>
                <a href="/?route=login" class="btn btn-outline btn-sm">登录</a>
                <a href="/?route=register" class="btn btn-primary btn-sm">免费注册</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main>
