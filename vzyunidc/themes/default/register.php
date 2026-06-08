<?php
$pageTitle = '用户注册';
require_once __DIR__ . '/header.php';
?>
<div class="auth-page">
    <div class="auth-box">
        <h3>创建账户</h3>
        <p class="subtitle">注册成为会员，享受高性能云服务</p>
        <form id="registerForm">
            <div class="mb-3">
                <label class="form-label">用户名</label>
                <input type="text" name="username" class="form-control" placeholder="请输入用户名" required>
            </div>
            <div class="mb-3">
                <label class="form-label">邮箱</label>
                <input type="email" name="email" class="form-control" placeholder="请输入邮箱" required>
            </div>
            <div class="mb-3">
                <label class="form-label">手机号</label>
                <input type="text" name="phone" class="form-control" placeholder="选填">
            </div>
            <div class="mb-3">
                <label class="form-label">密码</label>
                <input type="password" name="password" class="form-control" placeholder="至少6位" required minlength="6">
            </div>
            <div class="mb-3">
                <label class="form-label">确认密码</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="再次输入密码" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary">注 册</button>
        </form>
        <div class="auth-footer">
            已有账户？<a href="/?route=login">立即登录</a>
        </div>
    </div>
</div>
<script>
$('#registerForm').on('submit', function(e) {
    e.preventDefault();
    if ($('[name="password"]').val() !== $('[name="confirm_password"]').val()) {
        showToast('两次密码输入不一致', 'danger');
        return;
    }
    var $btn = $(this).find('[type="submit"]');
    $btn.prop('disabled', true).html('<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span>');
    $.post('/api/auth.php?action=register', $(this).serialize(), function(res) {
        if (res.code === 0) {
            showToast('注册成功', 'success');
            setTimeout(function() { location.href = '/'; }, 500);
        } else {
            showToast(res.msg, 'danger');
            $btn.prop('disabled', false).text('注 册');
        }
    }).fail(function() {
        showToast('网络错误', 'danger');
        $btn.prop('disabled', false).text('注 册');
    });
});
</script>
<?php require_once __DIR__ . '/footer.php'; ?>
