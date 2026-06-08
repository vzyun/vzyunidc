<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? SITE_NAME) ?> - <?= SITE_NAME ?></title>
    <meta name="keywords" content="<?= h(SITE_KEYWORDS) ?>">
    <meta name="description" content="<?= h(SITE_DESCRIPTION) ?>">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/themes/default/css/style.css?v=1.0">
    <?php Hook::action('frontend_head'); ?>
</head>
<body>
<?php $announcement = getSetting('site_announcement'); ?>
<?php if ($announcement): ?>
<div class="announcement-bar">
    <?= h($announcement) ?>
</div>
<?php endif; ?>

<header class="header">
    <div class="header-inner">
        <a href="/" class="logo">
            <img src="/themes/default/assets/logo.svg" alt="vzyunIDC" style="height:32px;width:32px;vertical-align:middle;">
            <span style="margin-left:8px;">vzyunIDC</span>
        </a>
        <nav class="nav">
            <a href="/" class="<?= !isset($_GET['route']) || $_GET['route'] === 'index' ? 'active' : '' ?>">首页</a>
            <a href="/?route=products" class="<?= ($_GET['route'] ?? '') === 'products' ? 'active' : '' ?>">产品</a>
            <a href="/?route=cart">购物车</a>
            <a href="/?route=user">用户中心</a>
        </nav>
        <div class="nav-right">
            <?php if (isset($currentUser) && $currentUser): ?>
                <a href="/?route=user" class="btn btn-outline btn-sm">
                    <i class="fas fa-user"></i> <?= h($currentUser['username']) ?>
                </a>
                <a href="/?route=user&action=logout" class="btn btn-outline btn-sm">退出</a>
            <?php else: ?>
                <a href="/?route=login" class="btn btn-outline btn-sm">登录</a>
                <a href="/?route=register" class="btn btn-primary btn-sm">注册</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<main>
