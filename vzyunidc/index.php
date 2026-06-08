<?php
/**
 * vzyunIDC - 用户端入口
 */

// 加载配置
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/init.php';

// 加载Hook
Hook::loadPlugins();

// 路由分发
$route = $_GET['route'] ?? 'index';

switch ($route) {
    case 'index':
        $theme->render('index', [
            'products' => getRecommendedProducts(),
            'categories' => getAllCategories(),
        ]);
        break;
        
    case 'login':
        $theme->render('login');
        break;
        
    case 'register':
        $theme->render('register');
        break;
        
    case 'products':
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $products = getProducts($categoryId, $page);
        $categories = getAllCategories();
        $theme->render('products', [
            'products' => $products,
            'categories' => $categories,
            'currentCategory' => $categoryId,
        ]);
        break;
        
    case 'product':
        $id = (int)($_GET['id'] ?? 0);
        $product = getProduct($id);
        if (!$product) {
            redirect('/?route=products');
        }
        $theme->render('product', ['product' => $product]);
        break;
        
    case 'cart':
        $user = requireUser();
        $carts = getCartItems($user['id']);
        $theme->render('cart', ['carts' => $carts]);
        break;
        
    case 'checkout':
        $user = requireUser();
        $theme->render('checkout', ['user' => $user]);
        break;
        
    case 'user':
        $user = requireUser();
        $action = $_GET['action'] ?? 'dashboard';
        $theme->render('user/index', ['user' => $user, 'action' => $action]);
        break;
        
    case 'tickets':
        $user = requireUser();
        require __DIR__ . '/tickets.php';
        exit;
        
    case 'logout':
        Auth::instance()->logout();
        redirect('/');
        break;
        
    default:
        $theme->render('index');
}

/**
 * 获取推荐产品
 */
function getRecommendedProducts() {
    $db = DB::instance();
    return $db->getRows('SELECT * FROM products WHERE status = 1 AND recommended = 1 ORDER BY sort ASC, id DESC LIMIT 8');
}

/**
 * 获取所有分类
 */
function getAllCategories() {
    $db = DB::instance();
    return $db->getRows('SELECT * FROM product_categories WHERE status = 1 ORDER BY sort ASC, id ASC');
}

/**
 * 获取产品列表
 */
function getProducts($categoryId = 0, $page = 1) {
    $db = DB::instance();
    $where = 'status = 1';
    $params = [];
    if ($categoryId > 0) {
        $where .= ' AND category_id = :category_id';
        $params['category_id'] = $categoryId;
    }
    return $db->paginate('products', $where, $params, 'sort ASC, id DESC', $page);
}

/**
 * 获取产品详情
 */
function getProduct($id) {
    $db = DB::instance();
    return $db->getRow('SELECT p.*, pc.name as category_name FROM products p LEFT JOIN product_categories pc ON p.category_id = pc.id WHERE p.id = :id AND p.status = 1 LIMIT 1', ['id' => $id]);
}

/**
 * 获取购物车
 */
function getCartItems($userId) {
    $db = DB::instance();
    return $db->getRows(
        'SELECT c.*, p.name, p.price, p.cycle, p.type FROM carts c JOIN products p ON c.product_id = p.id WHERE c.user_id = :user_id ORDER BY c.created_at DESC',
        ['user_id' => $userId]
    );
}
