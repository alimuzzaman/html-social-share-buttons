(function(){
    'use strict';

    function toggleQr(button) {
        var container = button.closest('.hssb-wechat');
        if (!container) return;
        var qr = container.querySelector('.hssb-wechat-qr');
        if (!qr) return;
        var isVisible = qr.classList.contains('hssb-wechat-qr--visible');
        if (isVisible) {
            qr.classList.remove('hssb-wechat-qr--visible');
            qr.style.display = 'none';
            button.setAttribute('aria-expanded', 'false');
        } else {
            qr.classList.add('hssb-wechat-qr--visible');
            qr.style.display = '';
            button.setAttribute('aria-expanded', 'true');
        }
    }

    function onDocumentClick(e) {
        var btn = e.target.closest('.hssb-wechat-btn');
        if (btn) {
            e.preventDefault();
            toggleQr(btn);
            return;
        }

        // Click outside any QR container closes all visible QR areas
        var openQrs = document.querySelectorAll('.hssb-wechat-qr.hssb-wechat-qr--visible');
        if (openQrs.length) {
            openQrs.forEach(function(q){
                q.classList.remove('hssb-wechat-qr--visible');
                q.style.display = 'none';
                var container = q.closest('.hssb-wechat');
                if (container) {
                    var b = container.querySelector('.hssb-wechat-btn');
                    if (b) b.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }

    function onKeydown(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            var openQrs = document.querySelectorAll('.hssb-wechat-qr.hssb-wechat-qr--visible');
            openQrs.forEach(function(q){
                q.classList.remove('hssb-wechat-qr--visible');
                q.style.display = 'none';
                var container = q.closest('.hssb-wechat');
                if (container) {
                    var b = container.querySelector('.hssb-wechat-btn');
                    if (b) b.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }

    document.addEventListener('click', onDocumentClick);
    document.addEventListener('keydown', onKeydown);
})();
