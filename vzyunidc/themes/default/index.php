<?php
/**
 * vzyunIDC - 首页模板（速科云风格）
 */
$pageTitle = '首页';
require_once __DIR__ . '/header.php';
?>

<!-- Hero Banner -->
<section class="hero">
    <div class="hero-content">
        <h1><?= h(getSetting('hero_title', '高性能云服务<br>助力您的<span>业务增长</span>')) ?></h1>
        <p><?= h(getSetting('hero_subtitle', '弹性扩展 · 安全可靠 · 全球部署 · 极速体验')) ?></p>
        <div class="hero-actions">
            <a href="/?route=products" class="btn btn-primary btn-lg">
                <i class="fas fa-rocket"></i> 立即选购
            </a>
            <a href="/?route=register" class="btn btn-outline btn-lg" style="border-color:rgba(255,255,255,0.2);color:white;">
                免费注册
            </a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <div class="hero-stat-number">10000+</div>
                <div class="hero-stat-label">满意客户</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-number">99.9%</div>
                <div class="hero-stat-label">在线率保障</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-number">50+</div>
                <div class="hero-stat-label">全球节点</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-number">7×24</div>
                <div class="hero-stat-label">技术支持</div>
            </div>
        </div>
    </div>
</section>

<!-- 推荐产品 -->
<?php if (!empty($products)): ?>
<section class="section section-light">
    <div class="section-header">
        <h2>热门产品</h2>
        <p>精选高性能云产品，满足您不同业务场景需求</p>
    </div>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card">
            <div class="product-card-header">
                <div class="icon">
                    <i class="fas fa-server"></i>
                </div>
                <h3><?= h($product['name']) ?></h3>
                <span class="type"><?= h($product['type']) ?></span>
            </div>
            <div class="product-card-body">
                <ul>
                    <?php 
                    $features = explode("\n", $product['description']);
                    foreach (array_slice($features, 0, 4) as $feature):
                        if (trim($feature)):
                    ?>
                    <li><?= h(trim($feature)) ?></li>
                    <?php endif; endforeach; ?>
                </ul>
            </div>
            <div class="product-card-footer">
                <div class="price">
                    ¥<?= formatMoney($product['price']) ?>
                    <span class="cycle">/<?= h($product['cycle']) ?></span>
                </div>
                <?php if ($product['setup_fee'] > 0): ?>
                <div class="old-price">初装费 ¥<?= formatMoney($product['setup_fee']) ?></div>
                <?php endif; ?>
                <a href="/?route=product&id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">立即购买</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- 为什么选择我们 -->
<section class="section section-gray">
    <div class="section-header">
        <h2>为什么选择我们</h2>
        <p>专业的技术实力和完善的服务体系，为您的业务保驾护航</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="icon"><i class="fas fa-bolt"></i></div>
            <h4>极速部署</h4>
            <p>分钟级快速部署，弹性扩展，按需付费，轻松应对业务增长</p>
        </div>
        <div class="feature-card">
            <div class="icon"><i class="fas fa-shield-alt"></i></div>
            <h4>安全可靠</h4>
            <p>多层安全防护，DDoS防护，数据加密，99.9%在线率保障</p>
        </div>
        <div class="feature-card">
            <div class="icon"><i class="fas fa-globe"></i></div>
            <h4>全球覆盖</h4>
            <p>全球50+数据中心节点，CN2 GIA直连，低延迟高速访问</p>
        </div>
        <div class="feature-card">
            <div class="icon"><i class="fas fa-headset"></i></div>
            <h4>专业支持</h4>
            <p>7×24小时技术支持，工单即时响应，专业技术团队</p>
        </div>
        <div class="feature-card">
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <h4>弹性扩展</h4>
            <p>资源弹性伸缩，随时调整配置，按实际使用付费</p>
        </div>
        <div class="feature-card">
            <div class="icon"><i class="fas fa-cog"></i></div>
            <h4>管理便捷</h4>
            <p>直观的控制面板，一键重装、重启、监控，操作简单</p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
