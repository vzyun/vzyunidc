<?php
/**
 * vzyunIDC - 管理后台 订单管理
 */
require_once __DIR__ . '/config.php';

$pageTitle = '订单管理';
$db = DB::instance();

// 处理订单操作
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $orderId = (int)($_POST['order_id'] ?? 0);
    $order = $db->getRow('SELECT * FROM orders WHERE id = :id', ['id' => $orderId]);
    if ($order) {
        switch ($_POST['action']) {
            case 'paid':
                $db->update('orders', ['status' => 'paid', 'payment_time' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $orderId]);
                Auth::instance()->log('admin', null, ADMIN_ID, '标记订单已支付:' . $order['order_no']);
                break;
            case 'active':
                $db->update('orders', ['status' => 'active', 'active_time' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $orderId]);
                Auth::instance()->log('admin', null, ADMIN_ID, '开通订单:' . $order['order_no']);
                // 创建host记录
                $db->insert('hosts', [
                    'user_id' => $order['user_id'],
                    'order_id' => $order['id'],
                    'product_id' => $order['product_id'],
                    'status' => 'active',
                    'first_payment' => $order['total'],
                    'billing_cycle' => $order['cycle'],
                    'active_time' => date('Y-m-d H:i:s'),
                ]);
                break;
            case 'cancel':
                $db->update('orders', ['status' => 'cancelled'], 'id = :id', ['id' => $orderId]);
                Auth::instance()->log('admin', null, ADMIN_ID, '取消订单:' . $order['order_no']);
                break;
        }
    }
    header('Location: orders.php');
    exit;
}

$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);

$where = '1=1';
$params = [];
if ($status) {
    $where .= ' AND o.status = :status';
    $params['status'] = $status;
}
if ($search) {
    $where .= ' AND (o.order_no LIKE :search OR u.username LIKE :search2)';
    $params['search'] = '%' . $search . '%';
    $params['search2'] = '%' . $search . '%';
}

$orders = $db->paginate(
    'orders o LEFT JOIN users u ON o.user_id = u.id',
    $where,
    $params,
    'o.created_at DESC',
    $page
);

$statusLabels = ['pending'=>'待支付','paid'=>'已支付','active'=>'已开通','suspended'=>'已暂停','cancelled'=>'已取消','refunded'=>'已退款'];
$statusColors = ['pending'=>'warning','paid'=>'info','active'=>'success','suspended'=>'secondary','cancelled'=>'dark','refunded'=>'danger'];

require_once __DIR__ . '/header.php';
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">订单列表</h5>
    </div>
    <div class="card-body">
        <!-- 搜索 -->
        <div class="search-bar">
            <form method="get" class="d-flex gap-2 flex-wrap">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">全部状态</option>
                    <?php foreach ($statusLabels as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $status === $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="search" class="form-control" placeholder="订单号/用户名" value="<?= h($search) ?>">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>订单号</th>
                        <th>用户</th>
                        <th>金额</th>
                        <th>状态</th>
                        <th>时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders['rows'])): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">暂无订单</td></tr>
                    <?php else: ?>
                    <?php foreach ($orders['rows'] as $order): ?>
                    <tr>
                        <td><code><?= h($order['order_no']) ?></code></td>
                        <td><?= h($order['username'] ?? '--') ?></td>
                        <td>¥<?= formatMoney($order['total']) ?></td>
                        <td>
                            <span class="badge bg-<?= $statusColors[$order['status']] ?? 'secondary' ?>">
                                <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                            </span>
                        </td>
                        <td style="font-size:13px;color:var(--gray-500);"><?= $order['created_at'] ?></td>
                        <td>
                            <?php if ($order['status'] === 'pending'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" name="action" value="paid" class="btn btn-sm btn-success" onclick="return confirm('确认标记为已支付？')">标记支付</button>
                                <button type="submit" name="action" value="cancel" class="btn btn-sm btn-danger" onclick="return confirm('确认取消订单？')">取消</button>
                            </form>
                            <?php elseif ($order['status'] === 'paid'): ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit" name="action" value="active" class="btn btn-sm btn-primary" onclick="return confirm('确认开通产品？')">开通</button>
                            </form>
                            <?php else: ?>
                            <span class="text-muted">--</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($orders['totalPages'] > 1): ?>
        <div class="mt-3">
            <?= paginationHtml($orders['total'], $orders['page'], $orders['pageSize'], 'orders.php' . ($status ? '?status=' . $status : '')) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
