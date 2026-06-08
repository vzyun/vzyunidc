<?php
/**
 * vzyunIDC - 工单系统前端路由
 */
$action = $_GET['action'] ?? 'list';
$db = DB::instance();

// 用户必须登录
$userInfo = Auth::instance()->getUser();

// 提交工单
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'create') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $priority = $_POST['priority'] ?? 'medium';
    
    $db->insert('tickets', [
        'user_id' => $userInfo['id'],
        'title' => $title,
        'content' => $content,
        'priority' => $priority,
        'status' => 'open',
    ]);
    echo '<script>alert("工单已提交");location.href="/?route=tickets";</script>';
    exit;
}

// 回复工单
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'reply' && isset($_GET['id'])) {
    $content = $_POST['content'] ?? '';
    $ticketId = (int)$_GET['id'];
    $ticket = $db->getRow('SELECT * FROM tickets WHERE id = :id AND user_id = :uid', ['id' => $ticketId, 'uid' => $userInfo['id']]);
    if ($ticket && $ticket['status'] !== 'closed') {
        $db->insert('ticket_replies', [
            'ticket_id' => $ticketId,
            'user_id' => $userInfo['id'],
            'content' => $content,
            'is_admin' => 0,
        ]);
        $db->update('tickets', ['status' => 'open', 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $ticketId]);
    }
    header('Location: /?route=tickets&action=view&id=' . $ticketId);
    exit;
}

// 关闭工单
if ($action === 'close' && isset($_GET['id'])) {
    $db->update('tickets', ['status' => 'closed', 'updated_at' => date('Y-m-d H:i:s')], 'id = :id AND user_id = :uid', ['id' => (int)$_GET['id'], 'uid' => $userInfo['id']]);
    header('Location: /?route=tickets');
    exit;
}

switch ($action) {
    case 'create':
        $pageTitle = '提交工单';
        ?>
        <div class="container" style="padding-top:100px;padding-bottom:40px;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:32px;">
                        <h5>提交工单</h5>
                        <form method="post" class="mt-3">
                            <div class="mb-3">
                                <label class="form-label">标题</label>
                                <input type="text" name="title" class="form-control" required placeholder="简述您的问题">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">优先级</label>
                                <select name="priority" class="form-select">
                                    <option value="low">低</option>
                                    <option value="medium" selected>中</option>
                                    <option value="high">高</option>
                                    <option value="urgent">紧急</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">详细内容</label>
                                <textarea name="content" class="form-control" rows="8" required placeholder="请详细描述您遇到的问题..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">提交工单</button>
                            <a href="/?route=tickets" class="btn btn-outline-secondary">返回</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
        break;

    case 'view':
        $ticketId = (int)$_GET['id'];
        $ticket = $db->getRow('SELECT * FROM tickets WHERE id = :id AND user_id = :uid', ['id' => $ticketId, 'uid' => $userInfo['id']]);
        if (!$ticket) die('工单不存在');
        $replies = $db->getRows('SELECT tr.*, u.username FROM ticket_replies tr LEFT JOIN users u ON tr.user_id = u.id WHERE tr.ticket_id = :tid ORDER BY tr.created_at ASC', ['tid' => $ticketId]);
        $pageTitle = $ticket['title'];
        $statusLabels = ['open'=>'等待回复','waiting_admin'=>'等待管理员回复','waiting_user'=>'等待您回复','closed'=>'已关闭'];
        $statusColors = ['open'=>'warning','waiting_admin'=>'info','waiting_user'=>'success','closed'=>'secondary'];
        $priorityLabels = ['low'=>'低','medium'=>'中','high'=>'高','urgent'=>'紧急'];
        $priorityColors = ['low'=>'secondary','medium'=>'info','high'=>'warning','urgent'=>'danger'];
        ?>
        <div class="container" style="padding-top:100px;padding-bottom:40px;">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:24px;margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:12px;">
                            <div>
                                <h5><?= h($ticket['title']) ?></h5>
                                <span class="badge bg-<?= $priorityColors[$ticket['priority']] ?? 'secondary' ?>"><?= $priorityLabels[$ticket['priority']] ?? $ticket['priority'] ?></span>
                                <span class="badge bg-<?= $statusColors[$ticket['status']] ?? 'secondary' ?>"><?= $statusLabels[$ticket['status']] ?? $ticket['status'] ?></span>
                            </div>
                            <?php if ($ticket['status'] !== 'closed'): ?>
                            <a href="/?route=tickets&action=close&id=<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-secondary" onclick="return confirm('确定关闭工单？')">关闭工单</a>
                            <?php endif; ?>
                        </div>
                        <div style="padding:16px;background:var(--gray-50);border-radius:8px;line-height:1.7;white-space:pre-wrap;"><?= h($ticket['content']) ?></div>
                    </div>

                    <h6 style="margin-bottom:12px;">回复（<?= count($replies) ?>）</h6>
                    
                    <?php foreach ($replies as $reply): ?>
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:16px;margin-bottom:12px;<?= $reply['is_admin'] ? 'border-left:3px solid var(--primary);' : '' ?>">
                        <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--gray-500);margin-bottom:8px;">
                            <span><strong><?= $reply['is_admin'] ? '👤 管理员' : '👤 ' . h($reply['username'] ?? '用户') ?></strong></span>
                            <span><?= $reply['created_at'] ?></span>
                        </div>
                        <div style="line-height:1.7;white-space:pre-wrap;"><?= h($reply['content']) ?></div>
                    </div>
                    <?php endforeach; ?>

                    <?php if ($ticket['status'] !== 'closed'): ?>
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:24px;margin-top:16px;">
                        <form method="post" action="/?route=tickets&action=reply&id=<?= $ticket['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">回复内容</label>
                                <textarea name="content" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">回复</button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
        break;

    default:
        $pageTitle = '我的工单';
        $tickets = $db->getRows('SELECT * FROM tickets WHERE user_id = :uid ORDER BY created_at DESC', ['uid' => $userInfo['id']]);
        ?>
        <div class="container" style="padding-top:100px;padding-bottom:40px;">
            <div class="row g-4">
                <div class="col-lg-3">
                    <?php include __DIR__ . '/themes/default/user/sidebar.php'; ?>
                </div>
                <div class="col-lg-9">
                    <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);">
                        <div style="padding:16px 20px;border-bottom:1px solid var(--gray-200);display:flex;justify-content:space-between;align-items:center;">
                            <h5 class="mb-0">我的工单</h5>
                            <a href="/?route=tickets&action=create" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> 提交工单</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>标题</th><th>优先级</th><th>状态</th><th>时间</th><th>操作</th></tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($tickets)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">暂无工单</td></tr>
                                    <?php else: ?>
                                    <?php foreach ($tickets as $t): ?>
                                    <tr>
                                        <td><strong><?= h($t['title']) ?></strong></td>
                                        <td><span class="badge bg-<?= $priorityColors[$t['priority']] ?? 'secondary' ?>"><?= $priorityLabels[$t['priority']] ?? $t['priority'] ?></span></td>
                                        <td><span class="badge bg-<?= $statusColors[$t['status']] ?? 'secondary' ?>"><?= $statusLabels[$t['status']] ?? $t['status'] ?></span></td>
                                        <td style="font-size:13px;color:var(--gray-500);"><?= $t['created_at'] ?></td>
                                        <td><a href="/?route=tickets&action=view&id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">查看</a></td>
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
}
