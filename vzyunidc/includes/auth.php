<?php
/**
 * vzyunIDC - 认证授权类
 * 支持 Session 和 JWT Token 两种认证方式
 */

class Auth {
    private static $instance = null;
    private $db;

    private function __construct() {
        $this->db = DB::instance();
    }

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 密码加密
     */
    public function hashPassword($password) {
        return password_hash($password . AUTH_KEY, PASSWORD_BCRYPT);
    }

    /**
     * 验证密码
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password . AUTH_KEY, $hash);
    }

    /**
     * 用户登录
     */
    public function userLogin($username, $password, $remember = false) {
        $user = $this->db->getRow(
            'SELECT * FROM users WHERE (username = :username OR email = :email) AND status = 1 LIMIT 1',
            ['username' => $username, 'email' => $username]
        );
        if (!$user || !$this->verifyPassword($password, $user['password'])) {
            return ['success' => false, 'msg' => '用户名或密码错误'];
        }
        
        // 更新登录信息
        $ip = $this->getClientIp();
        $this->db->update('users', [
            'last_login_ip' => $ip,
            'last_login_time' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $user['id']]);

        // 记录日志
        $this->log('login', $user['id'], null, '用户登录', $ip);

        // 生成Token
        $token = $this->generateToken();
        $expire = $remember ? time() + 604800 : time() + TOKEN_EXPIRE;
        
        // Session登录
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_token'] = $token;
        $_SESSION['user_expire'] = $expire;

        // 保存Token到数据库（用于API认证）
        $this->db->insert('tokens', [
            'user_id' => $user['id'],
            'token' => $token,
            'type' => 'user',
            'expires_at' => date('Y-m-d H:i:s', $expire),
        ]);

        return ['success' => true, 'user' => $user, 'token' => $token];
    }

    /**
     * 管理员登录
     */
    public function adminLogin($username, $password) {
        $admin = $this->db->getRow(
            'SELECT * FROM admins WHERE username = :username AND status = 1 LIMIT 1',
            ['username' => $username]
        );
        if (!$admin || !$this->verifyPassword($password, $admin['password'])) {
            return ['success' => false, 'msg' => '用户名或密码错误'];
        }

        $ip = $this->getClientIp();
        $this->db->update('admins', [
            'last_login_ip' => $ip,
            'last_login_time' => date('Y-m-d H:i:s'),
        ], 'id = :id', ['id' => $admin['id']]);

        $this->log('admin_login', null, $admin['id'], '管理员登录', $ip);

        $token = $this->generateToken();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_token'] = $token;

        return ['success' => true, 'admin' => $admin, 'token' => $token];
    }

    /**
     * 验证用户登录
     */
    public function checkUser() {
        if (isset($_SESSION['user_id'])) {
            $user = $this->db->getRow('SELECT * FROM users WHERE id = :id AND status = 1 LIMIT 1', ['id' => $_SESSION['user_id']]);
            if ($user) {
                return $user;
            }
        }
        // API Token验证
        $token = $this->getBearerToken();
        if ($token) {
            $tokenData = $this->db->getRow(
                'SELECT t.*, u.* FROM tokens t JOIN users u ON t.user_id = u.id WHERE t.token = :token AND t.type = :type AND t.expires_at > NOW() AND u.status = 1 LIMIT 1',
                ['token' => $token, 'type' => 'user']
            );
            if ($tokenData) {
                return $tokenData;
            }
        }
        return null;
    }

    /**
     * 验证管理员登录
     */
    public function checkAdmin() {
        if (isset($_SESSION['admin_id'])) {
            $admin = $this->db->getRow('SELECT * FROM admins WHERE id = :id AND status = 1 LIMIT 1', ['id' => $_SESSION['admin_id']]);
            if ($admin) {
                return $admin;
            }
        }
        $token = $this->getBearerToken();
        if ($token) {
            $tokenData = $this->db->getRow(
                'SELECT t.*, a.* FROM tokens t JOIN admins a ON t.user_id = a.id WHERE t.token = :token AND t.type = :type AND t.expires_at > NOW() AND a.status = 1 LIMIT 1',
                ['token' => $token, 'type' => 'admin']
            );
            if ($tokenData) {
                return $tokenData;
            }
        }
        return null;
    }

    /**
     * 退出登录
     */
    public function logout($type = 'user') {
        if ($type === 'admin') {
            unset($_SESSION['admin_id'], $_SESSION['admin_token']);
        } else {
            unset($_SESSION['user_id'], $_SESSION['user_token'], $_SESSION['user_expire']);
        }
    }

    /**
     * 生成随机Token
     */
    public function generateToken($length = 64) {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * 获取Bearer Token
     */
    private function getBearerToken() {
        $headers = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $headers = $requestHeaders['Authorization'];
            }
        }
        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * 获取客户端IP
     */
    public function getClientIp() {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
            return $_SERVER['HTTP_X_REAL_IP'];
        }
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * 记录日志
     */
    public function log($type, $userId = null, $adminId = null, $action = '', $ip = null, $detail = null) {
        $this->db->insert('logs', [
            'type' => $type,
            'user_id' => $userId,
            'admin_id' => $adminId,
            'action' => $action,
            'detail' => $detail,
            'ip' => $ip ?: $this->getClientIp(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
