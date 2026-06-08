-- vzyunIDC Database Structure
-- MySQL 5.7+ / 8.0+

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 管理员表
-- ----------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码hash',
  `email` varchar(128) NOT NULL DEFAULT '' COMMENT '邮箱',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `role` varchar(32) NOT NULL DEFAULT 'admin' COMMENT '角色 super/admin/operator',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1正常 0禁用',
  `last_login_ip` varchar(45) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- ----------------------------
-- 用户表
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码hash',
  `email` varchar(128) NOT NULL DEFAULT '' COMMENT '邮箱',
  `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '头像',
  `realname` varchar(32) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `idcard` varchar(18) NOT NULL DEFAULT '' COMMENT '身份证号',
  `certification` tinyint(1) NOT NULL DEFAULT 0 COMMENT '实名状态 0未认证 1已认证 2审核中',
  `balance` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额',
  `credit` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '信用额度',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1正常 0禁用',
  `api_token` varchar(64) NOT NULL DEFAULT '' COMMENT 'API Token',
  `last_login_ip` varchar(45) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后登录时间',
  `reg_ip` varchar(45) NOT NULL DEFAULT '' COMMENT '注册IP',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`),
  UNIQUE KEY `uk_phone` (`phone`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- 产品分类表
-- ----------------------------
DROP TABLE IF EXISTS `product_categories`;
CREATE TABLE `product_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '分类名称',
  `slug` varchar(64) NOT NULL DEFAULT '' COMMENT '分类别名',
  `description` text COMMENT '分类描述',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序 越小越前',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1显示 0隐藏',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品分类表';

-- ----------------------------
-- 产品表
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '分类ID',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '产品名称',
  `description` text COMMENT '产品描述',
  `content` longtext COMMENT '产品详情HTML',
  `type` varchar(32) NOT NULL DEFAULT 'other' COMMENT '产品类型 vps/host/server/domain/other',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '价格',
  `renew_price` decimal(10,2) DEFAULT NULL COMMENT '续费价（NULL=同原价）',
  `setup_fee` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '初装费',
  `cycle` varchar(32) NOT NULL DEFAULT 'monthly' COMMENT '周期 monthly/quarterly/semiannually/annually/biennially/triennially',
  `stock` int(11) NOT NULL DEFAULT -1 COMMENT '库存 -1不限',
  `sort` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1上架 0下架',
  `recommended` tinyint(1) NOT NULL DEFAULT 0 COMMENT '推荐 1是 0否',
  `config_options` text COMMENT '配置选项JSON',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status_sort` (`status`,`sort`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品表';

-- ----------------------------
-- 购物车表
-- ----------------------------
DROP TABLE IF EXISTS `carts`;
CREATE TABLE `carts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `product_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '产品ID',
  `config_options` text COMMENT '配置选项JSON',
  `qty` int(11) NOT NULL DEFAULT 1 COMMENT '数量',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_user_product` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车表';

-- ----------------------------
-- 优惠码表
-- ----------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL DEFAULT '' COMMENT '优惠码',
  `type` varchar(16) NOT NULL DEFAULT 'percent' COMMENT '类型 percent固定百分比 amount固定金额',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '折扣值',
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '最低订单金额',
  `max_uses` int(11) NOT NULL DEFAULT 0 COMMENT '最大使用次数 0不限',
  `used_count` int(11) NOT NULL DEFAULT 0 COMMENT '已使用次数',
  `start_date` date DEFAULT NULL COMMENT '有效期开始',
  `end_date` date DEFAULT NULL COMMENT '有效期结束',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='优惠码表';

-- ----------------------------
-- 订单表
-- ----------------------------
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_no` varchar(32) NOT NULL DEFAULT '' COMMENT '订单号',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `product_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '产品ID',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '订单金额',
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '折扣金额',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '实付金额',
  `coupon_id` int(11) unsigned DEFAULT NULL COMMENT '优惠码ID',
  `config_options` text COMMENT '配置选项JSON',
  `cycle` varchar(32) NOT NULL DEFAULT 'monthly' COMMENT '购买周期',
  `billing_cycle` int(11) NOT NULL DEFAULT 1 COMMENT '计费周期(月数)',
  `status` varchar(16) NOT NULL DEFAULT 'pending' COMMENT '状态 pending待支付 paid已支付 active已开通 suspended已暂停 cancelled已取消 refunded已退款',
  `payment_method` varchar(32) NOT NULL DEFAULT '' COMMENT '支付方式 alipay/wxpay/manual',
  `payment_time` datetime DEFAULT NULL COMMENT '支付时间',
  `active_time` datetime DEFAULT NULL COMMENT '开通时间',
  `expire_time` datetime DEFAULT NULL COMMENT '到期时间',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `ip` varchar(45) NOT NULL DEFAULT '' COMMENT '下单IP',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_order_no` (`order_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_user_status` (`user_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
-- 用户产品表（已购产品）
-- ----------------------------
DROP TABLE IF EXISTS `hosts`;
CREATE TABLE `hosts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `order_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '订单ID',
  `product_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '产品ID',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '产品名称/标识',
  `domain` varchar(255) NOT NULL DEFAULT '' COMMENT '域名/IP',
  `config_options` text COMMENT '配置选项JSON',
  `username` varchar(64) NOT NULL DEFAULT '' COMMENT '管理用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '管理密码（加密）',
  `status` varchar(16) NOT NULL DEFAULT 'pending' COMMENT '状态 pending待开通 active运行中 suspended已暂停 terminated已删除',
  `dedicated_ip` varchar(45) NOT NULL DEFAULT '' COMMENT '独立IP',
  `os` varchar(64) NOT NULL DEFAULT '' COMMENT '操作系统',
  `first_payment` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '首付金额',
  `renewal_price` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '续费价格',
  `billing_cycle` varchar(32) NOT NULL DEFAULT 'monthly' COMMENT '计费周期',
  `next_due_date` date DEFAULT NULL COMMENT '下次续费日期',
  `active_time` datetime DEFAULT NULL COMMENT '开通时间',
  `suspend_time` datetime DEFAULT NULL COMMENT '暂停时间',
  `terminate_time` datetime DEFAULT NULL COMMENT '删除时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_status` (`status`),
  KEY `idx_next_due` (`next_due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户产品表';

-- ----------------------------
-- 工单表
-- ----------------------------
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE `tickets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_no` varchar(32) NOT NULL DEFAULT '' COMMENT '工单编号',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `admin_id` int(11) unsigned DEFAULT NULL COMMENT '处理管理员ID',
  `host_id` int(11) unsigned DEFAULT NULL COMMENT '关联产品ID',
  `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `priority` varchar(16) NOT NULL DEFAULT 'medium' COMMENT '优先级 low/medium/high/urgent',
  `status` varchar(16) NOT NULL DEFAULT 'pending' COMMENT '状态 pending待回复 replied已回复 customer_replied客户已回复 closed已关闭',
  `last_reply_time` datetime DEFAULT NULL COMMENT '最后回复时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ticket_no` (`ticket_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_admin` (`admin_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='工单表';

-- ----------------------------
-- 工单回复表
-- ----------------------------
DROP TABLE IF EXISTS `ticket_replies`;
CREATE TABLE `ticket_replies` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '工单ID',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '回复用户ID（NULL=管理员）',
  `admin_id` int(11) unsigned DEFAULT NULL COMMENT '回复管理员ID',
  `content` text COMMENT '回复内容',
  `attachments` text COMMENT '附件JSON',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ticket` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='工单回复表';

-- ----------------------------
-- 支付记录表
-- ----------------------------
DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `trade_no` varchar(64) NOT NULL DEFAULT '' COMMENT '本地交易号',
  `out_trade_no` varchar(128) NOT NULL DEFAULT '' COMMENT '第三方交易号',
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `order_id` int(11) unsigned DEFAULT NULL COMMENT '订单ID',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '金额',
  `method` varchar(32) NOT NULL DEFAULT '' COMMENT '支付方式',
  `type` varchar(16) NOT NULL DEFAULT 'order' COMMENT '类型 order订单支付 recharge余额充值',
  `status` varchar(16) NOT NULL DEFAULT 'pending' COMMENT '状态 pending待支付 success成功 failed失败 refunded已退款',
  `pay_time` datetime DEFAULT NULL COMMENT '支付时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_trade_no` (`trade_no`),
  KEY `idx_out_trade_no` (`out_trade_no`),
  KEY `idx_user` (`user_id`),
  KEY `idx_order` (`order_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='支付记录表';

-- ----------------------------
-- 消息通知表
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID 0=系统公告',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `type` varchar(32) NOT NULL DEFAULT 'system' COMMENT '类型 system公告 order订单 ticket工单',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '已读 1是 0否',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_user_read` (`user_id`,`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='消息通知表';

-- ----------------------------
-- 系统设置表
-- ----------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) NOT NULL DEFAULT '' COMMENT '配置键',
  `value` text COMMENT '配置值',
  `group` varchar(32) NOT NULL DEFAULT 'system' COMMENT '分组 system/mail/sms/payment/security',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_key` (`key`),
  KEY `idx_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统设置表';

-- ----------------------------
-- 操作日志表
-- ----------------------------
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '日志类型 login/order/ticket/admin',
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '操作用户ID',
  `admin_id` int(11) unsigned DEFAULT NULL COMMENT '操作管理员ID',
  `action` varchar(64) NOT NULL DEFAULT '' COMMENT '操作描述',
  `detail` text COMMENT '详细内容',
  `ip` varchar(45) NOT NULL DEFAULT '' COMMENT '操作IP',
  `user_agent` varchar(500) NOT NULL DEFAULT '' COMMENT 'UserAgent',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_user` (`user_id`),
  KEY `idx_admin` (`admin_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作日志表';

-- ----------------------------
-- 插件表
-- ----------------------------
DROP TABLE IF EXISTS `plugins`;
CREATE TABLE `plugins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '插件标识',
  `title` varchar(128) NOT NULL DEFAULT '' COMMENT '插件名称',
  `version` varchar(32) NOT NULL DEFAULT '1.0' COMMENT '版本',
  `author` varchar(64) NOT NULL DEFAULT '' COMMENT '作者',
  `description` text COMMENT '描述',
  `config` text COMMENT '配置JSON',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态 1启用 0禁用',
  `installed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件表';

-- ----------------------------
-- Hook注册表
-- ----------------------------
DROP TABLE IF EXISTS `hooks`;
CREATE TABLE `hooks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `hook_name` varchar(64) NOT NULL DEFAULT '' COMMENT 'Hook名称',
  `plugin_name` varchar(64) NOT NULL DEFAULT '' COMMENT '插件标识',
  `priority` int(11) NOT NULL DEFAULT 10 COMMENT '优先级',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态 1启用 0禁用',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_hook_plugin` (`hook_name`,`plugin_name`),
  KEY `idx_hook_name` (`hook_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Hook注册表';

-- ----------------------------
-- 登录令牌表（JWT黑名单）
-- ----------------------------
DROP TABLE IF EXISTS `tokens`;
CREATE TABLE `tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `token` varchar(255) NOT NULL DEFAULT '' COMMENT 'Token',
  `type` varchar(16) NOT NULL DEFAULT 'user' COMMENT '类型 user/admin',
  `expires_at` datetime NOT NULL COMMENT '过期时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_token` (`token`(191)),
  KEY `idx_user` (`user_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='登录令牌表';

-- ----------------------------
-- 默认数据
-- ----------------------------

-- 默认管理员账号: admin / vzyun2026
INSERT INTO `admins` (`username`, `password`, `email`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@vzyun.com', 'super', 1);

-- 默认系统设置
INSERT INTO `settings` (`key`, `value`, `group`) VALUES
('site_name', 'vzyunIDC', 'system'),
('site_url', '', 'system'),
('site_logo', '', 'system'),
('site_description', '专业的IDC业务管理系统', 'system'),
('site_keywords', 'IDC,云服务器,VPS,主机,财务系统', 'system'),
('site_icp', '', 'system'),
('site_status', '1', 'system'),
('site_close_reason', '系统维护中，请稍后再试...', 'system'),
('register_enabled', '1', 'system'),
('register_verify_email', '0', 'system'),
('register_verify_phone', '0', 'system'),
('currency_code', 'CNY', 'system'),
('currency_symbol', '¥', 'system'),
('currency_precision', '2', 'system'),
('timezone', 'Asia/Shanghai', 'system'),
('date_format', 'Y-m-d', 'system'),
('admin_path', 'admin', 'security'),
('default_theme', 'default', 'system'),
('mail_type', 'smtp', 'mail'),
('mail_smtp_host', '', 'mail'),
('mail_smtp_port', '465', 'mail'),
('mail_smtp_user', '', 'mail'),
('mail_smtp_pass', '', 'mail'),
('mail_smtp_encryption', 'ssl', 'mail'),
('mail_from_address', '', 'mail'),
('mail_from_name', 'vzyunIDC', 'mail'),
('alipay_enabled', '0', 'payment'),
('alipay_app_id', '', 'payment'),
('alipay_private_key', '', 'payment'),
('alipay_public_key', '', 'payment'),
('wxpay_enabled', '0', 'payment'),
('wxpay_app_id', '', 'payment'),
('wxpay_mch_id', '', 'payment'),
('wxpay_key', '', 'payment');

-- 默认产品分类
INSERT INTO `product_categories` (`name`, `slug`, `description`, `icon`, `sort`) VALUES
('云服务器', 'cloud-server', '高性能云服务器，弹性扩展，按需付费', 'fa-cloud', 1),
('物理服务器', 'dedicated-server', '高性能物理服务器，独享资源', 'fa-server', 2),
('虚拟主机', 'web-hosting', '稳定可靠的企业级虚拟主机', 'fa-home', 3),
('域名服务', 'domain', '国内外域名注册与管理', 'fa-globe', 4);

SET FOREIGN_KEY_CHECKS = 1;
