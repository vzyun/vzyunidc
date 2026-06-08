<?php
/**
 * vzyunIDC - 注册页
 */
$pageTitle = '用户注册';
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
        <h3>创建账号</h3>

        <form id="registerForm" method="post" action="/api/auth.php?action=register">
            <div id="alertBox"></div>

            <div class="form-group">
                <label for="username">用户名</label>
                <input type="text" class="form-control" id="username" name="username" required
                       placeholder="请输入用户名（字母数字组合）" minlength="3" maxlength="32">
            </div>

            <div class="form-group">
                <label for="email">邮箱</label>
                <input type="email" class="form-control" id="email" name="email" required
                       placeholder="请输入邮箱地址">
            </div>

            <div class="form-group">
                <label for="phone">手机号</label>
                <input type="tel" class="form-control" id="phone" name="phone"
                       placeholder="请输入手机号（选填）" maxlength="11">
            </div>

            <div class="form-group">
                <label for="password">密码</label>
                <input type="password" class="form-control" id="password" name="password" required
                       placeholder="请输入密码（至少6位）" minlength="6">
            </div>

            <div class="form-group">
                <label for="confirm_password">确认密码</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required
                       placeholder="请再次输入密码">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;font-size:16px;">
                <i class="fas fa-user-plus"></i> 注册
            </button>
        </form>

        <div class="form-footer">
            已有账号？<a href="/?route=login">立即登录</a>
        </div>
    </div>
</div>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script>
$('#registerForm').on('submit', function(e) {
    e.preventDefault();
    
    const password = $('#password').val();
    const confirm = $('#confirm_password').val();
    if (password !== confirm) {
        $('#alertBox').html('<div class="alert alert-danger">两次输入的密码不一致</div>');
        return;
    }

    const $btn = $(this).find('button[type="submit"]');
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> 注册中...');
    $('#alertBox').html('');

    $.post($(this).attr('action'), $(this).serialize(), function(res) {
        if (res.code === 0) {
            $('#alertBox').html('<div class="alert alert-success">' + res.msg + '，正在跳转...</div>');
            setTimeout(() => window.location.href = '/?route=login', 1500);
        } else {
            $('#alertBox').html('<div class="alert alert-danger">' + res.msg + '</div>');
            $btn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> 注册');
        }
    }, 'json').fail(function() {
        $('#alertBox').html('<div class="alert alert-danger">网络错误，请重试</div>');
        $btn.prop('disabled', false).html('<i class="fas fa-user-plus"></i> 注册');
    });
});
</script>
</body>
</html>
