# vzyunIDC - 项目规划

## 技术栈
- **后端**: PHP 原生（轻量无框架，便于理解和学习）
- **数据库**: MySQL
- **前端**: HTML + CSS + Bootstrap 5 + jQuery
- **模板引擎**: PHP原生混编

## 目录结构
```
vzyunidc/
├── admin/               # 管理后台
│   ├── index.php        # 后台登录页
│   ├── dashboard.php    # 后台仪表盘
│   ├── products.php     # 产品管理
│   ├── orders.php       # 订单管理
│   ├── users.php        # 用户管理
│   └── settings.php     # 系统设置
├── api/                 # API接口
│   ├── auth.php         # 登录注册接口
│   ├── product.php      # 产品接口
│   └── order.php        # 订单接口
├── assets/              # 静态资源
│   ├── css/
│   ├── js/
│   └── images/
├── includes/            # 公共库
│   ├── config.php       # 数据库配置
│   ├── db.php           # 数据库操作类
│   ├── functions.php    # 公共函数
│   └── auth.php         # 认证类
├── templates/           # 模板
│   └── default/         # 用户端模板
├── install.php          # 安装向导
├── index.php            # 用户端入口
└── .htaccess            # URL重写
```

## 阶段计划（高考假期版）

### 第一天 - 基础框架 + 用户系统 ✅
- [x] 项目结构搭建
- [x] GitHub仓库配置
- [ ] MySQL数据库设计（users表、products表、orders表）
- [ ] 配置文件 config.php
- [ ] 用户注册/登录（含验证码）
- [ ] 用户中心首页

### 第二天 - 产品管理 + 购物车
- [ ] 产品分类管理
- [ ] 产品添加/编辑/上架/下架
- [ ] 产品前台展示列表
- [ ] 购物车功能（加入/删除/结算）

### 第三天 - 订单系统 + 管理后台
- [ ] 订单创建/支付流程
- [ ] 订单状态管理（待支付/已支付/开通中/已开通/已取消）
- [ ] 管理后台仪表盘
- [ ] 管理后台订单管理

### 第四天 - 支付对接 + 优化
- [ ] 支付接口（支付宝当面付/PC支付）
- [ ] 产品开通自动化流程
- [ ] 工单系统（简单的工单提交与回复）
- [ ] 部署文档编写

### 后续迭代
- 实名认证
- 发票管理
- 多语言支持
- API对接外部系统
- 财务管理报表

## 数据库设计（初版）

### users 用户表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | int(11) | 主键 |
| username | varchar(32) | 用户名 |
| email | varchar(64) | 邮箱 |
| password | varchar(255) | 密码(加密) |
| balance | decimal(10,2) | 账户余额 |
| status | tinyint(1) | 状态 |
| created_at | datetime | 注册时间 |

### products 产品表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | int(11) | 主键 |
| name | varchar(128) | 产品名称 |
| description | text | 产品描述 |
| type | varchar(32) | 产品类型(vps/host/server) |
| price | decimal(10,2) | 价格 |
| cycle | varchar(16) | 周期(month/quarter/year) |
| stock | int(11) | 库存 |
| status | tinyint(1) | 上架状态 |
| created_at | datetime | 创建时间 |

### orders 订单表
| 字段 | 类型 | 说明 |
|------|------|------|
| id | int(11) | 主键 |
| order_no | varchar(32) | 订单号 |
| user_id | int(11) | 用户ID |
| product_id | int(11) | 产品ID |
| amount | decimal(10,2) | 金额 |
| status | varchar(16) | 状态(pending/paid/active/cancelled) |
| created_at | datetime | 创建时间 |
| paid_at | datetime | 支付时间 |
