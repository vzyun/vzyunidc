/**
 * vzyunIDC - 前端JS
 */

$(function() {
    // 导航滚动效果
    var $header = $('#mainHeader');
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 50) {
            $header.addClass('scrolled');
        } else {
            $header.removeClass('scrolled');
        }
    });

    // Toast提示
    window.showToast = function(msg, type) {
        type = type || 'success';
        var icon = type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle';
        var html = '<div class="toast-custom ' + type + '">' +
            '<i class="fas fa-' + icon + '" style="color:var(--' + (type === 'danger' ? 'danger' : type === 'success' ? 'success' : 'info') + ');"></i> ' +
            msg + '</div>';
        $('#toastContainer').append(html);
        setTimeout(function() { $('.toast-custom:first').remove(); }, 3000);
    };

    // AJAX表单
    $(document).on('submit', '.ajax-form', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('[type="submit"]');
        $btn.prop('disabled', true).html('<span class="spinner" style="width:16px;height:16px;border-width:2px;"></span>');
        $.post($form.attr('action'), $form.serialize(), function(res) {
            if (res.code === 0) {
                showToast(res.msg || '操作成功', 'success');
                if (res.redirect) setTimeout(function() { location.href = res.redirect; }, 500);
            } else {
                showToast(res.msg || '操作失败', 'danger');
            }
        }).fail(function() {
            showToast('网络错误', 'danger');
        }).always(function() {
            $btn.prop('disabled', false).html($btn.data('original-text') || $btn.text());
        });
    });

    // Confirm操作
    window.confirmAction = function(msg, callback) {
        if (confirm(msg || '确定执行此操作？')) { callback(); }
    };
});
