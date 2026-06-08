<?php
/**
 * vzyunIDC - 产品列表页
 */
$pageTitle = '产品中心';
require_once __DIR__ . '/header.php';
?>

<section class="product-list-page">
    <div class="product-list-header">
        <h2 style="font-weight:700;font-size:28px;margin-bottom:8px;">产品中心</h2>
        <p style="color:var(--gray-500);">选择最适合您的云产品方案</p>
    </div>

    <!-- 分类标签 -->
    <div class="product-tabs">
        <a href="/?route=products" class="product-tab <?= $currentCategory == 0 ? 'active' : '' ?>">全部</a>
        <?php foreach ($categories as $cat): ?>
        <a href="/?route=products&category=<?= $cat['id'] ?>" class="product-tab <?= $currentCategory == $cat['id'] ? 'active' : '' ?>">
            <?= h($cat['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- 产品列表 -->
    <?php if (empty($products['rows'])): ?>
    <div class="text-center" style="padding:60px 0;">
        <i class="fas fa-box-open" style="font-size:48px;color:var(--gray-300);margin-bottom:16px;display:block;"></i>
        <p style="color:var(--gray-500);font-size:16px;">暂无产品</p>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach ($products['rows'] as $product): ?>
        <div class="product-card">
            <div class="product-card-header">
                <div class="icon">
                    <?php $icons = ['vps' => 'fa-microchip', 'host' => 'fa-home', 'server' => 'fa-server', 'domain' => 'fa-globe', 'other' => 'fa-cube']; ?>
                    <i class="fas <?= $icons[$product['type']] ?? 'fa-server' ?>"></i>
                </div>
                <h3><?= h($product['name']) ?></h3>
                <span class="type"><?= h($product['type']) ?></span>
            </div>
            <div class="product-card-body">
                <ul>
                    <?php 
                    $features = explode("\n", $product['description']);
                    foreach (array_slice($features, 0, 4) as $feature):
                        if (trim($feature)):
                    ?>
                    <li><?= h(trim($feature)) ?></li>
                    <?php endif; endforeach; ?>
                </ul>
            </div>
            <div class="product-card-footer">
                <div class="price">
                    ¥<?= formatMoney($product['price']) ?>
                    <span class="cycle">
                        <?php $cycles = ['monthly'=>'月','quarterly'=>'季','semiannually'=>'半年','annually'=>'年','biennially'=>'两年','triennially'=>'三年']; ?>
                        /<?= $cycles[$product['cycle']] ?? $product['cycle'] ?>
                    </span>
                </div>
                <a href="/?route=product&id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">查看详情</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- 分页 -->
    <div style="margin-top:40px;">
        <?= paginationHtml($products['total'], $products['page'], $products['pageSize'], '/?route=products' . ($currentCategory ? '&category=' . $currentCategory : '')) ?>
    </div>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
