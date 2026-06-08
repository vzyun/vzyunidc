<?php
if (!defined("IN_VZYUNIDC")) { http_response_code(403); exit; }
/**
 * vzyunIDC - 认证API接口
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/init.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? '';
$auth = Auth::instance();

switch ($action) {
    case 'login':
        handleLogin($auth);
        break;
    case 'register':
        handleRegister($auth);
        break;
    case 'logout':
        $auth->logout();
        jsonSuccess([], '已退出登录');
        break;
    case 'userinfo':
        $user = requireUser();
        jsonSuccess([
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'balance' => $user['balance'],
            'avatar' => $user['avatar'],
            'created_at' => $user['created_at'],
        ]);
        break;
    default:
        jsonError('未知操作');
}

function handleLogin($auth) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    if (empty($username) || empty($password)) {
        jsonError('请填写用户名和密码');
    }

    $result = $auth->userLogin($username, $password, $remember);
    if ($result['success']) {
        jsonSuccess(['token' => $result['token']], '登录成功');
    } else {
        jsonError($result['msg']);
    }
}

function handleRegister($auth) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // 验证
    if (empty($username) || empty($email) || empty($password)) {
        jsonError('请填写必要信息');
    }
    if (!preg_match('/^[a-zA-Z0-9_]{3,32}$/', $username)) {
        jsonError('用户名仅支持字母、数字、下划线，3-32位');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('邮箱格式不正确');
    }
    if (strlen($password) < 6) {
        jsonError('密码至少6位');
    }
    if ($password !== $confirmPassword) {
        jsonError('两次密码不一致');
    }
    if ($phone && !preg_match('/^1[3-9]\d{9}$/', $phone)) {
        jsonError('手机号格式不正确');
    }

    $db = DB::instance();

    // 检查重复
    if ($db->getOne('SELECT COUNT(*) FROM users WHERE username = :username', ['username' => $username])) {
        jsonError('用户名已存在');
    }
    if ($db->getOne('SELECT COUNT(*) FROM users WHERE email = :email', ['email' => $email])) {
        jsonError('邮箱已注册');
    }
    if ($phone && $db->getOne('SELECT COUNT(*) FROM users WHERE phone = :phone', ['phone' => $phone])) {
        jsonError('手机号已注册');
    }

    // 注册
    $userId = $db->insert('users', [
        'username' => $username,
        'email' => $email,
        'phone' => $phone,
        'password' => $auth->hashPassword($password),
        'reg_ip' => $auth->getClientIp(),
        'api_token' => $auth->generateToken(),
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    Hook::action('action_user_register_after', $userId, $username);

    jsonSuccess(['user_id' => $userId], '注册成功');
}
