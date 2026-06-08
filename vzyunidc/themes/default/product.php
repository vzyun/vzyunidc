<?php
/**
 * vzyunIDC - 产品详情页
 */
$pageTitle = $product['name'];
require_once __DIR__ . '/header.php';
?>

<section style="padding:40px 20px;">
    <div class="container">
        <nav aria-label="breadcrumb" style="margin-bottom:24px;">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">首页</a></li>
                <li class="breadcrumb-item"><a href="/?route=products">产品中心</a></li>
                <li class="breadcrumb-item active"><?= h($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- 产品信息 -->
            <div class="col-lg-8">
                <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:32px;">
                    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
                        <div style="width:56px;height:56px;background:var(--gradient-primary);border-radius:16px;display:flex;align-items:center;justify-content:center;color:white;font-size:24px;">
                            <i class="fas fa-server"></i>
                        </div>
                        <div>
                            <h3 style="margin:0;"><?= h($product['name']) ?></h3>
                            <span style="color:var(--gray-500);font-size:14px;"><?= h($product['type']) ?></span>
                        </div>
                    </div>

                    <h5 style="font-weight:600;margin-bottom:12px;">产品描述</h5>
                    <p style="color:var(--gray-600);line-height:1.8;white-space:pre-line;"><?= h($product['description']) ?></p>

                    <?php if (!empty($product['content'])): ?>
                    <hr style="margin:24px 0;">
                    <div style="line-height:1.8;">
                        <?= $product['content'] ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 购买面板 -->
            <div class="col-lg-4">
                <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:24px;position:sticky;top:80px;">
                    <div style="text-align:center;margin-bottom:20px;">
                        <div style="font-size:36px;font-weight:800;color:var(--primary);">
                            ¥<?= formatMoney($product['price']) ?>
                            <span style="font-size:14px;font-weight:400;color:var(--gray-500);">
                                <?php $cycles = ['monthly'=>'/月','quarterly'=>'/季','semiannually'=>'/半年','annually'=>'/年','biennially'=>'/两年','triennially'=>'/三年']; ?>
                                <?= $cycles[$product['cycle']] ?? '' ?>
                            </span>
                        </div>
                        <?php if ($product['setup_fee'] > 0): ?>
                        <div style="font-size:14px;color:var(--gray-400);margin-top:4px;">
                            初装费 ¥<?= formatMoney($product['setup_fee']) ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-bottom:20px;">
                        <label style="font-size:14px;font-weight:500;margin-bottom:8px;display:block;">购买周期</label>
                        <select class="form-control" id="cycleSelect">
                            <option value="monthly" <?= $product['cycle'] === 'monthly' ? 'selected' : '' ?>>按月</option>
                            <option value="quarterly" <?= $product['cycle'] === 'quarterly' ? 'selected' : '' ?>>按季度</option>
                            <option value="semiannually" <?= $product['cycle'] === 'semiannually' ? 'selected' : '' ?>>按半年</option>
                            <option value="annually" <?= $product['cycle'] === 'annually' ? 'selected' : '' ?>>按年</option>
                        </select>
                    </div>

                    <button class="btn btn-primary btn-lg" style="width:100%;" onclick="buyNow(<?= $product['id'] ?>)">
                        <i class="fas fa-shopping-cart"></i> 立即购买
                    </button>
                    <button class="btn btn-outline btn-sm" style="width:100%;margin-top:8px;" onclick="addToCart(<?= $product['id'] ?>)">
                        <i class="fas fa-cart-plus"></i> 加入购物车
                    </button>

                    <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--gray-200);font-size:13px;color:var(--gray-500);">
                        <p><i class="fas fa-shield-alt"></i> 安全支付保障</p>
                        <p><i class="fas fa-clock"></i> 购买后即时开通</p>
                        <p><i class="fas fa-headset"></i> 7×24小时技术支持</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function buyNow(productId) {
    const cycle = $('#cycleSelect').val();
    $.post('/api/order.php?action=create', { product_id: productId, cycle: cycle }, function(res) {
        if (res.code === 0) {
            window.location.href = '/?route=user&action=order&order_no=' + res.data.order_no;
        } else if (res.code === 401) {
            window.location.href = '/?route=login';
        } else {
            showToast(res.msg, 'danger');
        }
    }, 'json');
}

function addToCart(productId) {
    $.post('/api/product.php?action=add_cart', { product_id: productId }, function(res) {
        if (res.code === 0) {
            showToast('已加入购物车', 'success');
        } else if (res.code === 401) {
            window.location.href = '/?route=login';
        } else {
            showToast(res.msg, 'danger');
        }
    }, 'json');
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
