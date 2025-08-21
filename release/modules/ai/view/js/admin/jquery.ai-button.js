/**
 * Jquery плагин, отвечающий за ИИ-кнопки возле форм
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.aiButton = function( method ) {
        const defaults = {},
            args = arguments;

        return this.map(function() {
            let $this = $(this),
                data = $this.data('aiButtonInstance'),
                params = $this.data('aiButton');

            const methods = {
                init: function (initOptions) {
                    if (data) return;
                    data = {};
                    $this.data('aiButtonInstance', data);
                    data.opt = $.extend({}, defaults, initOptions);

                    createButton();
                    bindAutoStop();
                },

                /**
                 * Выполняет запрос на генерацию значения для поля
                 *
                 * @param promptId
                 * @param force - всегда запускать
                 */
                startGeneration: async function(promptId, force) {
                    if (data.$buttonGroup.hasClass('loading')) {
                        methods.stopGeneration();
                        if (!force) return;
                    }

                    return new Promise(async (resolve, reject) => {
                        let form = data.$buttonGroup.closest('form');

                        if (!promptId) {
                            promptId = data.$mainButton.data('promptId');
                        }

                        data.$buttonGroup.addClass('loading');
                        let url = global.ai.generateUrl + '&prompt_id=' + promptId;
                        let formData = new FormData(form[0]);

                        data.fetcher = new StreamFetcher();
                        data.fetcher.setStreamCallback((fulltext, jsonData, iteration) => {
                            if (iteration === 0) {
                                data.previousValue = $this[0].value;
                                $this[0].value = '';
                            }

                            $this[0].value += jsonData.text;
                        });

                        data.fetcher.fetchStream(url, formData)
                            .then(object => {
                                resolve(object);
                            })
                            .catch(error => {
                                reject(error);
                            })
                            .finally(() => {
                                data.$buttonGroup.removeClass('loading');
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
             * Создает кнопку генерации текста с выпадающим списком
             */
            createButton = function() {
                if (!params['prompts'].length) return;

                let isMultiedit = $this.closest('.multi_edit_rightcol');
                if (isMultiedit.length) return;

                data.$buttonGroup = $(`<div class="btn-group ai-btn-group"></div>`);
                data.$mainButton = $(`<button type="button" class="btn btn-default ai-gen"></button>`)
                    .attr('data-prompt-id', params['prompts'][0]['id'])
                    .attr('title', lang.t('Заполнить через ИИ'))
                    .appendTo(data.$buttonGroup);

                data.$dropDownButton = $(`<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false"><span class="caret"></span>
                                            </button>`).appendTo(data.$buttonGroup);

                data.$dropDown = $(`<ul class="dropdown-menu dropdown-menu-right"></ul>`).appendTo(data.$buttonGroup);

                params['prompts'].forEach((prompt) => {
                    $('<li/>').append(
                        $('<a/>')
                            .attr('data-prompt-id', prompt['id'])
                            .text(prompt['note'])
                            .on('click', event => {
                                methods.startGeneration($(event.currentTarget).data('prompt-id')).catch(() => {});
                            })
                    ).appendTo(data.$dropDown);
                });

                data.$buttonGroup.data('aiBaseField', $this);
                $this
                    .wrap('<span class="form-wrapper"></span>')
                    .parent()
                    .append(data.$buttonGroup);

                data.$buttonGroup.parent().trigger('new-content');

                data.$mainButton.on('click', event => {
                    methods.startGeneration($(event.currentTarget).data('prompt-id')).catch(() => {});
                });
                $this.on('keyup', checkUndo);
            },

            /**
             * Позволяет возвращать значение в поле до генерации при нажатии CTRL+Z
             *
             * @param event
             */
            checkUndo = function(event) {
                if (data.previousValue !== null && event.ctrlKey && event.keyCode === 90) {
                    $this[0].value = data.previousValue;
                    data.previousValue = null;

                    event.preventDefault();
                }
            },

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
        $('[data-ai-button]', this).aiButton();
    });

})(jQuery);