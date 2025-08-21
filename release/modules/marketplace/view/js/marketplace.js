$(function(){
    // Сохраянем URL ифрейма при переходах
    var iframe = $("iframe#frame");

    // Обработчик сообщения, посылаемого из IFrame
    window.addEventListener("message", function(ev){
        // Если Iframe сообщил о смене адреса
        if(ev.data.url) {
            history.replaceState({
                iframeUrl:ev.data.url
            }, null, document.location.pathname + '#' + ev.data.url);

            window.scrollTo({
                top:0
            });
        }

        // Если Iframe сообщил, что нужно установить скролл
        if (ev.data.scrollTo) {
            window.scrollTo(ev.data.scrollTo);
        }
    }, false);

    // Восстанавливаем URL ифрейма из hash (при обновлении страницы)
    if(document.location.hash){
        var url_with_proxy = iframe.data('proxyUrl');
        url_with_proxy += '?url='+encodeURIComponent(document.location.hash.substr(1));
        iframe.attr('src', url_with_proxy);
    }

    iframe.load(function() {
        $('#mp-loader').hide();
    });
});