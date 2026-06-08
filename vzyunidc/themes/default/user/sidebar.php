<div style="background:white;border-radius:12px;border:1px solid var(--gray-200);overflow:hidden;position:sticky;top:80px;">
    <div style="padding:16px;background:var(--gradient-primary);color:white;text-align:center;">
        <div style="width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;margin:0 auto 8px;font-size:20px;">
            <i class="fas fa-user"></i>
        </div>
        <div style="font-weight:600;"><?= h($userInfo['username']) ?></div>
        <div style="font-size:13px;opacity:0.9;">余额：¥<?= formatMoney($userInfo['balance']) ?></div>
    </div>
    <nav style="padding:8px 0;">
        <a href="/?route=user" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--gray-600);text-decoration:none;font-size:14px;">
            <i class="fas fa-chart-simple" style="width:18px;"></i> 用户中心
        </a>
        <a href="/?route=user&action=profile" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--gray-600);text-decoration:none;font-size:14px;">
            <i class="fas fa-user-gear" style="width:18px;"></i> 账户资料
        </a>
        <a href="/?route=user&action=orders" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--gray-600);text-decoration:none;font-size:14px;">
            <i class="fas fa-file-invoice" style="width:18px;"></i> 我的订单
        </a>
        <a href="/?route=user&action=hosts" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--gray-600);text-decoration:none;font-size:14px;">
            <i class="fas fa-server" style="width:18px;"></i> 我的产品
        </a>
        <a href="/?route=tickets" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--gray-600);text-decoration:none;font-size:14px;">
            <i class="fas fa-ticket" style="width:18px;"></i> 我的工单
        </a>
        <hr style="margin:8px 16px;">
        <a href="/?route=logout" style="display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--gray-400);text-decoration:none;font-size:14px;">
            <i class="fas fa-sign-out-alt" style="width:18px;"></i> 退出登录
        </a>
    </nav>
</div>
