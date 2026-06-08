<?php
/**
 * vzyunIDC - 登录页
 */
$pageTitle = '用户登录';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/themes/default/css/style.css?v=1.0">
</head>
<body>
<div class="auth-page">
    <div class="auth-box">
        <div class="logo">
            <h2>vzyunIDC</h2>
        </div>
        <h3>用户登录</h3>

        <form id="loginForm" method="post" action="/api/auth.php?action=login">
            <div id="alertBox"></div>

            <div class="form-group">
                <label for="username">用户名 / 邮箱</label>
                <input type="text" class="form-control" id="username" name="username" required
                       placeholder="请输入用户名或邮箱">
            </div>

            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" class="form-control" id="password" name="password" required
                       placeholder="请输入密码">
            </div>

            <div class="form-group" style="display:flex;justify-content:space-between;align-items:center;">
                <label style="margin:0;">
                    <input type="checkbox" name="remember" value="1"> 记住登录
                </label>
                <a href="/?route=forgot" style="font-size:14px;">忘记密码？</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:16px;">
                <i class="fas fa-sign-in-alt"></i> 登录
            </button>
        </form>

        <div class="form-footer">
            还没有账号？<a href="/?route=register">立即注册</a>
        </div>
    </div>
</div>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> 登录中...');
    $('#alertBox').html('');

    $.post($(this).attr('action'), $(this).serialize(), function(res) {
        if (res.code === 0) {
            window.location.href = '/?route=user';
        } else {
            $('#alertBox').html('<div class="alert alert-danger">' + res.msg + '</div>');
            $btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> 登录');
        }
    }, 'json').fail(function() {
        $('#alertBox').html('<div class="alert alert-danger">网络错误，请重试</div>');
        $btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> 登录');
    });
});
</script>
</body>
</html>
