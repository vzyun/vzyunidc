<?php
/**
 * vzyunIDC - 购物车/结算页
 */
$action = $_GET['action'] ?? 'cart';
$db = DB::instance();

if ($action === 'cart') {
    $pageTitle = '购物车';
    $cartItems = $db->getRows('SELECT c.*, p.name as product_name, p.price, p.cycle, p.description 
        FROM carts c LEFT JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = :uid ORDER BY c.created_at DESC', ['uid' => $userInfo['id']]);
    ?>
    <div class="container" style="padding-top:100px;padding-bottom:40px;">
        <h4 style="font-weight:700;margin-bottom:24px;">购物车</h4>
        <?php if (empty($cartItems)): ?>
        <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:60px;text-align:center;">
            <i class="fas fa-cart-empty" style="font-size:48px;color:var(--gray-300);display:block;margin-bottom:12px;"></i>
            <p style="color:var(--gray-500);">购物车空空如也</p>
            <a href="/?route=products" class="btn btn-primary">去选购</a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <?php foreach ($cartItems as $item): ?>
                <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:16px 20px;margin-bottom:12px;display:flex;align-items:center;gap:16px;">
                    <div style="flex:1;">
                        <h6 class="mb-1"><?= h($item['product_name']) ?></h6>
                        <span style="font-size:13px;color:var(--gray-500);"><?= h($item['description']) ?></span>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:18px;font-weight:700;color:var(--primary);">¥<?= formatMoney($item['price']) ?></div>
                        <button class="btn btn-sm btn-outline-danger" onclick="removeCart(<?= $item['id'] ?>)">删除</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="col-lg-4">
                <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:24px;position:sticky;top:80px;">
                    <h6>购物车总计</h6>
                    <div style="font-size:28px;font-weight:800;color:var(--primary);margin:16px 0;">
                        ¥<?= formatMoney(array_sum(array_column($cartItems, 'price'))) ?>
                    </div>
                    <a href="/?route=checkout&action=checkout" class="btn btn-primary btn-lg w-100">去结算</a>
                    <a href="/?route=products" class="btn btn-outline w-100 mt-2">继续选购</a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script>
    function removeCart(id) {
        if (!confirm('确定移除？')) return;
        $.post('/api/product.php?action=remove_cart', { cart_id: id }, function(res) {
            if (res.code === 0) location.reload();
            else showToast(res.msg, 'danger');
        });
    }
    </script>
    <?php
}

elseif ($action === 'checkout') {
    $pageTitle = '结算';
    $orderNo = $_GET['order_no'] ?? '';
    $order = $db->getRow('SELECT o.*, p.name as product_name FROM orders o LEFT JOIN products p ON o.product_id = p.id WHERE o.order_no = :no AND o.user_id = :uid', ['no' => $orderNo, 'uid' => $userInfo['id']]);
    if (!$order) die('订单不存在');
    ?>
    <div class="container" style="padding-top:100px;padding-bottom:40px;">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div style="background:white;border-radius:12px;border:1px solid var(--gray-200);padding:32px;text-align:center;">
                    <i class="fas fa-receipt" style="font-size:48px;color:var(--primary);display:block;margin-bottom:16px;"></i>
                    <h5>订单确认</h5>
                    <div style="margin:20px 0;">
                        <div style="font-size:14px;color:var(--gray-500);">订单号：<code><?= h($order['order_no']) ?></code></div>
                        <div style="font-size:14px;color:var(--gray-500);">产品：<?= h($order['product_name']) ?></div>
                        <div style="font-size:32px;font-weight:800;color:var(--primary);margin:16px 0;">¥<?= formatMoney($order['total']) ?></div>
                    </div>
                    <form method="post" action="/?route=checkout&action=pay">
                        <input type="hidden" name="order_no" value="<?= h($order['order_no']) ?>">
                        <button type="submit" name="method" value="balance" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-wallet"></i> 余额支付
                        </button>
                        <p style="font-size:13px;color:var(--gray-400);margin-top:8px;">
                            当前余额：¥<?= formatMoney($userInfo['balance']) ?>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
}

elseif ($action === 'pay') {
    $orderNo = $_POST['order_no'] ?? '';
    $method = $_POST['method'] ?? '';
    $order = $db->getRow('SELECT * FROM orders WHERE order_no = :no AND user_id = :uid', ['no' => $orderNo, 'uid' => $userInfo['id']]);
    if (!$order || $order['status'] !== 'pending') die('订单状态异常');
    
    if ($method === 'balance') {
        if ($userInfo['balance'] < $order['total']) {
            echo '<script>alert("余额不足");history.back();</script>'; exit;
        }
        $db->beginTransaction();
        try {
            $newBalance = $userInfo['balance'] - $order['total'];
            $db->update('users', ['balance' => $newBalance], 'id = :id', ['id' => $userInfo['id']]);
            $db->update('orders', ['status' => 'paid', 'payment_time' => date('Y-m-d H:i:s'), 'payment_method' => 'balance'], 'id = :id', ['id' => $order['id']]);
            // 自动开通
            $db->update('orders', ['status' => 'active', 'active_time' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $order['id']]);
            $db->insert('hosts', [
                'user_id' => $userInfo['id'],
                'order_id' => $order['id'],
                'product_id' => $order['product_id'],
                'status' => 'active',
                'first_payment' => $order['total'],
                'billing_cycle' => $order['cycle'],
                'active_time' => date('Y-m-d H:i:s'),
            ]);
            $db->commit();
            echo '<script>alert("支付成功！产品已开通！");location.href="/?route=user&action=hosts";</script>';
        } catch (Exception $e) {
            $db->rollBack();
            echo '<script>alert("支付失败");history.back();</script>';
        }
        exit;
    }
    echo '<script>alert("不支持的支付方式");history.back();</script>';
    exit;
}
