/**
 * vzyunIDC - 前端主脚本
 */

$(function() {
    // Toast提示
    window.showToast = function(msg, type) {
        type = type || 'success';
        const icon = { success: 'fa-check-circle', danger: 'fa-exclamation-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        const html = '<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index:9999">' +
            '<div class="toast align-items-center text-bg-' + type + ' border-0" role="alert">' +
            '<div class="d-flex"><div class="toast-body"><i class="fas ' + (icon[type] || 'fa-info-circle') + '"></i> ' + msg + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div></div>';
        const $toast = $(html);
        $('body').append($toast);
        const toast = new bootstrap.Toast($toast.find('.toast')[0], { delay: 3000 });
        toast.show();
        $toast.find('.toast').on('hidden.bs.toast', function() { $toast.remove(); });
    };

    // AJAX全局设置
    $.ajaxSetup({
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') || ''
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                window.location.href = '/?route=login';
            }
        }
    });

    // 表单验证
    $('.form-control').on('blur', function() {
        const $this = $(this);
        if ($this.prop('required') && !$this.val()) {
            $this.addClass('error').removeClass('success');
        } else if ($this.attr('type') === 'email' && $this.val()) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            $this.toggleClass('error', !re.test($this.val())).toggleClass('success', re.test($this.val()));
        } else if ($this.val()) {
            $this.addClass('success').removeClass('error');
        }
    });

    // 加入购物车
    $(document).on('click', '.add-to-cart', function() {
        const productId = $(this).data('id');
        $.post('/api/product.php?action=add_cart', { product_id: productId }, function(res) {
            if (res.code === 0) {
                showToast('已加入购物车', 'success');
            } else if (res.code === 401) {
                window.location.href = '/?route=login';
            } else {
                showToast(res.msg, 'danger');
            }
        }, 'json');
    });

    // 删除购物车
    $(document).on('click', '.remove-cart', function() {
        const cartId = $(this).data('id');
        $.post('/api/product.php?action=remove_cart', { cart_id: cartId }, function(res) {
            if (res.code === 0) {
                $(this).closest('.cart-item').fadeOut();
                showToast('已移除', 'success');
            }
        }, 'json');
    });
});
