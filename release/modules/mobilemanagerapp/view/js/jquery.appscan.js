/**
 * Plugin ReadyScript, активирует сканирование штрихкодов и QR-кодов через приложение ReadyScript
 * Плагин инициализируется автоматически на все элементы с атрибутом data-app-scan
 */
(function($){
    $.fn.appScan = function(method) {
        const defaults = {
                refreshInterval:2000,
                autoPressEnter: true,    //Нажимать Enter после ввода результатов сканирования в поле input
                autoPressEvent: 'keydown', //Событие для автоматического нажатия Enter (keydown или keypress)
                triggerEvent: false,     //Событие для генерации, после вставки. Например: 'click'. false - не генерировать событие
                triggerTarget: '',       //Селектор элемента для события. Например: '.apply-button',
                triggerTargetContext: '*' //Селектор родительского элемента, в котором находится triggerTarget. Например: '.apply-button-wrapper'
            },
            args = arguments;

        return this.each(function() {

            let $this = $(this),
                data = $this.data('appScanPlugin');

            const methods = {
                init: function (initoptions) {
                    if (data) return;
                    data = {};
                    $this.data('appScanPlugin', data);
                    data.opt = $.extend({}, defaults, initoptions, $this.data('appScanOptions'));

                    createButton();
                }
            };

            //private
            const
                /**
                 * Создает кнопку сканирования
                 */
                createButton = function() {
                    let button = $('<a>')
                        .attr('class', 'btn btn-default app-scan-button m-l-5 m-r-5')
                        .attr('title', lang.t('Сканировать в приложении ReadyScript'))
                        .append('<i class="zmdi zmdi-fullscreen"></i>');

                    button.click(startScan);
                    button.insertAfter($this);
                    button.parent().trigger('new-content');
                },
                /**
                 * Обрабатывает нажатие на кнопку сканирования
                 */
                startScan = function() {
                    if ($.rs.loading.inProgress) {
                        return;
                    }

                    const formats = $this.data('appScan');
                    const filter = $this.data('appScanFilter');

                    $.rs.openDialog({
                        url: global.scanUrl,
                        extraParams: {
                            formats: formats,
                            filter: filter
                        },
                        dialogOptions: {
                            width:400
                        },
                        afterOpen: function(dialog) {
                            initEvents(dialog);
                            dialog.on('click', '[data-resend-url]', (event) => reSend(event, dialog));
                        },
                        close: function(dialog) {
                            clearInterval(dialog.data('refreshInterval'));
                        }
                    });
                },
                /**
                 * Обновляет сведения о запросе на сканирование
                 */
                refresh = function(dialog, resend) {
                    let refreshBlock = dialog.find('[data-scan-root]');
                    $.ajaxQuery({
                        loadingProgress: resend === true,
                        url: refreshBlock.data('refreshUrl'),
                        data: {
                            status: refreshBlock.data('status'),
                            resend: (resend ? 1 : 0)
                        },
                        success: function(response) {
                            if (response.changed) {
                                if (response.status === 'success') {
                                    $this.val(response.result);
                                    dialog.dialog('close');

                                    if (data.opt.autoPressEnter) {
                                        const keyboardEvent = new KeyboardEvent(data.opt.autoPressEvent, {
                                            code: 'Enter',
                                            key: 'Enter',
                                            charCode: 13,
                                            keyCode: 13,
                                            view: window,
                                            bubbles: true
                                        });
                                        $this.get(0).dispatchEvent(keyboardEvent);
                                    }

                                    if (data.opt.triggerEvent) {
                                        const triggerElement = $this.parents('*').first()
                                            .find(data.opt.triggerTarget)
                                            .get(0);

                                        if (triggerElement) {
                                            triggerElement.dispatchEvent(
                                                new Event(data.opt.triggerEvent, {bubbles: true}));
                                        }
                                    }
                                } else {
                                    refreshBlock.replaceWith(response.html);
                                    initEvents(dialog);
                                }
                            }
                        }
                    });
                },

                /**
                 * Отправляет Push уведомление на мобильное устройство повторно
                 */
                reSend = function(event, dialog) {
                    clearInterval(dialog.data('refreshInterval'));

                    let button = $(event.currentTarget);
                        button.text(button.data('sendingText'))
                              .prop('disabled', true);

                    refresh(dialog, true);
                },

                /**
                 * Инициализирует автоматическое обновление данных
                 */
                initEvents = function(dialog) {
                    let refreshBlock = dialog.find('[data-scan-root]');

                    clearInterval(dialog.data('refreshInterval'));
                    if (refreshBlock.data('status') === 'waiting') {
                        dialog.data('refreshInterval', setInterval(() => refresh(dialog),
                            data.opt.refreshInterval));
                    }
                };


            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }
})(jQuery);

$.contentReady(function() {
    $('input[data-app-scan]').appScan();
});