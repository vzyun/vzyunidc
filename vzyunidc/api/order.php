<?php
if (!defined("IN_VZYUNIDC")) { http_response_code(403); exit; }
/**
 * vzyunIDC - 订单API接口
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$db = DB::instance();
$auth = Auth::instance();

switch ($action) {
    case 'create':
        $user = requireUser();
        $productId = (int)($_POST['product_id'] ?? 0);
        $cycle = $_POST['cycle'] ?? 'monthly';

        $product = $db->getRow('SELECT * FROM products WHERE id = :id AND status = 1', ['id' => $productId]);
        if (!$product) jsonError('产品不存在');

        $cyclePrices = $product['config_options'] ? json_decode($product['config_options'], true) : [];
        $price = $product['price'];
        
        $orderNo = generateOrderNo();
        $orderId = $db->insert('orders', [
            'order_no' => $orderNo,
            'user_id' => $user['id'],
            'product_id' => $productId,
            'amount' => $price,
            'total' => $price,
            'cycle' => $cycle,
            'status' => 'pending',
            'ip' => $auth->getClientIp(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        Hook::action('action_order_create_after', $orderId, $user['id'], $productId);

        jsonSuccess([
            'order_id' => $orderId,
            'order_no' => $orderNo,
            'amount' => $price,
        ], '订单创建成功');
        break;

    case 'list':
        $user = requireUser();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $status = $_GET['status'] ?? '';
        
        $where = 'user_id = :user_id';
        $params = ['user_id' => $user['id']];
        if ($status) {
            $where .= ' AND status = :status';
            $params['status'] = $status;
        }
        
        $result = $db->paginate('orders', $where, $params, 'created_at DESC', $page);
        jsonSuccess($result);
        break;

    case 'detail':
        $user = requireUser();
        $orderNo = $_GET['order_no'] ?? '';
        $order = $db->getRow('SELECT o.*, p.name as product_name FROM orders o LEFT JOIN products p ON o.product_id = p.id WHERE o.order_no = :order_no AND o.user_id = :user_id', ['order_no' => $orderNo, 'user_id' => $user['id']]);
        if (!$order) jsonError('订单不存在');
        jsonSuccess($order);
        break;

    case 'cancel':
        $user = requireUser();
        $orderNo = $_POST['order_no'] ?? '';
        $order = $db->getRow('SELECT * FROM orders WHERE order_no = :order_no AND user_id = :user_id', ['order_no' => $orderNo, 'user_id' => $user['id']]);
        if (!$order) jsonError('订单不存在');
        if ($order['status'] !== 'pending') jsonError('当前状态不可取消');

        $db->update('orders', ['status' => 'cancelled'], 'id = :id', ['id' => $order['id']]);
        Hook::action('action_order_cancel_after', $order['id'], $user['id']);
        jsonSuccess([], '订单已取消');
        break;

    default:
        jsonError('未知操作');
}
