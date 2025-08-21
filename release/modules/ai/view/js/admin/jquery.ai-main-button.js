/**
 * Jquery плагин, отвечающий за ИИ-кнопки возле главной формы в админке
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.aiMainButton = function( method ) {
        const defaults = {},
            args = arguments;

        return this.each(function() {
            let $this = $(this),
                data = $this.data('aiMainButtonInstance'),
                params;

            const methods = {
                init: function (initOptions) {
                    if (data) return;
                    data = {};
                    $this.data('aiMainButtonInstance', data);
                    data.opt = $.extend({}, defaults, initOptions);
                    params = $this.data('aiMainButton');

                    createButton();
                },
                /**
                 * Выполняет запрос на генерацию значения для поля
                 *
                 * @param promptId
                 */
                startGeneration: async function() {
                    if (data.$buttonGroup.hasClass('loading')) {
                        return methods.stopGeneration();
                    }

                    if ($this.val() === '') {
                        $.messenger('show', {
                            theme: 'error',
                            text: lang.t('Заполните поле `%field`, чтобы на основе него можно было сгенерировать остальные поля', {
                                field: params.main_field_title
                            })
                        });
                        return;
                    }

                    data.$buttonGroup.addClass('loading');

                    //Получаем список форм, для которых доступна генерация
                    let formsByField = getFormsByField();

                    //Последовательно запускаем эти формы
                    for(let n in params['generate_fields']) {
                        let promises = [];
                        for(let i in params['generate_fields'][n]) {
                            let field = params['generate_fields'][n][i];
                            if (formsByField[field].val() === '')
                            {
                                let promise;
                                if (formsByField[field].is('[data-ai-button]')) {
                                    promise = formsByField[field].aiButton('startGeneration', null, true).get(0);
                                }
                                if (formsByField[field].is('[data-ai-richtext]')) {
                                    let tinymce = $(formsByField[field]).tinymce();
                                    if (tinymce) {
                                        promise = tinymce.rs.startGeneration(tinymce.rs.aiGenerateApi, null, true);
                                    } else {
                                        //Если мы здесь, значит tinymce находится на отдельной вкладке и он не инициализирован
                                        //В этом случае мы запускаем генерацию для обычного
                                        promise = formsByField[field].aiRichText('startGeneration', null, true).get(0);
                                    }
                                }

                                promise.catch(() => {});

                                if (promise) {
                                    promises.push(promise);
                                }
                            }
                        }

                        try {
                            await Promise.all(promises);
                        } catch(error) {
                            break;
                        }
                    }

                    data.$buttonGroup.removeClass('loading');
                },

                /**
                 * Прерывает генерацию значения
                 */
                stopGeneration: function() {
                    data.$buttonGroup.removeClass('loading');

                    let form = data.$buttonGroup.closest('form');

                    //Получаем список форм, для которых доступна генерация
                    let formsByField = {};
                    $('[data-ai-button]', form)
                        .add($('[data-ai-richtext]', form))
                        .each((n, element) => {
                            let $element = $(element);
                            if ($element.is('[data-ai-button]')) {
                                $element.aiButton('stopGeneration')
                            }
                            if ($element.is('[data-ai-richtext]')) {
                                let tinymce = $element.tinymce();
                                tinymce.rs.stopGeneration();
                            }
                        });
                }
            };

            //private
            const
                /**
                 * Создает кнопку генерации текста с выпадающим списком
                 */
                createButton = function() {
                    let isMultiedit = $this.closest('.multi_edit_rightcol');
                    if (isMultiedit.length) return;
                    if (!Object.keys(getFormsByField()).length) return;

                    data.$buttonGroup = $(`<div class="btn-group ai-btn-group"></div>`);
                    data.$mainButton = $(`<button type="button" class="btn btn-default ai-gen main"></button>`)
                        .attr('title', lang.t('Заполнить пустые поля'))
                        .appendTo(data.$buttonGroup);

                    data.$buttonGroup.data('aiBaseField', $this);
                    $this
                        .wrap('<span class="form-wrapper"></span>')
                        .parent()
                        .append(data.$buttonGroup);
                    data.$buttonGroup.parent().trigger('new-content');

                    data.$mainButton.on('click', event => methods.startGeneration());
                },

                /**
                 * Возвращает список элементов, для которых доступна генерация
                 *
                 * @returns {{}}
                 */
                getFormsByField = function() {
                    let formsByField = {};

                    let form = $this.closest('form');
                    $('[data-ai-button]', form)
                        .add($('[data-ai-richtext]', form))
                        .each((n, element) => {
                            let $element = $(element);
                            if ($element.is('[data-ai-button]') && $element.data('aiButton').prompts.length) {
                                formsByField[$element.data('aiButton').field_name] = $element;
                            }
                            if ($element.is('[data-ai-richtext]') && $element.data('aiRichtext').prompts.length) {
                                formsByField[$element.data('aiRichtext').field_name] = $element;
                            }
                        });

                    return formsByField;
                }

            if (methods[method]) {
                methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, args);
            }
        });
    }

    //Инициализируем каждый раз, когда на странице появляется новый контент
    $.contentReady(function () {
        $('[data-ai-main-button]', this).aiMainButton();
    });

})(jQuery);