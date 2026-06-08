<?php
/**
 * vzyunIDC - 管理后台 系统设置
 */
require_once __DIR__ . '/config.php';

$pageTitle = '系统设置';
$db = DB::instance();

$settingGroups = [
    'system' => '基本设置',
    'mail' => '邮件配置',
    'payment' => '支付配置',
    'security' => '安全设置',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'setting_') === 0) {
            $settingKey = substr($key, 8);
            updateSetting($settingKey, $value);
        }
    }
    Auth::instance()->log('admin', null, ADMIN_ID, '更新系统设置');
    $msg = '设置已保存';
}

$allSettings = getAllSettings();
$currentTab = $_GET['tab'] ?? 'system';

require_once __DIR__ . '/header.php';
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= h($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <?php foreach ($settingGroups as $group => $label): ?>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === $group ? 'active' : '' ?>" href="settings.php?tab=<?= $group ?>">
                    <?= h($label) ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="card-body">
        <form method="post">
            <?php if ($currentTab === 'system'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">站点名称</label>
                    <input type="text" name="setting_site_name" class="form-control" value="<?= h($allSettings['site_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">站点地址</label>
                    <input type="text" name="setting_site_url" class="form-control" value="<?= h($allSettings['site_url'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">货币符号</label>
                    <input type="text" name="setting_currency_symbol" class="form-control" value="<?= h($allSettings['currency_symbol'] ?? '¥') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ICP备案号</label>
                    <input type="text" name="setting_site_icp" class="form-control" value="<?= h($allSettings['site_icp'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">默认主题</label>
                    <select name="setting_default_theme" class="form-select">
                        <option value="default" <?= ($allSettings['default_theme'] ?? '') === 'default' ? 'selected' : '' ?>>默认主题</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">站点状态</label>
                    <select name="setting_site_status" class="form-select">
                        <option value="1" <?= ($allSettings['site_status'] ?? '1') == '1' ? 'selected' : '' ?>>正常开放</option>
                        <option value="0" <?= ($allSettings['site_status'] ?? '1') == '0' ? 'selected' : '' ?>>维护中</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Hero标题（首页大标题）</label>
                    <input type="text" name="setting_hero_title" class="form-control" value="<?= h($allSettings['hero_title'] ?? '高性能云服务<br>助力您的<span>业务增长</span>') ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Hero副标题</label>
                    <input type="text" name="setting_hero_subtitle" class="form-control" value="<?= h($allSettings['hero_subtitle'] ?? '弹性扩展 · 安全可靠 · 全球部署 · 极速体验') ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">底部版权信息</label>
                    <textarea name="setting_site_footer" class="form-control" rows="2"><?= h($allSettings['site_footer'] ?? '© 2026 vzyunIDC All rights reserved.') ?></textarea>
                </div>
            </div>

            <?php elseif ($currentTab === 'mail'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">邮件发送方式</label>
                    <select name="setting_mail_type" class="form-select">
                        <option value="smtp" <?= ($allSettings['mail_type'] ?? '') === 'smtp' ? 'selected' : '' ?>>SMTP</option>
                        <option value="mail" <?= ($allSettings['mail_type'] ?? '') === 'mail' ? 'selected' : '' ?>>PHP mail()</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">SMTP服务器</label>
                    <input type="text" name="setting_mail_smtp_host" class="form-control" value="<?= h($allSettings['mail_smtp_host'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SMTP端口</label>
                    <input type="text" name="setting_mail_smtp_port" class="form-control" value="<?= h($allSettings['mail_smtp_port'] ?? '465') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SMTP用户名</label>
                    <input type="text" name="setting_mail_smtp_user" class="form-control" value="<?= h($allSettings['mail_smtp_user'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SMTP密码</label>
                    <input type="password" name="setting_mail_smtp_pass" class="form-control" value="<?= h($allSettings['mail_smtp_pass'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">发件人邮箱</label>
                    <input type="text" name="setting_mail_from_address" class="form-control" value="<?= h($allSettings['mail_from_address'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">发件人名称</label>
                    <input type="text" name="setting_mail_from_name" class="form-control" value="<?= h($allSettings['mail_from_name'] ?? 'vzyunIDC') ?>">
                </div>
            </div>

            <?php elseif ($currentTab === 'payment'): ?>
            <div class="row g-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 支付接口需要安装对应插件后配置，内置手动入账功能。
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="alipay_enabled" disabled>
                        <label class="form-check-label" for="alipay_enabled">支付宝支付（需要安装支付宝插件）</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="wxpay_enabled" disabled>
                        <label class="form-check-label" for="wxpay_enabled">微信支付（需要安装微信支付插件）</label>
                    </div>
                </div>
            </div>

            <?php elseif ($currentTab === 'security'): ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">开放注册</label>
                    <select name="setting_register_enabled" class="form-select">
                        <option value="1" <?= ($allSettings['register_enabled'] ?? '1') == '1' ? 'selected' : '' ?>>开启</option>
                        <option value="0" <?= ($allSettings['register_enabled'] ?? '1') == '0' ? 'selected' : '' ?>>关闭</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">邮箱验证注册</label>
                    <select name="setting_register_verify_email" class="form-select">
                        <option value="1" <?= ($allSettings['register_verify_email'] ?? '0') == '1' ? 'selected' : '' ?>>开启</option>
                        <option value="0" <?= ($allSettings['register_verify_email'] ?? '0') == '0' ? 'selected' : '' ?>>关闭</option>
                    </select>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 保存设置</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
