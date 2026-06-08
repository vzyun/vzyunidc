<?php
/**
 * vzyunIDC - Footer模板
 */
?>
</main>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-brand">
            <h4><?= SITE_NAME ?></h4>
            <p>精品云计算服务商，提供云服务器、VPS、虚拟主机等高性能云产品。弹性扩展 · 安全可靠 · 全球部署 · 极速体验。</p>
            <div style="margin-top:16px;">
                <a href="#" style="color:rgba(255,255,255,0.4);margin-right:12px;font-size:18px;"><i class="fab fa-weixin"></i></a>
                <a href="#" style="color:rgba(255,255,255,0.4);margin-right:12px;font-size:18px;"><i class="fab fa-qq"></i></a>
                <a href="#" style="color:rgba(255,255,255,0.4);font-size:18px;"><i class="fab fa-bilibili"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h5>产品服务</h5>
            <ul>
                <li><a href="/?route=products">云服务器</a></li>
                <li><a href="/?route=products">VPS</a></li>
                <li><a href="/?route=products">虚拟主机</a></li>
                <li><a href="/?route=products">物理服务器</a></li>
                <li><a href="/?route=products">域名注册</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h5>帮助支持</h5>
            <ul>
                <li><a href="/?route=tickets">提交工单</a></li>
                <li><a href="#">帮助中心</a></li>
                <li><a href="#">价格说明</a></li>
                <li><a href="#">服务条款</a></li>
                <li><a href="#">隐私政策</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h5>关于我们</h5>
            <ul>
                <li><a href="#">公司介绍</a></li>
                <li><a href="#">新闻公告</a></li>
                <li><a href="#">加入我们</a></li>
                <li><a href="#">合作伙伴</a></li>
                <li><a href="#">联系我们</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <?= h(getSetting('site_footer', '© 2026 ' . SITE_NAME . ' All rights reserved.')) ?>
        <?php $icp = getSetting('site_icp'); if ($icp): ?>
        <span style="margin:0 8px;">|</span>
        <a href="https://beian.miit.gov.cn" target="_blank"><?= h($icp) ?></a>
        <?php endif; ?>
    </div>
</footer>

<div class="toast-container" id="toastContainer"></div>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="/themes/default/js/main.js?v=2.0"></script>
<?php Hook::action('frontend_footer'); ?>
</body>
</html>
