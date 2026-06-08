<?php
$pageTitle = '用户登录';
require_once __DIR__ . '/header.php';
?>
<div class="auth-page">
    <div class="auth-box">
        <h3>欢迎回来</h3>
        <p class="subtitle">登录您的账户以继续</p>
        <form id="loginForm">
            <div class="mb-3">
                <label class="form-label">用户名 / 邮箱</label>
                <input type="text" name="username" class="form-control" placeholder="请输入用户名或邮箱" required>
            </div>
            <div class="mb-3">
                <label class="form-label">密码</label>
                <input type="password" name="password" class="form-control" placeholder="请输入密码" required>
            </div>
            <button type="submit" class="btn btn-primary">登 录</button>
        </form>
        <div class="auth-footer">
            还没有账户？<a href="/?route=register">立即注册</a>
        </div>
    </div>
</div>
<script>
$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    var $btn = $(this).find('[type="submit"]');
    $btn.prop('disabled', true).html('<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span>');
    $.post('/api/auth.php?action=login', $(this).serialize(), function(res) {
        if (res.code === 0) {
            showToast('登录成功', 'success');
            setTimeout(function() { location.href = res.redirect || '/'; }, 500);
        } else {
            showToast(res.msg, 'danger');
            $btn.prop('disabled', false).text('登 录');
        }
    }).fail(function() {
        showToast('网络错误', 'danger');
        $btn.prop('disabled', false).text('登 录');
    });
});
</script>
<?php require_once __DIR__ . '/footer.php'; ?>
