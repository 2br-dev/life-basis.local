/**
 * Jquery плагин, отвечающий за генерацию контента для textarea, которые связаны с TinyMCE
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.aiRichText = function( method ) {
        const defaults = {},
            args = arguments;

        return this.map(function() {
            let $this = $(this),
                data = $this.data('aiRichTextInstance'),
                params = $this.data('aiRichtext');

            const methods = {
                init: function (initOptions) {
                    if (data) return;
                    data = {};
                    $this.data('aiRichTextInstance', data);
                    data.opt = $.extend({}, defaults, initOptions);
                    bindAutoStop();
                },

                /**
                 * Выполняет запрос на генерацию значения для поля
                 *
                 * @param promptId
                 * @param force - всегда запускать
                 */
                startGeneration: async function(promptId, force) {
                    if (data['aiLoading'] ===  true) {
                        methods.stopGeneration();
                        if (!force) return;
                    }

                    return new Promise(async (resolve, reject) => {
                        let form = $this.closest('form');

                        if (!promptId) {
                            promptId = params['prompts'][0]['id'];
                        }

                        //data.$buttonGroup.addClass('loading');
                        data['aiLoading'] = true;
                        $this.trigger('aiStartLoading');

                        let url = global.ai.generateUrl + '&prompt_id=' + promptId;
                        let formData = new FormData(form[0]);

                        data.fetcher = new StreamFetcher();
                        data.fetcher.setStreamCallback((fulltext, jsonData, iteration) => {
                                if (iteration === 0) {
                                    $this.trigger('aiBeforeFirstSetValue');
                                    $this[0].value = '';
                                }

                                $this[0].value += jsonData.text;
                                $this.trigger('aiSetValue', [$this[0].value]);
                            })

                        data.fetcher.fetchStream(url, formData)
                            .then(object => {
                                resolve(object);
                            })
                            .catch(error => {
                                reject(error);
                            })
                            .finally(() => {
                                data['aiLoading'] = null;
                                $this.trigger('aiEndLoading');
                            });
                    });
                },

                /**
                 * Прерывает генерацию значения
                 */
                stopGeneration: function() {
                    if (data.fetcher) {
                        data.fetcher.getAbortController().abort('AbortError');
                    }
                }
            };

            //private
            const

            /**
             * Автоматически прерывает запрос, если закрылось окно, в котором находится поле
             */
            bindAutoStop = function() {
                $this.closest('.ui-dialog').on('dialogclose', methods.stopGeneration);
            };

            if (methods[method]) {
                return methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                methods.init.apply(this, args);
                return this;
            }
        });
    }

    //Инициализируем каждый раз, когда на странице появляется новый контент
    $.contentReady(function () {
        $('[data-ai-richtext]', this).aiRichText();
    });

})(jQuery);