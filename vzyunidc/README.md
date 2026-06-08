# vzyunIDC - IDC财务管理系统

> 一个轻量级的 IDC 财务管理系统，参考智简魔方财务系统开发。
> 适合虚拟主机、VPS、独立服务器等 IDC 业务的订单与财务管理。

## 📋 技术栈

| 组件 | 技术 |
|------|------|
| 后端 | PHP（原生） |
| 数据库 | MySQL |
| 前端 | Bootstrap 5 + jQuery |
| UI | AdminLTE 风格管理后台 |

## ✨ 功能特性

### 用户端
- 🔐 用户注册 / 登录（密码加密存储）
- 👤 用户中心（个人信息、余额、订单列表）
- 🛒 产品浏览与购物车下单
- 💳 在线支付（支付宝对接）
- 📋 订单管理（查看状态、续费、取消）
- 🎫 工单提交与沟通

### 管理端
- 📊 仪表盘（今日订单、收入统计、用户数）
- 📦 产品管理（分类 / 添加 / 编辑 / 上下架）
- 📑 订单管理（查看 / 处理 / 开通 / 退款）
- 👥 用户管理（查看 / 编辑 / 禁用）
- ⚙️ 系统设置（站点信息、支付配置）

## 🚀 快速开始

### 环境要求
- PHP 7.4+
- MySQL 5.7+
- Nginx / Apache

### 安装步骤

```bash
# 1. 克隆仓库
git clone https://github.com/vzyun/vzyunidc.git
cd vzyunidc

# 2. 配置数据库
# 创建 MySQL 数据库，导入 install/database.sql

# 3. 修改配置
# 编辑 includes/config.php，填入数据库信息

# 4. 配置 Web 服务器
# Nginx/Apache 指向项目根目录

# 5. 访问安装向导
# http://你的域名/install.php
```

## 📁 目录结构

```
vzyunidc/
├── admin/                  # 管理后台
│   ├── index.php           # 后台登录
│   ├── dashboard.php       # 仪表盘
│   ├── products.php        # 产品管理
│   ├── orders.php          # 订单管理
│   ├── users.php           # 用户管理
│   └── settings.php        # 系统设置
├── api/                    # API 接口
│   ├── auth.php            # 认证接口
│   ├── product.php         # 产品接口
│   └── order.php           # 订单接口
├── assets/                 # 静态资源
│   ├── css/
│   ├── js/
│   └── images/
├── includes/               # 核心库
│   ├── config.php          # 数据库配置
│   ├── db.php              # 数据库操作
│   ├── functions.php       # 公共函数
│   └── auth.php            # 权限认证
├── templates/              # 用户端模板
│   └── default/
├── uploads/                # 上传文件
├── install/                # 安装文件
│   └── database.sql        # 数据库结构
├── install.php             # 安装向导
├── index.php               # 用户端入口
├── .htaccess               # URL 重写
└── README.md               # 本文件
```

## 🗓️ 开发计划

| 阶段 | 内容 | 状态 |
|------|------|------|
| 第一天 | 基础框架 + 用户系统 | ⏳ 进行中 |
| 第二天 | 产品管理 + 购物车 | ⏳ 待开始 |
| 第三天 | 订单系统 + 管理后台 | ⏳ 待开始 |
| 第四天 | 支付对接 + 工单系统 | ⏳ 待开始 |

## 📜 开源协议

MIT License

## 👨‍💻 开发者

- [vzyun](https://github.com/vzyun) — 项目发起人
- [Neo](https://github.com/vzyun/vzyunidc) — AI 开发助手

---

> ⚡ 项目管理与开发记录由 [Neo](https://github.com/vzyun/vzyunidc) 维护
