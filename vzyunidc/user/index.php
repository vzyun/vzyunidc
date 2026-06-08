<?php
require_once __DIR__ . '/../includes/init.php';
if (!isset($currentUser) || !$currentUser) {
    header('Location: /?route=login');
    exit;
}
// 路由参数
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'dashboard':
        $pageTitle = '用户中心';
        $userInfo = Auth::instance()->getUser();
        $db = DB::instance();
        $orderCount = $db->getOne("SELECT COUNT(*) FROM orders WHERE user_id = :uid", ['uid' => $userInfo['id']]);
        $hostCount = $db->getOne("SELECT COUNT(*) FROM hosts WHERE user_id = :uid AND status = 'active'", ['uid' => $userInfo['id']]);
        $ticketCount = $db->getOne("SELECT COUNT(*) FROM tickets WHERE user_id = :uid AND status != 'closed'", ['uid' => $userInfo['id']]);
        ?>
        <div class="container" style="padding-top:100px;padding-bottom:40px;">
            <div class="row g-4">
                <div class="col-lg-3">
                    <?php include __DIR__ . '/user/sidebar.php'; ?>
                </div>
                <div class="col-lg-9">
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:24px;margin-bottom:20px;">
                        <h5>欢迎回来，<?= h($userInfo['username']) ?> 👋</h5>
                        <p style="color:var(--gray-500);margin:4px 0 0;">
                            账户余额：<strong style="color:var(--primary);">¥<?= formatMoney($userInfo['balance']) ?></strong>
                        </p>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:20px;text-align:center;">
                                <div style="font-size:28px;font-weight:700;color:var(--primary);"><?= $orderCount ?></div>
                                <div style="font-size:14px;color:var(--gray-500);">全部订单</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:20px;text-align:center;">
                                <div style="font-size:28px;font-weight:700;color:var(--success);"><?= $hostCount ?></div>
                                <div style="font-size:14px;color:var(--gray-500);">运行中产品</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:20px;text-align:center;">
                                <div style="font-size:28px;font-weight:700;color:var(--warning);"><?= $ticketCount ?></div>
                                <div style="font-size:14px;color:var(--gray-500);">待处理工单</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'profile':
        $pageTitle = '账户资料';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            DB::instance()->update('users', ['email' => $email, 'phone' => $phone], 'id = :id', ['id' => $userInfo['id']]);
            echo '<script>alert("资料已更新");location.reload();</script>';
        }
        $userInfo = Auth::instance()->getUser();
        ?>
        <div class="container" style="padding-top:100px;padding-bottom:40px;">
            <div class="row g-4">
                <div class="col-lg-3"><?php include __DIR__ . '/user/sidebar.php'; ?></div>
                <div class="col-lg-9">
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:24px;">
                        <h5>账户资料</h5>
                        <form method="post" class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">用户名</label>
                                <input type="text" class="form-control" value="<?= h($userInfo['username']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">邮箱</label>
                                <input type="email" name="email" class="form-control" value="<?= h($userInfo['email']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">手机号</label>
                                <input type="text" name="phone" class="form-control" value="<?= h($userInfo['phone']) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">注册时间</label>
                                <input type="text" class="form-control" value="<?= $userInfo['created_at'] ?>" disabled>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">保存修改</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'orders':
        $pageTitle = '我的订单';
        $db = DB::instance();
        $orders = $db->getRows('SELECT * FROM orders WHERE user_id = :uid ORDER BY created_at DESC', ['uid' => $userInfo['id']]);
        $statusLabels = ['pending'=>'待支付','paid'=>'已支付','active'=>'已开通','suspended'=>'已暂停','cancelled'=>'已取消','refunded'=>'已退款'];
        $statusColors = ['pending'=>'warning','paid'=>'info','active'=>'success','suspended'=>'secondary','cancelled'=>'dark','refunded'=>'danger'];
        ?>
        <div class="container" style="padding-top:100px;padding-bottom:40px;">
            <div class="row g-4">
                <div class="col-lg-3"><?php include __DIR__ . '/user/sidebar.php'; ?></div>
                <div class="col-lg-9">
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);">
                        <div style="padding:16px 20px;border-bottom:1px solid var(--gray-200);"><h5 class="mb-0">我的订单</h5></div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>订单号</th><th>金额</th><th>状态</th><th>时间</th><th>操作</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($orders)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">暂无订单</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($orders as $o): ?>
                                    <tr>
                                        <td><code><?= h($o['order_no']) ?></code></td>
                                        <td>¥<?= formatMoney($o['total']) ?></td>
                                        <td><span class="badge bg-<?= $statusColors[$o['status']] ?? 'secondary' ?>"><?= $statusLabels[$o['status']] ?? $o['status'] ?></span></td>
                                        <td style="font-size:13px;color:var(--gray-500);"><?= $o['created_at'] ?></td>
                                        <td>
                                            <?php if ($o['status'] === 'pending'): ?>
                                            <a href="/?route=checkout&order_no=<?= $o['order_no'] ?>" class="btn btn-sm btn-primary">去支付</a>
                                            <?php elseif ($o['status'] === 'paid'): ?>
                                            <span class="text-info">等待开通</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'hosts':
        $pageTitle = '我的产品';
        $db = DB::instance();
        $hosts = $db->getRows('SELECT h.*, p.name as product_name FROM hosts h LEFT JOIN products p ON h.product_id = p.id WHERE h.user_id = :uid ORDER BY h.created_at DESC', ['uid' => $userInfo['id']]);
        $hostStatusLabels = ['pending'=>'待开通','active'=>'运行中','suspended'=>'已暂停','cancelled'=>'已取消','terminated'=>'已删除'];
        $hostStatusColors = ['pending'=>'warning','active'=>'success','suspended'=>'secondary','cancelled'=>'dark','terminated'=>'danger'];
        ?>
        <div class="container" style="padding-top:100px;padding-bottom:40px;">
            <div class="row g-4">
                <div class="col-lg-3"><?php include __DIR__ . '/user/sidebar.php'; ?></div>
                <div class="col-lg-9">
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);">
                        <div style="padding:16px 20px;border-bottom:1px solid var(--gray-200);"><h5 class="mb-0">我的产品</h5></div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>产品</th><th>状态</th><th>开通时间</th><th>到期时间</th><th>操作</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($hosts)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">暂无产品</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($hosts as $h): ?>
                                    <tr>
                                        <td><strong><?= h($h['product_name'] ?? '--') ?></strong></td>
                                        <td><span class="badge bg-<?= $hostStatusColors[$h['status']] ?? 'secondary' ?>"><?= $hostStatusLabels[$h['status']] ?? $h['status'] ?></span></td>
                                        <td style="font-size:13px;color:var(--gray-500);"><?= $h['active_time'] ?? '--' ?></td>
                                        <td style="font-size:13px;color:var(--gray-500);"><?= $h['expire_time'] ?? '--' ?></td>
                                        <td>
                                            <a href="/?route=user&action=host_detail&id=<?= $h['id'] ?>" class="btn btn-sm btn-outline-primary">管理</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    default:
        echo '<div class="container" style="padding-top:100px;"><h3>页面不存在</h3></div>';
}
