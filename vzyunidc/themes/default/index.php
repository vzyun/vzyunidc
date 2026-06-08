<?php
/**
 * vzyunIDC - 首页模板（维智云风格）
 */
$pageTitle = '首页';
require_once __DIR__ . '/header.php';
?>

<!-- Hero Banner -->
<section class="hero">
    <div class="hero-grid"></div>
    <div class="hero-content">
        <h1><?= h(getSetting('hero_title', '高性能云服务<br>助力您的<span>业务增长</span>')) ?></h1>
        <p><?= h(getSetting('hero_subtitle', '弹性扩展 · 安全可靠 · 全球部署 · 极速体验')) ?></p>
        <div class="hero-actions">
            <a href="/?route=products" class="btn btn-primary">
                <i class="fas fa-rocket"></i> 立即选购
            </a>
            <a href="/?route=register" class="btn btn-outline-light">
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
<section class="section section-gray">
    <div class="section-header">
        <h2>热门产品</h2>
        <p>精选高性能云产品，满足您不同业务场景需求</p>
    </div>
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <div class="product-card <?= $product['recommended'] ? 'recommended' : '' ?>">
            <div class="product-card-header">
                <div class="icon">
                    <?php $icons = ['vps'=>'icon-vps.svg','host'=>'icon-cloud.svg','server'=>'icon-server.svg','domain'=>'icon-domain.svg','other'=>'icon-shield.svg']; ?>
                    <img src="/themes/default/assets/<?= $icons[$product['type']] ?? 'icon-server.svg' ?>" alt="<?= h($product['type']) ?>">
                </div>
                <h3><?= h($product['name']) ?></h3>
                <span class="type"><?php $types = ['vps'=>'云服务器','host'=>'虚拟主机','server'=>'物理服务器','domain'=>'域名','other'=>'其他']; ?>
                    <?= $types[$product['type']] ?? $product['type'] ?></span>
            </div>
            <div class="product-card-body">
                <ul>
                    <?php 
                    $features = explode("\n", $product['description']);
                    foreach (array_slice(array_filter($features), 0, 4) as $feature):
                    ?>
                    <li><?= h(trim($feature)) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="product-card-footer">
                <div class="price">
                    ¥<?= formatMoney($product['price']) ?>
                    <span class="cycle">/<?php $cycles = ['monthly'=>'月','quarterly'=>'季','semiannually'=>'半年','annually'=>'年']; echo $cycles[$product['cycle']] ?? $product['cycle']; ?></span>
                </div>
                <?php if ($product['setup_fee'] > 0): ?>
                <div class="old-price">初装费 ¥<?= formatMoney($product['setup_fee']) ?></div>
                <?php endif; ?>
                <a href="/?route=product&id=<?= $product['id'] ?>" class="btn btn-primary">立即购买</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- 为什么选择我们 -->
<section class="section">
    <div class="section-header">
        <h2>为什么选择我们</h2>
        <p>提供极致体验的企业上云服务，拥有安全有效的解决方案，为您云上旅程保驾护航</p>
    </div>
    <div class="features-grid">
        <div class="feature-card">
            <div class="icon"><img src="/themes/default/assets/remoteL17054789546267.svg" alt="弹性计算"></div>
            <h4>弹性计算</h4>
            <p>您可以在几分钟之内快速根据业务需求，可弹性创建与释放云服务器，轻松应对业务的快速变化。</p>
        </div>
        <div class="feature-card">
            <div class="icon"><img src="/themes/default/assets/remoteL16850667456953.svg" alt="多样化配置"></div>
            <h4>多样化配置</h4>
            <p>提供多种类型的实例、操作系统和软件包。各实例中的CPU、内存、硬盘和带宽可以灵活调整。</p>
        </div>
        <div class="feature-card">
            <div class="icon"><img src="/themes/default/assets/remoteL16905328285515.svg" alt="安全网络"></div>
            <h4>安全的网络</h4>
            <p>通过云控制台，切实保证您云上资源的安全性。您还可以完全掌控您的私有网络环境配置等。</p>
        </div>
        <div class="feature-card">
            <div class="icon"><img src="/themes/default/assets/remoteL16905328288110.svg" alt="管理简单"></div>
            <h4>管理简单</h4>
            <p>可以使用云控制台进行重启等重要操作，管理实例就像管理操作您的计算机一样简单方便。</p>
        </div>
        <div class="feature-card">
            <div class="icon"><img src="/themes/default/assets/remoteL16905328292905.svg" alt="快速部署"></div>
            <h4>快速部署</h4>
            <p>分钟级快速部署，弹性扩展，按需付费，轻松应对业务增长，让您专注核心业务。</p>
        </div>
        <div class="feature-card">
            <div class="icon"><img src="/themes/default/assets/remoteL16905328293623.svg" alt="7x24支持"></div>
            <h4>7×24支持</h4>
            <p>7×24小时技术支持，工单即时响应，专业技术团队为您排忧解难，服务永不掉线。</p>
        </div>
    </div>
</section>

<!-- 合作伙伴 -->
<section class="section section-gray">
    <div class="section-header">
        <h2>合作伙伴</h2>
        <p>携手行业领先企业，为您提供稳定可靠的云服务</p>
    </div>
    <div class="partners-grid">
        <img src="/themes/default/assets/aliy.png" alt="阿里云">
        <img src="/themes/default/assets/txyun.svg" alt="腾讯云">
        <img src="/themes/default/assets/huawei.png" alt="华为云">
        <img src="/themes/default/assets/bt.png" alt="宝塔">
        <img src="/themes/default/assets/dianxin.png" alt="电信">
        <img src="/themes/default/assets/liantong.png" alt="联通">
    </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
