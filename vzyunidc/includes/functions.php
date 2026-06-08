<?php
/**
 * vzyunIDC - 公共函数库
 */

/**
 * 输出JSON
 */
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * 成功响应
 */
function jsonSuccess($data = [], $msg = '操作成功') {
    jsonResponse(['code' => 0, 'msg' => $msg, 'data' => $data]);
}

/**
 * 错误响应
 */
function jsonError($msg = '操作失败', $code = 1) {
    jsonResponse(['code' => $code, 'msg' => $msg]);
}

/**
 * 重定向
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * 获取系统设置
 */
function getSetting($key, $default = '') {
    $db = DB::instance();
    $value = $db->getOne('SELECT `value` FROM settings WHERE `key` = :key LIMIT 1', ['key' => $key]);
    return $value !== false ? $value : $default;
}

/**
 * 获取所有设置
 */
function getAllSettings() {
    $db = DB::instance();
    $rows = $db->getRows('SELECT `key`, `value` FROM settings');
    $settings = [];
    foreach ($rows as $row) {
        $settings[$row['key']] = $row['value'];
    }
    return $settings;
}

/**
 * 更新设置
 */
function updateSetting($key, $value) {
    $db = DB::instance();
    $exists = $db->getOne('SELECT COUNT(*) FROM settings WHERE `key` = :key', ['key' => $key]);
    if ($exists) {
        $db->update('settings', ['value' => $value], '`key` = :key', ['key' => $key]);
    } else {
        $db->insert('settings', ['key' => $key, 'value' => $value]);
    }
}

/**
 * 生成订单号
 */
function generateOrderNo() {
    return date('YmdHis') . strtoupper(substr(uniqid(), -6));
}

/**
 * 生成工单编号
 */
function generateTicketNo() {
    return 'TK' . date('Ymd') . strtoupper(substr(uniqid(), -5));
}

/**
 * 金额格式化
 */
function formatMoney($amount) {
    return number_format($amount, 2, '.', '');
}

/**
 * 安全过滤
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * 截取字符串
 */
function strLimit($str, $length = 100, $suffix = '...') {
    if (mb_strlen($str) <= $length) return $str;
    return mb_substr($str, 0, $length) . $suffix;
}

/**
 * 时间友好显示
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    if ($diff < 60) return '刚刚';
    if ($diff < 3600) return floor($diff / 60) . '分钟前';
    if ($diff < 86400) return floor($diff / 3600) . '小时前';
    if ($diff < 2592000) return floor($diff / 86400) . '天前';
    return date('Y-m-d', $timestamp);
}

/**
 * 检查权限
 */
function requireUser() {
    $user = Auth::instance()->checkUser();
    if (!$user) {
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            jsonError('请先登录', 401);
        }
        redirect('/login.php');
    }
    return $user;
}

/**
 * 检查管理员权限
 */
function requireAdmin() {
    $admin = Auth::instance()->checkAdmin();
    if (!$admin) {
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            jsonError('请先登录', 401);
        }
        redirect('/admin/index.php');
    }
    return $admin;
}

/**
 * 分页HTML生成
 */
function paginationHtml($total, $page, $pageSize, $url) {
    $totalPages = ceil($total / $pageSize);
    if ($totalPages <= 1) return '';
    
    $html = '<nav><ul class="pagination justify-content-center">';
    
    // 上一页
    $prev = $page > 1 ? $page - 1 : 1;
    $html .= '<li class="page-item' . ($page <= 1 ? ' disabled' : '') . '"><a class="page-link" href="' . $url . '&page=' . $prev . '">上一页</a></li>';
    
    // 页码
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    if ($start > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=1">1</a></li>';
        if ($start > 2) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }
    for ($i = $start; $i <= $end; $i++) {
        $html .= '<li class="page-item' . ($i == $page ? ' active' : '') . '"><a class="page-link" href="' . $url . '&page=' . $i . '">' . $i . '</a></li>';
    }
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        $html .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // 下一页
    $next = $page < $totalPages ? $page + 1 : $totalPages;
    $html .= '<li class="page-item' . ($page >= $totalPages ? ' disabled' : '') . '"><a class="page-link" href="' . $url . '&page=' . $next . '">下一页</a></li>';
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * 发送邮件
 */
function sendMail($to, $subject, $content) {
    $mailType = getSetting('mail_type', 'smtp');
    if ($mailType === 'smtp') {
        return sendMailSmtp($to, $subject, $content);
    }
    return false;
}

function sendMailSmtp($to, $subject, $content) {
    require_once __DIR__ . '/mailer.php';
    $mailer = new Mailer();
    return $mailer->send($to, $subject, $content);
}

/**
 * 获取插件配置
 */
function getPluginConfig($pluginName) {
    $db = DB::instance();
    $row = $db->getRow('SELECT config FROM plugins WHERE name = :name LIMIT 1', ['name' => $pluginName]);
    if ($row && $row['config']) {
        return json_decode($row['config'], true);
    }
    return [];
}

/**
 * 设置插件配置
 */
function setPluginConfig($pluginName, $config) {
    $db = DB::instance();
    $db->update('plugins', ['config' => json_encode($config, JSON_UNESCAPED_UNICODE)], 'name = :name', ['name' => $pluginName]);
}
