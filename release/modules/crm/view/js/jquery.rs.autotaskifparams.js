/**
 * Плагин инициализирует в административной панели работу функционала добавления параметров условия автозадачи
 *
 * @author ReadyScript lab.
 */
(function ($) {
    $.fn.autoTaskIfParams = function (method) {
        let defaults = {
                wrapper: '.autotask-if-wrapper',
                thenWrapper: '.autotask-then-wrapper',
                selectIfType: 'select[name="if_type"]',
                actionNode: '.autotask_if_action',
                paramsNode: '.autotask_if_params',
                addParamButton: '#addParamIf',
                currentValuesElement: '[rel="current-values-if"]',
                getTypesUrl: null,
                getParamsUrl: null,
                paramsData: null,
                paramsCount: 0,
                paramsLength: 0,
                initParamsCount: 0,
                currentType: null,
                currentAction: null,
                currentParams: null
            },
            args = arguments;

        return this.each(function () {
            let $this = $(this),
                data = $this.data('autoTaskIfParams');

            let methods = {
                init: function (initoptions) {
                    if (data) return;
                    data = {};
                    $this.data('autoTaskIfParams', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    data.wrapper = $(data.opt.wrapper);
                    data.thenWrapper = $(data.opt.thenWrapper);
                    data.getTypesUrl = data.wrapper.data('getTypesUrl');
                    data.getParamsUrl = data.wrapper.data('getParamsUrl');
                    data.initParamsCount = data.wrapper.data('paramsCount');
                    data.selectIfType = $(data.opt.selectIfType);
                    data.actionNode = $(data.opt.actionNode);
                    data.paramsNode = $(data.opt.paramsNode);
                    data.addParamButton = $(data.opt.addParamButton);

                    const currentValuesElement = $(data.opt.currentValuesElement);

                    data.currentType = currentValuesElement.data('currentType') || null;
                    data.currentAction = currentValuesElement.data('currentAction') || null;
                    data.currentParams = JSON.parse(currentValuesElement.text());

                    methods.bindEvents();
                },
                bindEvents: function () {
                    data.selectIfType.on('change', methods.handleTypeChange);
                    data.addParamButton.on('click', methods.addParam);
                    data.paramsNode.on('click', '.remove', function () {
                        $(this).closest('.autotask_if_param').remove();

                        if (data.wrapper.find('.autotask_if_params select[id="main-select"]').length < data.initParamsCount) {
                            data.addParamButton.removeClass('hidden');
                        }
                    });
                    data.paramsNode.on('click', '.multi-input-add', function () {
                        const valuesContainer = $(this).closest('.multi-input-wrapper').find('.multi-input-values');
                        const selectedValue = $(this).closest('.multi-input-wrapper').closest('.autotask_if_param').find('#main-select').val();
                        if (valuesContainer && selectedValue) {
                            valuesContainer.append(methods.createInputRow('', false, valuesContainer, selectedValue));
                        }
                    });

                    data.paramsNode.on('click', '.multi-input-remove', function () {
                        $(this).closest('.multi-input-line').remove();
                    });
                    data.wrapper.on('change', 'select[name="if_action"]', function () {
                        let selectedAction = $(this).val();
                        let before = $(this).data('previousValue') || selectedAction;

                        if (methods.hasContent()) {
                            let need_ask = confirm('Вы действительно желаете изменить действие? Установленные параметны будут сброшены.');
                            if (!need_ask) {
                                $(this).val(before);
                                return false;
                            }
                        }

                        $(this).attr('data-previous-value', selectedAction);

                        data.wrapper.find('.autotask_if_action-wrapper .autotask_if_params').html('');

                        if (!selectedAction) {
                            data.wrapper.find('.autotask_if_action-wrapper').addClass('hidden');
                        }else {
                            if (data.paramsLenght > 0) {
                                data.addParamButton.removeClass('hidden');
                                data.wrapper.find('.autotask_if_action-wrapper').removeClass('hidden');
                            }
                        }
                    });
                },
                handleTypeChange: function () {
                    let selectedType = $(this).val();
                    let before = $(this).data('previousValue') || selectedType;

                    if (methods.hasConditions()) {
                        let need_ask = confirm('Вы действительно желаете изменить объект условия? Все установленные параметры для действия будут сброшены.');
                        if (!need_ask) {
                            $(this).val(before);
                            return false;
                        }
                        $(data.thenWrapper).find("select[name='then_type']").val('');
                        $(data.thenWrapper).find(".autotask_then_action").html('');
                        $(data.thenWrapper).find(".then_vars").addClass('hidden');
                        $(data.thenWrapper).find(".autotask_then_condition-wrapper").addClass('hidden');
                        $(data.thenWrapper).find(".autotask_then_conditions").html('');
                        $(data.thenWrapper).find(".autotask_then_params-wrapper").addClass('hidden');
                        $(data.thenWrapper).find(".autotask_then_params").html('');
                    }

                    const variablesList = $(data.thenWrapper).find('#variables-list');

                    if (selectedType != 0) {
                        methods.getData(selectedType, data.getTypesUrl, data.actionNode, response => {
                            data.wrapper.find('.autotask_if_action-wrapper').addClass('hidden');
                            if (response?.params) {
                                data.paramsLenght = Object.keys(response.params).length;
                                //data.wrapper.find('.autotask_if_action-wrapper').removeClass('hidden');
                            }
                            variablesList.empty();
                            if (response?.vars && Object.keys(response.vars).length > 0) {
                                Object.keys(response.vars).forEach(varName => {
                                    const title = response.vars[varName]; // Получаем заголовок для переменной
                                    const listItem = `<li>{${varName}} - ${title}</li>`; // Формируем строку для элемента <li>
                                    variablesList.append(listItem); // Добавляем <li> в <ul>
                                });
                            }
                        });
                    }else {
                        data.paramsLenght = 0;
                        variablesList.empty();
                        data.wrapper.find('.autotask_if_action-wrapper').addClass('hidden');
                        data.actionNode.html('');
                    }

                    data.paramsNode.html('');
                },
                hasContent: function () {
                    return data.paramsNode.find('.autotask_if_param').length > 0;
                },
                hasConditions: function () {
                    const thenTypeSelect = $(data.thenWrapper).find("select[name='then_type']");
                    if (thenTypeSelect && thenTypeSelect.val()) {
                        return true;
                    }
                },
                createInputRow: function (val = '', isFirst = false, valuesContainer, selectedValue) {
                    const line = $('<div class="multi-input-line form-inline m-b-5">');
                    const inputGroup = $('<div class="input-group">').append(
                        $('<input type="text" class="form-control">')
                            .attr('name', `if_params_arr[${selectedValue}][value][]`)
                            .val(val)
                    );
                    line.append(inputGroup);

                    if (isFirst) {
                        const addBtn = $('<a class="btn f-18 multi-input-add" title="Добавить значение"><i class="zmdi zmdi-plus"></i></a>');
                        addBtn.on('click', () => {
                            valuesContainer.append(methods.createInputRow('', false, valuesContainer));
                            methods.updateButtons(valuesContainer);
                        });
                        line.append(addBtn);
                    } else {
                        const removeBtn = $('<a class="btn f-18 c-red multi-input-remove" title="Удалить"><i class="zmdi zmdi-close"></i></a>');
                        removeBtn.on('click', () => {
                            line.remove();
                            methods.updateButtons(valuesContainer);
                        });
                        line.append(removeBtn);
                    }

                    return line;
                },
                updateButtons: function (valuesContainer) {
                    const lines = valuesContainer.find('.multi-input-line');
                    lines.each((i, el) => {
                        const $el = $(el);
                        $el.find('.multi-input-add, .multi-input-remove').remove();

                        if (i === 0) {
                            const addBtn = $('<a class="btn f-18 multi-input-add" title="Добавить значение"><i class="zmdi zmdi-plus"></i></a>');
                            addBtn.on('click', () => {
                                valuesContainer.append(methods.createInputRow('', false, valuesContainer));
                                methods.updateButtons(valuesContainer);
                            });
                            $el.append(addBtn);
                        } else {
                            const removeBtn = $('<a class="btn f-18 c-red multi-input-remove" title="Удалить"><i class="zmdi zmdi-close"></i></a>');
                            removeBtn.on('click', () => {
                                $el.remove();
                                methods.updateButtons(valuesContainer);
                            });
                            $el.append(removeBtn);
                        }
                    });
                },
                addParam: function (key = null, value = null) {
                    let existingParams = data.wrapper
                        .find('.autotask_if_params select[id="main-select"]')
                        .map(function () {
                            return $(this).val();
                        })
                        .get()
                        .filter(Boolean);

                    $.ajaxQuery({
                        url: data.getParamsUrl,
                        data: {
                            type: data.selectIfType.val(),
                            action: $('select[name="if_action"]').val(),
                            existingParams: existingParams
                        },
                        success: function (response) {
                            data.paramsData = response.params;
                            data.paramsCount = response.params_count;

                            let existingParamsCount = data.wrapper.find('.autotask_if_params select[id="main-select"]').length;
                            if (existingParamsCount + 1 >= data.paramsCount) {
                                data.addParamButton.addClass('hidden');
                            }

                            let wrapperDiv = $('<div>').addClass('m-b-10 autotask_if_param');

                            let mainSelect = $('<select>')
                                .attr('id', 'main-select')
                                .addClass('m-r-10')
                                .append($('<option>').val('').text('Выберите параметр').prop('selected', true)); // Устанавливаем "Выберите параметр" по умолчанию

                            $.each(data.paramsData, function (keyOption, param) {
                                mainSelect.append($('<option>').val(keyOption).text(param.label));
                            });

                            let deleteButton = $('<a>')
                                .addClass('remove zmdi zmdi-delete')
                                .on('click', function () {
                                    wrapperDiv.remove();
                                    if (
                                        data.wrapper.find('.autotask_if_params select[id="main-select"]').length < data.paramsCount
                                    ) {
                                        data.addParamButton.removeClass('hidden');
                                    }
                                });

                            wrapperDiv.append(mainSelect);

                            mainSelect.on('change', function () {
                                let selectedValue = $(this).val();

                                $(this).parent().find('select:not(#main-select), input, label').remove();

                                if (selectedValue && data.paramsData[selectedValue]) {
                                    let deleteButtonToEnd = true;

                                    mainSelect.attr('name', `if_params_arr[${selectedValue}][key]`);

                                    let additionalField = '';
                                    if (data.paramsData[selectedValue].type === 'select') {
                                        additionalField = $('<select>')
                                            .append($('<option>').val('').text('Выберите значение').prop('selected', true)); // Устанавливаем "Выберите значение" по умолчанию

                                        $.each(data.paramsData[selectedValue].values, function (valueKey, valueText) {
                                            additionalField.append($('<option>').val(valueKey).text(valueText));
                                        });
                                    } else if (data.paramsData[selectedValue].type === 'input') {
                                        if (data.paramsData[selectedValue].multiple) {
                                            const inputWrapper = $('<div class="multi-input-wrapper m-t-10">');
                                            const valuesContainer = $('<div class="multi-input-values">');
                                            valuesContainer.append(methods.createInputRow(value || '', true, valuesContainer, selectedValue));

                                            inputWrapper.append(valuesContainer);
                                            additionalField = inputWrapper;
                                            deleteButtonToEnd = false;
                                        } else {
                                            additionalField = $('<input>')
                                                .attr('type', 'text')
                                                .addClass('form-control');
                                        }
                                    }else if (data.paramsData[selectedValue].type === 'checkbox') {
                                        const hiddenCheckedField = $('<input>')
                                        .attr('type', 'hidden')
                                        .attr('value', 'off')
                                        .attr('name', `if_params_arr[${selectedValue}][value]`);

                                        wrapperDiv.append(hiddenCheckedField)
                                        additionalField = $('<input>')
                                            .attr('type', 'checkbox');
                                    }else if (data.paramsData[selectedValue].type === 'days') {
                                        const dayValues = data.paramsData[selectedValue].values;

                                        const wrapper = $('<div>').addClass('days-selector').addClass(selectedValue);

                                        $.each(dayValues, function(dayValue, dayLabel) {
                                            const id = `day_${selectedValue}_${dayValue}`;

                                            const checkbox = $('<input>')
                                                .attr({
                                                    type: 'checkbox',
                                                    id: id,
                                                    name: `if_params_arr[${selectedValue}][value][]`,
                                                    value: dayValue
                                                });

                                            const label = $('<label>')
                                                .attr('for', id)
                                                .addClass('day-label')
                                                .text(dayLabel);

                                            wrapper.append(checkbox).append(label);
                                        });

                                        deleteButtonToEnd = false;
                                        additionalField = wrapper;
                                    }else if (data.paramsData[selectedValue].type === 'time') {
                                        additionalField = $('<input>')
                                            .attr('type', 'time');
                                    }
                                    if (!data.paramsData[selectedValue].multiple) {
                                        additionalField
                                            .addClass('m-r-10')
                                            .attr('name', `if_params_arr[${selectedValue}][value]`);
                                    }

                                    if (value) {
                                        additionalField.val(value);
                                    }

                                    wrapperDiv.append(deleteButtonToEnd ? additionalField : deleteButton)
                                              .append(deleteButtonToEnd ? deleteButton : additionalField);
                                } else {
                                    mainSelect.attr('name', '');
                                    wrapperDiv.append(deleteButton);
                                }
                            });

                            wrapperDiv.append(deleteButton);

                            data.paramsNode.append(wrapperDiv);

                            if (typeof key === 'string' || typeof key === 'number') {
                                mainSelect.val(key).trigger('change');
                            } else {
                                mainSelect.val('').trigger('change');
                            }
                        }
                    });
                },
                getData: function (type, url, node, callback) {
                    $.ajaxQuery({
                        url: url,
                        data: { type: type },
                        success: function (response) {
                            node.html(response.html);
                            if (callback) callback(response);
                        }
                    });
                }
            };

            if (methods[method]) {
                methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                methods.init.apply(this, args);
            }
        });
    };
})(jQuery);