# vzyunIDC - 开源IDC财务管理系统

> 轻量级、可扩展的IDC业务财务管理平台，参考智简魔方财务系统架构设计。
> 支持虚拟主机、VPS、独立服务器、域名等IDC产品的自动化运营管理。

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)
[![GitHub Stars](https://img.shields.io/github/stars/vzyun/vzyunidc)](https://github.com/vzyun/vzyunidc)

---

## 📋 目录

- [功能特性](#-功能特性)
- [系统架构](#-系统架构)
- [环境要求](#-环境要求)
- [快速安装](#-快速安装)
- [目录结构](#-目录结构)
- [插件系统](#-插件系统)
- [主题系统](#-主题系统)
- [API文档](#-api文档)
- [对接魔方财务](#-对接魔方财务)
- [开发计划](#-开发计划)
- [参与贡献](#-参与贡献)
- [开源协议](#-开源协议)

---

## ✨ 功能特性

### 💻 用户端
| 功能 | 说明 |
|------|------|
| 🔐 用户系统 | 注册/登录、密码加密、邮箱验证、找回密码 |
| 👤 用户中心 | 个人信息编辑、安全设置、API密钥管理 |
| 💰 财务管理 | 余额充值、消费记录、提现申请、发票管理 |
| 📦 产品中心 | 产品分类浏览、搜索筛选、详情查看、配置选择 |
| 🛒 购物车 | 加入购物车、批量结算、优惠码抵扣 |
| 📋 订单管理 | 订单创建、在线支付、查看状态、续费、取消 |
| 🎫 工单系统 | 工单提交、客服回复、状态跟踪、历史记录 |
| 📊 资源管理 | 已购产品管理、VNC管理、重装系统、重启关机 |
| 🔔 消息通知 | 站内消息、邮件通知、短信通知（需插件） |

### 🔧 管理端
| 功能 | 说明 |
|------|------|
| 📊 仪表盘 | 今日订单、收入统计、新增用户、产品数据概览 |
| 🏷️ 产品管理 | 分类管理、产品添加/编辑/上下架、定价、库存管理 |
| 📑 订单管理 | 订单查看、处理、开通、暂停、取消、退款 |
| 👥 用户管理 | 用户列表、编辑、禁用、余额调整、登录日志 |
| 📬 工单管理 | 工单分配、回复、状态管理、预设回复模板 |
| 🎨 主题管理 | 主题安装、切换、自定义配置 |
| 🔌 插件管理 | 插件安装/卸载、配置管理、钩子管理 |
| ⚙️ 系统设置 | 站点信息、支付配置、邮件配置、安全设置 |
| 📈 财务统计 | 收入统计、支出统计、财务报表导出 |
| 📝 日志系统 | 操作日志、登录日志、系统日志 |

### 🔌 可扩展系统
| 模块 | 说明 |
|------|------|
| **插件系统** | Hook机制，支持自定义功能扩展 |
| **主题系统** | 模板引擎驱动，支持自定义前端主题 |
| **短信接口** | 阿里云/腾讯云/七牛云短信通道（插件） |
| **实名认证** | 支付宝/微信实名、人工审核（插件） |
| **支付接口** | 支付宝当面付/PC支付、微信支付、PayPal（插件） |
| **邮件服务** | SMTP、SendCloud、阿里云邮件推送 |

---

## 🏗️ 系统架构

```
┌─────────────────────────────────────────────────┐
│                  用户访问层                      │
│  浏览器 / API客户端 / 魔方财务 / 第三方系统      │
└────────────────────┬────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────┐
│              路由层 (入口文件)                    │
│     index.php / admin/index.php / api/*          │
└────────────────────┬────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────┐
│              核心框架层                          │
│  ┌──────────┐ ┌──────────┐ ┌────────────────┐   │
│  │ 数据库层 │ │ 认证授权 │ │ 缓存/Session   │   │
│  │ MySQLi   │ │ JWT/Session │ │ 文件/Redis    │   │
│  └──────────┘ └──────────┘ └────────────────┘   │
│  ┌──────────┐ ┌──────────┐ ┌────────────────┐   │
│  │ Hook系统  │ │ 模板引擎 │ │ 错误/日志处理   │   │
│  └──────────┘ └──────────┘ └────────────────┘   │
└────────────────────┬────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────┐
│              业务模块层                          │
│ 用户 → 产品 → 购物车 → 订单 → 支付 → 开通      │
│ 工单 → 通知 → 财务 → 实名                      │
└────────────────────┬────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────┐
│              扩展层                              │
│  ┌──────────┐ ┌──────────┐ ┌────────────────┐   │
│  │ 插件市场 │ │ 主题市场 │ │ 第三方API对接   │   │
│  │ Hook注册 │ │ 模板继承 │ │ 魔方财务API    │   │
│  └──────────┘ └──────────┘ └────────────────┘   │
└─────────────────────────────────────────────────┘
```

---

## 📦 环境要求

| 组件 | 最低要求 | 推荐配置 |
|------|---------|---------|
| PHP | 7.4 | 8.1+ |
| MySQL | 5.7 | 8.0+ |
| Web服务器 | Nginx / Apache | Nginx 1.20+ |
| 内存 | 128MB | 512MB+ |
| 存储 | 100MB | 1GB+ |

### PHP 扩展要求
- PDO / MySQLi
- JSON
- CURL
- GD (验证码)
- OpenSSL
- Fileinfo
- MBString
- Zip (插件/主题安装)

---

## 🚀 快速安装

### 方式一：直接部署

```bash
# 1. 克隆代码
git clone https://github.com/vzyun/vzyunidc.git /var/www/vzyunidc
cd /var/www/vzyunidc

# 2. 创建数据库
mysql -u root -p -e "CREATE DATABASE vzyunidc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 3. 导入数据表
mysql -u root -p vzyunidc < install/database.sql

# 4. 配置权限
chmod -R 755 .
chmod -R 777 uploads/ cache/

# 5. 访问安装向导
# 浏览器打开 http://你的域名/install.php
# 按步骤填写数据库信息和管理员账号
```

### 方式二：Docker 部署（开发中）
```bash
docker run -d --name vzyunidc -p 80:80 vzyun/vzyunidc
```

### Nginx 配置示例

```nginx
server {
    listen 80;
    server_name idc.yourdomain.com;
    root /var/www/vzyunidc;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }
}
```

---

## 📁 完整目录结构

```
vzyunidc/
├── index.php                 # 用户端入口（路由分发）
├── install.php               # 安装向导
├── .htaccess                 # Apache URL重写
├── config.php                # 系统配置文件
│
├── admin/                    # ★ 管理后台
│   ├── index.php             #   后台登录页
│   ├── dashboard.php         #   仪表盘
│   ├── products.php          #   产品管理
│   ├── product_categories.php #   产品分类
│   ├── orders.php            #   订单管理
│   ├── users.php             #   用户管理
│   ├── tickets.php           #   工单管理
│   ├── plugins.php           #   插件管理
│   ├── themes.php            #   主题管理
│   ├── settings.php          #   系统设置
│   ├── finance.php           #   财务统计
│   ├── logs.php              #   系统日志
│   ├── sms_config.php        #   短信配置
│   ├── certification.php     #   实名认证
│   └── css/  js/             #   后台资源文件
│
├── includes/                 # ★ 核心库
│   ├── db.php                #   数据库操作类 (MySQLi/PDO)
│   ├── auth.php              #   权限认证类 (JWT + Session)
│   ├── functions.php         #   公共函数库
│   ├── config.php            #   数据库/系统配置
│   ├── hook.php              #   Hook钩子系统
│   ├── plugin.php            #   插件加载器
│   ├── theme.php             #   主题引擎
│   ├── template.php          #   模板渲染引擎
│   ├── pagination.php        #   分页类
│   ├── mailer.php            #   邮件发送类
│   ├── sms.php               #   短信接口类
│   ├── upload.php            #   文件上传类
│   ├── cache.php             #   缓存类
│   ├── log.php               #   日志类
│   └── validator.php         #   数据验证类
│
├── api/                      # ★ API接口
│   ├── v1/                   #   RESTful API v1
│   │   ├── auth.php          #     登录/注册
│   │   ├── user.php          #     用户信息
│   │   ├── product.php       #     产品列表
│   │   ├── order.php         #     订单操作
│   │   ├── ticket.php        #     工单操作
│   │   ├── payment.php       #     支付接口
│   │   └── reseller.php      #     代理商对接
│   └── index.php             #   API路由入口
│
├── modules/                  # 业务模块
│   ├── user/                 #   用户模块
│   ├── product/              #   产品模块
│   ├── order/                #   订单模块
│   ├── payment/              #   支付模块
│   ├── ticket/               #   工单模块
│   └── cron/                 #   定时任务
│
├── plugins/                  # ★ 插件目录
│   ├── index.php             #   插件列表页面
│   ├── demo_plugin/          #   示例插件
│   │   ├── plugin.json      #   插件信息
│   │   ├── plugin.php       #   插件主文件
│   │   └── hook/            #   钩子函数
│   ├── alipay_payment/       #   支付宝支付插件
│   ├── wxpay_payment/        #   微信支付插件
│   ├── aliyun_sms/           #   阿里云短信插件
│   ├── tencent_sms/          #   腾讯云短信插件
│   ├── realname_auth/        #   实名认证插件
│   └── whmcs_migration/      #   WHMCS数据迁移
│
├── themes/                   # ★ 主题目录
│   └── default/              #   默认主题 (速科云风格)
│       ├── theme.json        #   主题信息
│       ├── header.php        #   公共头部
│       ├── footer.php        #   公共底部
│       ├── index.php         #   首页模板
│       ├── login.php         #   登录页
│       ├── register.php      #   注册页
│       ├── products.php      #   产品列表
│       ├── product.php       #   产品详情
│       ├── cart.php          #   购物车
│       ├── checkout.php      #   结算页
│       ├── order.php         #   订单详情
│       ├── user/             #   用户中心模板
│       │   ├── dashboard.php #     用户首页
│       │   ├── profile.php   #     个人资料
│       │   ├── orders.php    #     我的订单
│       │   ├── tickets.php   #     我的工单
│       │   ├── finance.php   #     财务管理
│       │   └── products.php  #     我的产品
│       ├── css/
│       │   ├── style.css     #   主样式
│       │   └── responsive.css #   响应式适配
│       └── js/
│           └── main.js       #   前端脚本
│
├── uploads/                  # 上传文件
├── cache/                    # 缓存目录
├── install/                  # 安装文件
│   └── database.sql          #   数据库结构
├── cron.php                  # 定时任务入口
│
├── docs/                     # 开发文档
│   ├── api.md                #   API文档
│   ├── plugin-dev.md         #   插件开发指南
│   ├── theme-dev.md          #   主题开发指南
│   └── hook-list.md          #   Hook列表
│
├── PLAN.md                   # 开发计划
└── README.md                 # 本文件
```

---

## 🔌 插件系统

vzyunIDC 采用 Hook 机制实现插件系统，类似魔方财务的插件架构。

### 插件生命周期
```
安装 → 启用 → 加载Hook → 执行 → 禁用 → 卸载
```

### Hook 类型

| Hook 类型 | 说明 | 示例 |
|-----------|------|------|
| `action_*` | 动作钩子 | 用户注册后、订单创建后 |
| `filter_*` | 过滤钩子 | 订单金额过滤、支付渠道过滤 |
| `page_*` | 页面钩子 | 后台菜单注入、前端页面注入 |

### 插件示例结构

```json
// plugins/demo_plugin/plugin.json
{
    "name": "demo_plugin",
    "title": "示例插件",
    "version": "1.0.0",
    "author": "vzyun",
    "description": "这是一个示例插件",
    "hooks": [
        "action_user_register_after",
        "filter_order_amount",
        "filter_sms_channels"
    ]
}
```

### 常用插件推荐

| 插件 | 功能 |
|------|------|
| 支付宝支付 | 对接支付宝当面付/PC支付 |
| 微信支付 | 对接微信支付Native/JSAPI |
| PayPal支付 | 国际支付对接 |
| 阿里云短信 | 阿里云短信通道 |
| 腾讯云短信 | 腾讯云短信通道 |
| 实名认证 | 支付宝/微信实名接口对接 |
| 魔方财务同步 | 与魔方财务系统数据同步 |
| WHMCS迁移 | 从WHMCS导入数据 |

---

## 🎨 主题系统

默认主题采用**速科云风格**设计，简洁大气、响应式适配。

### 主题特性

- ✅ 响应式设计（PC/平板/手机自适应）
- ✅ 深色模式支持
- ✅ 模块化模板继承
- ✅ 可视化配置面板
- ✅ 多语言支持

### 主题切换

```php
// 后台主题管理一键切换
// 支持自定义上传主题
```

### 开发自定义主题

参考 `themes/default/theme.json` 配置主题信息，遵循模板引擎规范即可开发自定义主题。

---

## 📡 API文档

vzyunIDC 提供完整的 RESTful API，支持与第三方系统对接。

### API基础信息

| 项目 | 说明 |
|------|------|
| 基础URL | `https://你的域名/api/v1/` |
| 格式 | JSON |
| 认证 | Bearer Token (JWT) |
| 分页 | `?page=1&limit=20` |

### 核心API

| 接口 | 说明 |
|------|------|
| `POST /auth/login` | 用户登录 |
| `POST /auth/register` | 用户注册 |
| `GET /products` | 产品列表 |
| `GET /products/{id}` | 产品详情 |
| `POST /orders` | 创建订单 |
| `GET /orders` | 订单列表 |
| `GET /orders/{id}` | 订单详情 |
| `POST /payment/create` | 创建支付 |
| `GET /tickets` | 工单列表 |
| `POST /tickets` | 创建工单 |

---

## 🔗 对接魔方财务

vzyunIDC 支持与智简魔方财务系统双向数据对接。

### 支持对接的功能

| 功能 | 说明 |
|------|------|
| 🔄 用户同步 | 双向同步用户数据 |
| 📦 产品同步 | 同步魔方财务产品到 vzyunIDC |
| 📋 订单同步 | 订单状态双向通知 |
| 💰 余额同步 | 用户余额同步更新 |
| 📨 通知推送 | 通过魔方财务发送通知 |
| 🔑 API密钥 | 使用魔方财务 API Token 认证 |

### 对接配置

在管理后台 → 系统设置 → 魔方财务对接，填写：
- 魔方财务系统地址
- API Token
- 同步方向（单向/双向）
- 同步频率

---

## 🗓️ 开发计划

### 阶段一：核心功能（高考假期版）
| 日期 | 任务 | 状态 |
|------|------|------|
| Day 1 | 基础框架 + 数据库设计 + 用户系统 | ⏳ 进行中 |
| Day 2 | 产品管理 + 购物车 + 订单系统 | ⏳ 待开始 |
| Day 3 | 管理后台 + 支付对接 | ⏳ 待开始 |
| Day 4 | 插件系统 + 主题系统 + 工单 + 短信 | ⏳ 待开始 |

### 阶段二：扩展完善（后续迭代）
- [ ] 魔方财务 API 对接
- [ ] 实名认证系统
- [ ] 发票管理
- [ ] 多语言支持
- [ ] 分销/代理系统
- [ ] 自动化部署功能

---

## 🤝 参与贡献

1. Fork 本仓库
2. 创建功能分支 (`git checkout -b feature/xxx`)
3. 提交代码 (`git commit -m 'Add xxx'`)
4. 推送到分支 (`git push origin feature/xxx`)
5. 创建 Pull Request

---

## 📜 开源协议

本项目基于 MIT 协议开源，可自由使用、修改、商用。

```
MIT License

Copyright (c) 2026 vzyun

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
...
```

---

## 👨‍💻 关于

| 角色 | 信息 |
|------|------|
| 项目发起人 | [vzyun](https://github.com/vzyun) |
| AI 开发助手 | Neo |
| 技术栈 | PHP + MySQL + Bootstrap 5 |
| 开发时间 | 2026年6月高考假期 |

---

> **⭐ 如果这个项目对你有帮助，请给一个 Star！**
>
> 项目维护与代码更新由 [Neo](https://github.com/vzyun/vzyunidc) 自动推送
