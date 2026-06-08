<?php
if (!defined("IN_VZYUNIDC")) { http_response_code(403); exit; }
/**
 * vzyunIDC - 产品API接口
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$db = DB::instance();

switch ($action) {
    case 'list':
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $where = 'status = 1';
        $params = [];
        if ($categoryId > 0) {
            $where .= ' AND category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        $result = $db->paginate('products', $where, $params, 'sort ASC, id DESC', $page);
        jsonSuccess($result);
        break;

    case 'detail':
        $id = (int)($_GET['id'] ?? 0);
        $product = $db->getRow('SELECT p.*, pc.name as category_name FROM products p LEFT JOIN product_categories pc ON p.category_id = pc.id WHERE p.id = :id AND p.status = 1', ['id' => $id]);
        if (!$product) jsonError('产品不存在');
        jsonSuccess($product);
        break;

    case 'add_cart':
        $user = requireUser();
        $productId = (int)($_POST['product_id'] ?? 0);
        if (!$productId) jsonError('参数错误');
        
        $product = $db->getRow('SELECT id, name, price FROM products WHERE id = :id AND status = 1', ['id' => $productId]);
        if (!$product) jsonError('产品不存在');

        // 检查是否已在购物车
        $exists = $db->getOne('SELECT id FROM carts WHERE user_id = :user_id AND product_id = :product_id', ['user_id' => $user['id'], 'product_id' => $productId]);
        if ($exists) {
            jsonError('该产品已在购物车中');
        }

        $db->insert('carts', [
            'user_id' => $user['id'],
            'product_id' => $productId,
        ]);
        
        Hook::action('action_cart_add_after', $user['id'], $productId);
        jsonSuccess([], '已加入购物车');
        break;

    case 'remove_cart':
        $user = requireUser();
        $cartId = (int)($_POST['cart_id'] ?? 0);
        $db->delete('carts', 'id = :id AND user_id = :user_id', ['id' => $cartId, 'user_id' => $user['id']]);
        jsonSuccess([], '已移除');
        break;

    case 'cart_list':
        $user = requireUser();
        $items = $db->getRows(
            'SELECT c.*, p.name, p.price, p.cycle, p.type, p.stock FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id ORDER BY c.created_at DESC',
            ['user_id' => $user['id']]
        );
        jsonSuccess($items);
        break;

    default:
        jsonError('未知操作');
}
