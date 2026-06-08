<?php
/**
 * vzyunIDC - 管理后台 用户管理
 */
require_once __DIR__ . '/config.php';

$pageTitle = '用户管理';
$db = DB::instance();

// 余额调整
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $userId = (int)($_POST['user_id'] ?? 0);
    if ($_POST['action'] === 'balance') {
        $amount = (float)($_POST['amount'] ?? 0);
        $user = $db->getRow('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user) {
            $newBalance = $user['balance'] + $amount;
            $db->update('users', ['balance' => $newBalance], 'id = :id', ['id' => $userId]);
            Auth::instance()->log('admin', null, ADMIN_ID, '调整用户余额 ID:' . $userId . ' 金额:' . $amount);
            $msg = '余额已调整';
        }
    } elseif ($_POST['action'] === 'toggle') {
        $user = $db->getRow('SELECT * FROM users WHERE id = :id', ['id' => $userId]);
        if ($user) {
            $newStatus = $user['status'] ? 0 : 1;
            $db->update('users', ['status' => $newStatus], 'id = :id', ['id' => $userId]);
            Auth::instance()->log('admin', null, ADMIN_ID, ($newStatus ? '启用' : '禁用') . '用户 ID:' . $userId);
            $msg = '用户状态已更新';
        }
    }
    header('Location: users.php?msg=' . urlencode($msg ?? ''));
    exit;
}

$search = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$where = '1=1';
$params = [];
if ($search) {
    $where .= ' AND (username LIKE :search OR email LIKE :search2 OR phone LIKE :search3)';
    $params['search'] = '%' . $search . '%';
    $params['search2'] = '%' . $search . '%';
    $params['search3'] = '%' . $search . '%';
}
$users = $db->paginate('users', $where, $params, 'created_at DESC', $page);

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
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">用户列表</h5>
    </div>
    <div class="card-body">
        <div class="search-bar">
            <form method="get" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="用户名/邮箱/手机" value="<?= h($search) ?>">
                <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>用户名</th>
                        <th>邮箱</th>
                        <th>手机</th>
                        <th>余额</th>
                        <th>状态</th>
                        <th>注册时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users['rows'])): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">暂无用户</td></tr>
                    <?php else: ?>
                    <?php foreach ($users['rows'] as $user): ?>
                    <tr>
                        <td><code><?= $user['id'] ?></code></td>
                        <td><strong><?= h($user['username']) ?></strong></td>
                        <td><?= h($user['email']) ?></td>
                        <td><?= h($user['phone'] ?: '--') ?></td>
                        <td>¥<?= formatMoney($user['balance']) ?></td>
                        <td>
                            <span class="badge bg-<?= $user['status'] ? 'success' : 'secondary' ?>">
                                <?= $user['status'] ? '正常' : '禁用' ?>
                            </span>
                        </td>
                        <td style="font-size:13px;color:var(--gray-500);"><?= $user['created_at'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#balanceModal<?= $user['id'] ?>">
                                <i class="fas fa-coins"></i> 余额
                            </button>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <input type="hidden" name="action" value="toggle">
                                <button type="submit" class="btn btn-sm btn-outline-<?= $user['status'] ? 'danger' : 'success' ?>"
                                    onclick="return confirm('确定<?= $user['status'] ? '禁用' : '启用' ?>该用户？')">
                                    <i class="fas fa-<?= $user['status'] ? 'ban' : 'check' ?>"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <!-- 余额调整弹窗 -->
                    <div class="modal fade" id="balanceModal<?= $user['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post">
                                    <div class="modal-header">
                                        <h5 class="modal-title">调整余额 - <?= h($user['username']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>当前余额：<strong>¥<?= formatMoney($user['balance']) ?></strong></p>
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="action" value="balance">
                                        <div class="input-group">
                                            <span class="input-group-text">¥</span>
                                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="正数充值，负数扣款" required>
                                        </div>
                                        <div class="form-text">输入正数增加余额，输入负数扣除余额</div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                                        <button type="submit" class="btn btn-primary">确认调整</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($users['totalPages'] > 1): ?>
        <div class="mt-3">
            <?= paginationHtml($users['total'], $users['page'], $users['pageSize'], 'users.php') ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
