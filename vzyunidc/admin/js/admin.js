/**
 * vzyunIDC - 管理后台JS
 */

$(function() {
    // Toast提示
    window.showToast = function(msg, type) {
        type = type || 'success';
        const html = '<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">' +
            '<div class="toast align-items-center text-bg-' + type + ' border-0 show" role="alert">' +
            '<div class="d-flex"><div class="toast-body"><i class="fas fa-' + (type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle') + '"></i> ' + msg + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div></div>';
        const $toast = $(html);
        $('body').append($toast);
        setTimeout(() => { $toast.remove(); }, 3000);
    };

    // 确认操作
    window.confirmAction = function(msg, callback) {
        if (confirm(msg || '确定执行此操作？')) {
            callback();
        }
    };

    // 表单确认删除
    $(document).on('click', '.btn-delete', function(e) {
        if (!confirm('确定要删除吗？此操作不可撤销。')) {
            e.preventDefault();
        }
    });
});
