/**
 * Плагин инициализирует в административной панели работу функционала добавления параметров действия автозадачи
 *
 * @author ReadyScript lab.
 */
(function ($) {
    $.fn.autoTaskThenParams = function (method) {
        let defaults = {
                wrapper: '.autotask-then-wrapper',
                selectThenType: "select[name='then_type']",
                selectThenAction: "select[name='then_action']",
                actionNode: '.autotask_then_action',
                paramsNode: '.autotask_then_params',
                conditionsNode: '.autotask_then_conditions',
                addParamButton: '#addThenParam',
                addConditionButton: '#addThenCondition',
                currentValuesElement: '[rel="current-values-then"]',
                getTypesUrl: null,
                getParamsUrl: null,
                getConditionUrl: null,
                getParamDataUrl: null,
                paramsData: null,
                paramsCount: 0,
                conditionsCount: 0,
                initParamsCount: 0,
                initConditionsCount: 0,
                currentType: null,
                currentAction: null,
                currentParams: null

            },
            args = arguments;

        return this.each(function () {
            let $this = $(this),
                data = $this.data('autoTaskThenParams');

            let methods = {
                init: function (initoptions) {
                    if (data) return;
                    data = {};
                    $this.data('autoTaskThenParams', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    data.wrapper = $(data.opt.wrapper);
                    data.getTypesUrl = data.wrapper.data('getTypesUrl');
                    data.getParamsUrl = data.wrapper.data('getParamsUrl');
                    data.getConditionUrl = data.wrapper.data('getConditionUrl');
                    data.getParamDataUrl = data.wrapper.data('getParamDataUrl');
                    data.initParamsCount = data.wrapper.data('paramsCount');
                    data.initConditionsCount = data.wrapper.data('conditionsCount');
                    data.selectThenType = $(data.opt.selectThenType);
                    data.actionNode = $(data.opt.actionNode);
                    data.paramsNode = $(data.opt.paramsNode);
                    data.conditionsNode = $(data.opt.conditionsNode);
                    data.addParamButton = $(data.opt.addParamButton);
                    data.addConditionButton = $(data.opt.addConditionButton);


                    const currentValuesElement = $(data.opt.currentValuesElement);

                    data.currentType = currentValuesElement.data('currentType') || null;
                    data.currentAction = currentValuesElement.data('currentAction') || null;
                    data.currentParams = JSON.parse(currentValuesElement.text());

                    methods.bindEvents();
                    //methods.initializeFields();
                },
                bindEvents: function () {
                    data.selectThenType.on('change', methods.handleTypeChange);
                    data.addParamButton.on('click', function () { methods.addElement('params'); });
                    data.addConditionButton.on('click', function () { methods.addElement('conditions'); });
                    methods.bindRemoveEvent(data.conditionsNode, 'condition', 'conditionsCount', data.addConditionButton);
                    methods.bindRemoveEvent(data.paramsNode, 'param', 'paramsCount', data.addParamButton);
                    data.wrapper.on('change', 'select[name="then_action"]', function () {
                        let selectedAction = $(this).val();
                        let before = $(this).data('previousValue') || selectedAction;

                        if (methods.hasConditions()) {
                            let need_ask = confirm('Вы действительно желаете изменить действие? Установленные условия будут сброшены.');
                            if (!need_ask) {
                                $(this).val(before);
                                return false;
                            }
                        }

                        $(this).attr('data-previous-value', selectedAction);

                        data.wrapper.find('.autotask_then_params-wrapper').addClass('hidden');
                        data.wrapper.find('.autotask_then_condition-wrapper').addClass('hidden');

                        if (selectedAction == 'create') {
                            data.wrapper.find('.autotask_then_condition-wrapper .autotask_then_conditions').html('');
                            data.wrapper.find('.autotask_then_params-wrapper').removeClass('hidden');
                        } else if (selectedAction == 'update') {
                            data.wrapper.find('.autotask_then_condition-wrapper').removeClass('hidden');
                            data.wrapper.find('.autotask_then_params-wrapper').removeClass('hidden');
                        }else {
                            data.wrapper.find('.autotask_then_condition-wrapper .autotask_then_conditions').html('');
                            data.wrapper.find('.autotask_then_params-wrapper .autotask_then_params').html('');
                        }
                    });
                },
                bindRemoveEvent: function (node, type, countKey, addButton) {
                    node.on('click', '.remove', function () {
                        $(this).closest(`.autotask_then_${type}`).remove();

                        if (data.wrapper.find(`.autotask_then_${type} select[id="main-select"]`).length < data[countKey]) {
                            addButton.removeClass('hidden');
                        }
                    });
                },
                handleTypeChange: function () {
                    let selectedType = $(this).val();
                    let before = $(this).data('previousValue') || selectedType;

                    if (methods.hasContent()) {
                        let need_ask = confirm('Вы действительно желаете изменить тип? Установленные условия будут сброшены.');
                        if (!need_ask) {
                            $(this).val(before);
                            return false;
                        }
                    }

                    $(this).attr('data-previous-value', selectedType);

                    if (selectedType != 0) {
                        methods.getData(selectedType, data.getTypesUrl, data.actionNode);

                        const variablesList = data.wrapper.find('#variables-list');
                        if (variablesList.find('li').length > 0) {
                            data.wrapper.find('.then_vars').removeClass('hidden');
                        } else {
                            data.wrapper.find('.then_vars').addClass('hidden');
                        }
                    }else {
                        data.wrapper.find('.autotask_then_params-wrapper').addClass('hidden');
                        data.wrapper.find('.autotask_then_params-wrapper .autotask_then_params').html('');
                        data.wrapper.find('.autotask_then_condition-wrapper').addClass('hidden');
                        data.wrapper.find('.autotask_then_condition-wrapper .autotask_then_conditions').html('');
                        data.wrapper.find('.then_vars').addClass('hidden');
                        data.actionNode.html('');
                    }

                    data.paramsNode.html('');
                },
                hasContent: function () {
                    return data.conditionsNode.find('.autotask_then_condition').length > 0 ||
                        data.paramsNode.find('.autotask_then_param').length > 0;
                },
                hasConditions: function () {
                    return data.conditionsNode.find('.autotask_then_condition').length > 0;
                },
                getData: function (type, url, node, callback) {
                    $.ajaxQuery({
                        url: url,
                        data: {
                            then_type: type,
                            if_type: $("select[name='if_type']").val()
                        },
                        success: function (response) {
                            node.html(response.html);
                            if (callback) callback();
                        }
                    });
                },
                addElement: function (type, key = null, value = null) {
                    let isParam = type === 'params';
                    let existingElements = data.wrapper
                        .find(`.autotask_then_${type} select[id="main-select"]`)
                        .map(function () {
                            return $(this).val();
                        })
                        .get()
                        .filter(Boolean);

                    $.ajaxQuery({
                        url: isParam ? data.getParamsUrl : data.getConditionUrl,
                        data: {
                            type: data.selectThenType.val(),
                            existingParams: existingElements
                        },
                        success: function (response) {
                            data.paramsData = response.params;
                            let countKey = isParam ? 'paramsCount' : 'conditionsCount';
                            data[countKey] = response.params_count;

                            let existingCount = data.wrapper.find(`.autotask_then_${type} select[id="main-select"]`).length;
                            if (existingCount + 1 >= data[countKey]) {
                                (isParam ? data.addParamButton : data.addConditionButton).addClass('hidden');
                            }

                            let wrapperDiv = $('<div>').addClass(`m-b-10 autotask_then_${type.slice(0, -1)}`);

                            let mainSelect = $('<select>')
                                .attr('id', 'main-select')
                                .addClass('m-r-10')
                                .append($('<option>').val('').text('Выберите параметр').prop('selected', true));

                            $.each(data.paramsData, function (keyOption, param) {
                                mainSelect.append($('<option>').val(keyOption).text(param));
                            });

                            let deleteButton = $('<a>')
                                .addClass('remove zmdi zmdi-delete')
                                .on('click', function () {
                                    wrapperDiv.remove();
                                    if (
                                        data.wrapper.find(`.autotask_then_${type} select[id="main-select"]`).length < data[countKey]
                                    ) {
                                        (isParam ? data.addParamButton : data.addConditionButton).removeClass('hidden');
                                    }
                                });

                            let paramDiv = $('<div>')
                                .addClass(`m-b-25 p-b-25 m-t-10 autotask_then_${type}_data`)
                                .css('border-bottom', '1px solid #ededed');

                            wrapperDiv.append(mainSelect, deleteButton);

                            mainSelect.on('change', event => {
                                if (event.target.value) {
                                    const paramDataNode = wrapperDiv.find(`.autotask_then_${type}_data`);

                                    let fieldName = `then_params_arr[${type}][${event.target.value}][key]`;

                                    // Если это кастомное поле, меняем логику формирования имени
                                    if (event.target.value.includes('custom_fields[')) {
                                        const customFieldKey = event.target.value.replace('custom_fields[', '').replace(']', ''); // Получаем название поля (например, 'test2')
                                        fieldName = `then_params_arr[${type}][custom_fields][${customFieldKey}][key]`; // Формируем правильное имя
                                    }

                                    mainSelect.attr('name', fieldName);

                                    $.ajaxQuery({
                                        url: data.getParamDataUrl,
                                        data: {
                                            type: data.selectThenType.val(),
                                            action: $(data.opt.selectThenAction).val(),
                                            field: event.target.value,
                                            params_type: type,
                                        },
                                        success: function (response) {
                                            paramDataNode.html(response.html).trigger('new-content');
                                        }
                                    });
                                } else {
                                    mainSelect.attr('name', '');
                                }
                            });

                            wrapperDiv.append(paramDiv);

                            (isParam ? data.paramsNode : data.conditionsNode).append(wrapperDiv);

                            if (typeof key === 'string' || typeof key === 'number') {
                                mainSelect.val(key).trigger('change');
                            } else {
                                mainSelect.val('').trigger('change');
                            }
                        }
                    });
                },
            };

            if (methods[method]) {
                methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                methods.init.apply(this, args);
            }
        });
    };
})(jQuery);