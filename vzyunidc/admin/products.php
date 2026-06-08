<?php
/**
 * vzyunIDC - 管理后台 产品管理
 */
require_once __DIR__ . '/config.php';

$pageTitle = '产品管理';
$db = DB::instance();
$action = $_GET['action'] ?? 'list';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $description = $_POST['description'] ?? '';
    $type = $_POST['type'] ?? 'other';
    $price = (float)($_POST['price'] ?? 0);
    $cycle = $_POST['cycle'] ?? 'monthly';
    $stock = (int)($_POST['stock'] ?? -1);
    $status = isset($_POST['status']) ? 1 : 0;
    $recommended = isset($_POST['recommended']) ? 1 : 0;
    $setupFee = (float)($_POST['setup_fee'] ?? 0);
    $sort = (int)($_POST['sort'] ?? 0);

    if ($action === 'add') {
        $db->insert('products', [
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'price' => $price,
            'cycle' => $cycle,
            'stock' => $stock,
            'status' => $status,
            'recommended' => $recommended,
            'setup_fee' => $setupFee,
            'sort' => $sort,
        ]);
        $msg = '产品添加成功';
    } elseif ($action === 'edit' && isset($_GET['id'])) {
        $db->update('products', [
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'price' => $price,
            'cycle' => $cycle,
            'stock' => $stock,
            'status' => $status,
            'recommended' => $recommended,
            'setup_fee' => $setupFee,
            'sort' => $sort,
        ], 'id = :id', ['id' => (int)$_GET['id']]);
        $msg = '产品更新成功';
    }
    
    Auth::instance()->log('admin', null, ADMIN_ID, $msg);
    header('Location: products.php?msg=' . urlencode($msg));
    exit;
}

// 删除产品
if ($action === 'delete' && isset($_GET['id'])) {
    $db->update('products', ['status' => 0], 'id = :id', ['id' => (int)$_GET['id']]);
    Auth::instance()->log('admin', null, ADMIN_ID, '下架产品 ID:' . $_GET['id']);
    header('Location: products.php?msg=' . urlencode('产品已下架'));
    exit;
}

// 获取编辑数据
$editProduct = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $editProduct = $db->getRow('SELECT * FROM products WHERE id = :id', ['id' => (int)$_GET['id']]);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = $_GET['search'] ?? '';
$where = '1=1';
$params = [];
if ($search) {
    $where .= ' AND name LIKE :search';
    $params['search'] = '%' . $search . '%';
}
$products = $db->paginate('products', $where, $params, 'sort ASC, id DESC', $page);
$categories = $db->getRows('SELECT * FROM product_categories WHERE status = 1 ORDER BY sort ASC');

require_once __DIR__ . '/header.php';
$msg = $_GET['msg'] ?? '';
?>

<?php if ($msg): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= h($msg) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($action === 'add' || ($action === 'edit' && $editProduct)): ?>
<!-- 添加/编辑产品表单 -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0"><?= $action === 'add' ? '添加产品' : '编辑产品' ?></h5>
    </div>
    <div class="card-body">
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">产品名称</label>
                <input type="text" name="name" class="form-control" required value="<?= h($editProduct['name'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">产品分类</label>
                <select name="category_id" class="form-select">
                    <option value="0">未分类</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id'] ?? 0) == $cat['id'] ? 'selected' : '' ?>>
                        <?= h($cat['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">产品类型</label>
                <select name="type" class="form-select">
                    <?php $types = ['vps'=>'VPS','host'=>'虚拟主机','server'=>'物理服务器','domain'=>'域名','other'=>'其他']; ?>
                    <?php foreach ($types as $k => $v): ?>
                    <option value="<?= $k ?>" <?= ($editProduct['type'] ?? '') == $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label">产品描述</label>
                <textarea name="description" class="form-control" rows="3"><?= h($editProduct['description'] ?? '') ?></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label">价格 (¥)</label>
                <input type="number" step="0.01" name="price" class="form-control" required value="<?= $editProduct['price'] ?? '0' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">初装费 (¥)</label>
                <input type="number" step="0.01" name="setup_fee" class="form-control" value="<?= $editProduct['setup_fee'] ?? '0' ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">计费周期</label>
                <select name="cycle" class="form-select">
                    <?php $cycles = ['monthly'=>'月付','quarterly'=>'季付','semiannually'=>'半年付','annually'=>'年付']; ?>
                    <?php foreach ($cycles as $k => $v): ?>
                    <option value="<?= $k ?>" <?= ($editProduct['cycle'] ?? 'monthly') == $k ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">库存（-1不限）</label>
                <input type="number" name="stock" class="form-control" value="<?= $editProduct['stock'] ?? '-1' ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">排序</label>
                <input type="number" name="sort" class="form-control" value="<?= $editProduct['sort'] ?? '0' ?>">
            </div>
            <div class="col-md-4 d-flex align-items-center gap-3" style="padding-top:32px;">
                <div class="form-check">
                    <input type="checkbox" name="status" class="form-check-input" id="statusCheck" <?= !isset($editProduct) || $editProduct['status'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="statusCheck">上架</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="recommended" class="form-check-input" id="recommendCheck" <?= !empty($editProduct['recommended']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="recommendCheck">推荐产品</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary"><?= $action === 'add' ? '添加' : '保存修改' ?></button>
                <a href="products.php" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>

<?php else: ?>
<!-- 产品列表 -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">产品列表</h5>
        <a href="products.php?action=add" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> 添加产品</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>名称</th>
                        <th>类型</th>
                        <th>价格</th>
                        <th>周期</th>
                        <th>库存</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products['rows'])): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">暂无产品</td></tr>
                    <?php else: ?>
                    <?php foreach ($products['rows'] as $p): ?>
                    <tr>
                        <td><code><?= $p['id'] ?></code></td>
                        <td><strong><?= h($p['name']) ?></strong></td>
                        <td><?= h($p['type']) ?></td>
                        <td>¥<?= formatMoney($p['price']) ?></td>
                        <td><?= h($p['cycle']) ?></td>
                        <td><?= $p['stock'] == -1 ? '不限' : $p['stock'] ?></td>
                        <td>
                            <span class="badge bg-<?= $p['status'] ? 'success' : 'secondary' ?>">
                                <?= $p['status'] ? '上架' : '下架' ?>
                            </span>
                            <?php if ($p['recommended']): ?>
                            <span class="badge bg-warning">推荐</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="products.php?action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                            <a href="products.php?action=delete&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($products['totalPages'] > 1): ?>
        <div class="p-3 border-top">
            <?= paginationHtml($products['total'], $products['page'], $products['pageSize'], 'products.php') ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
