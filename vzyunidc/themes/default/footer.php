</main>

<footer class="footer">
    <div class="footer-inner">
        <div>
            <h4>vzyunIDC</h4>
            <p style="font-size:14px;line-height:1.8">专业的IDC业务管理系统<br>为您提供一站式云计算服务</p>
        </div>
        <div>
            <h4>产品服务</h4>
            <a href="/?route=products&category=1">云服务器</a>
            <a href="/?route=products&category=2">物理服务器</a>
            <a href="/?route=products&category=3">虚拟主机</a>
            <a href="/?route=products&category=4">域名服务</a>
        </div>
        <div>
            <h4>帮助支持</h4>
            <a href="/?route=user&action=tickets">工单支持</a>
            <a href="/?route=page&slug=terms">服务条款</a>
            <a href="/?route=page&slug=privacy">隐私政策</a>
            <a href="/?route=page&slug=faq">常见问题</a>
        </div>
        <div>
            <h4>关于我们</h4>
            <a href="/?route=page&slug=about">关于我们</a>
            <a href="/?route=page&slug=contact">联系方式</a>
        </div>
    </div>
    <div class="footer-bottom">
        <?= nl2br(h(getSetting('site_footer', '© 2026 vzyunIDC All rights reserved.'))) ?>
        <?php $icp = getSetting('site_icp'); if ($icp): ?>
            <br><a href="https://beian.miit.gov.cn/" target="_blank"><?= h($icp) ?></a>
        <?php endif; ?>
    </div>
</footer>

<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="/themes/default/js/main.js?v=1.0"></script>
<?php Hook::action('frontend_footer'); ?>
</body>
</html>
