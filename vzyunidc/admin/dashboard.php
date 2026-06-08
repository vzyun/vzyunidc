<?php
/**
 * vzyunIDC - 管理后台 仪表盘
 */
require_once __DIR__ . '/config.php';

$pageTitle = '仪表盘';

// 统计数据
$db = DB::instance();

// 今天时间范围
$todayStart = date('Y-m-d 00:00:00');
$todayEnd = date('Y-m-d 23:59:59');

$stats = [
    'total_users' => $db->getOne('SELECT COUNT(*) FROM users'),
    'today_users' => $db->getOne('SELECT COUNT(*) FROM users WHERE created_at >= :start', ['start' => $todayStart]),
    'total_orders' => $db->getOne('SELECT COUNT(*) FROM orders'),
    'today_orders' => $db->getOne('SELECT COUNT(*) FROM orders WHERE created_at >= :start', ['start' => $todayStart]),
    'pending_orders' => $db->getOne("SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
    'total_products' => $db->getOne('SELECT COUNT(*) FROM products WHERE status = 1'),
    'today_income' => $db->getOne("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status IN ('paid','active') AND created_at >= :start", ['start' => $todayStart]),
    'total_income' => $db->getOne("SELECT COALESCE(SUM(total), 0) FROM orders WHERE status IN ('paid','active')"),
    'total_tickets' => $db->getOne("SELECT COUNT(*) FROM tickets WHERE status != 'closed'"),
    'active_hosts' => $db->getOne("SELECT COUNT(*) FROM hosts WHERE status = 'active'"),
];

// 最近订单
$recentOrders = $db->getRows('SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10');

require_once __DIR__ . '/header.php';
?>

<!-- 统计卡片 -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff;color:#2563eb;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['total_users'] ?></div>
                <div class="stat-label">注册用户</div>
                <div class="stat-change text-success">今日 +<?= $stats['today_users'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef2f2;color:#ef4444;">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['total_orders'] ?></div>
                <div class="stat-label">总订单</div>
                <div class="stat-change text-warning">待处理 <?= $stats['pending_orders'] ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ecfdf5;color:#10b981;">
                <i class="fas fa-yen-sign"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number">¥<?= formatMoney($stats['today_income']) ?></div>
                <div class="stat-label">今日收入</div>
                <div class="stat-change text-muted">累计 ¥<?= formatMoney($stats['total_income']) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background:#fffbeb;color:#f59e0b;">
                <i class="fas fa-server"></i>
            </div>
            <div class="stat-info">
                <div class="stat-number"><?= $stats['active_hosts'] ?></div>
                <div class="stat-label">运行中产品</div>
                <div class="stat-change text-muted">在售 <?= $stats['total_products'] ?> 款</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- 最近订单 -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-clock"></i> 最近订单</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>订单号</th>
                                <th>用户</th>
                                <th>金额</th>
                                <th>状态</th>
                                <th>时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">暂无订单</td></tr>
                            <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td><code><?= h($order['order_no']) ?></code></td>
                                <td><?= h($order['username'] ?? '--') ?></td>
                                <td>¥<?= formatMoney($order['total']) ?></td>
                                <td>
                                    <?php $statusLabels = ['pending'=>'待支付','paid'=>'已支付','active'=>'已开通','suspended'=>'已暂停','cancelled'=>'已取消','refunded'=>'已退款']; ?>
                                    <?php $statusColors = ['pending'=>'warning','paid'=>'info','active'=>'success','suspended'=>'secondary','cancelled'=>'dark','refunded'=>'danger']; ?>
                                    <span class="badge bg-<?= $statusColors[$order['status']] ?? 'secondary' ?>">
                                        <?= $statusLabels[$order['status']] ?? $order['status'] ?>
                                    </span>
                                </td>
                                <td style="font-size:13px;color:var(--gray-500);"><?= $order['created_at'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 快捷操作 -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-bolt"></i> 快捷操作</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="products.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> 添加产品</a>
                    <a href="orders.php?status=pending" class="btn btn-warning"><i class="fas fa-clock"></i> 待处理订单 (<?= $stats['pending_orders'] ?>)</a>
                    <a href="tickets.php" class="btn btn-info"><i class="fas fa-ticket-alt"></i> 工单管理 (<?= $stats['total_tickets'] ?>)</a>
                    <a href="settings.php" class="btn btn-secondary"><i class="fas fa-cog"></i> 系统设置</a>
                </div>
            </div>
        </div>

        <!-- 系统信息 -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> 系统信息</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><td style="width:100px;">PHP版本</td><td><?= PHP_VERSION ?></td></tr>
                    <tr><td>MySQL</td><td><?= $db->getOne('SELECT VERSION()') ?></td></tr>
                    <tr><td>系统版本</td><td>v1.0.0-alpha</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
