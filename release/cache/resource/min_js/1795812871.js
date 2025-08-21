/**
 * Плагин, активирует древовидный выпадающий список
 *
 * @author ReadyScript lab.
 */
(function ($) {
    $.fn.treeSelect = function (data) {
        if ($(this).data('treeSelect')) return false;
        $(this).data('treeSelect', {});

        let defaults = {
            selectedBox: '.tree-select_selected-box',
            selectedValues: '.tree-select_selected-values',
            selectedValueItem: '.tree-select_selected-value-item',
            selectedValueItemTitlePath: '.tree-select_selected-value-item_title-path',
            selectedValueItemRemove: '.tree-select_selected-value-item_remove',
            selectedValueStub: '.tree-select_selected-value-stub',
            searchInput: '.tree-select_search-input',
            list: '.tree-select_list',
            listItem: '.tree-select_list-item',
            listItemRow: '.tree-select_list-item_row',
            listItemTitle: '.tree-select_list-item_title',
            listItemSublistToggle: '.tree-select_list-item_sublist-toggle',
            listItemSublist: '.tree-select_list-item_sublist',
            classTitleContractionSeparator: 'tree-select_title-contraction-separator',
            classOpen: 'open',
            classChecked: 'checked',
            classClosedBranch: 'tree-collapsed',
            classOpenedBranch: 'tree-expanded',
            classNeedInitialize: 'need-initialize',
            classLoading: 'loading',
            classHidden: 'hidden',
            classTreeLeaf: 'tree-leaf',
            eventNameChange: 'treeSelectChange'
        };

        let $this = $(this);
        $this.options = $.extend({}, defaults, data);
        $this.options.multiple = $this.attr('multiple');
        $this.options.disallow_select_branches = $this.attr('disallowSelectBranches');
        $this.lastClicked = false;

        $this
        // нажатие на выбранное значение
            .on('click', $this.options.selectedValueItem, function () {
                if ($this.options.multiple) {
                    openDropBox();
                } else {
                    toggleDropBox();
                }
                goToValue($(this).data('id'));
            })
            // удаление выбранного значения
            .on('click', $this.options.selectedValueItemRemove, function () {
                removeValue($(this).closest($this.options.selectedValueItem).data('id'));
                return false;
            })
            // нажатие на список выбранных значений
            .on('click', $this.options.selectedBox, function (event) {
                if (!$(event.target).closest($this.options.selectedValueItem).length) {
                    toggleDropBox();
                }
            })
            // открытие/закрытие ветви дерева
            .on('click', $this.options.listItemSublistToggle, function () {
                let row = $(this).closest($this.options.listItem);
                row.toggleClass($this.options.classClosedBranch).toggleClass($this.options.classOpenedBranch);
                if (row.hasClass($this.options.classNeedInitialize)) {
                    loadNodes([row.data('id')]);
                }
            })
            // выбор узла дерева
            .on('click', $this.options.listItemRow, function (event) {
                if (!$(event.target).is($this.options.listItemSublistToggle)) {
                    let row = $(this).closest($this.options.listItem);
                    let id = row.data('id');

                    if (!$this.options.disallow_select_branches || row.hasClass($this.options.classTreeLeaf)) {
                        if ($this.options.multiple) {
                            let ids;
                            let in_closed_selector = '.' + $this.options.classClosedBranch + ' ' + $this.options.listItem + '[data-id="' + $this.lastClicked + '"]';
                            if (event.shiftKey && $this.lastClicked !== 0 && $this.lastClicked !== id && !$(in_closed_selector, $this).length) {
                                ids = getIdsSelectedByShift($this.lastClicked, id);
                            } else {
                                ids = [id];
                            }

                            if (row.hasClass($this.options.classChecked)) {
                                removeValue(ids);
                            } else {
                                if (!$($this.options.selectedValueItem, $this).length) {
                                    removeAllValues();
                                }
                                addValue(ids);
                            }

                            $this.lastClicked = id;
                        } else {
                            if (!row.hasClass($this.options.classChecked)) {
                                replaceValue(id);
                            }
                            closeDropBox();
                        }
                    }
                }
            })
            .on('input', $this.options.searchInput, function () {
                loadAllNodes().then(() => {
                    let search_string = $(this).val().toLowerCase();
                    if (search_string) {
                        hideAllListItems();
                        $($this.options.listItem, $this).each(function () {
                            let item_title = $(this).children($this.options.listItemRow).find($this.options.listItemTitle).text().toLowerCase();
                            if (item_title.indexOf(search_string) >= 0) {
                                showListItem($(this).data('id'));
                            }
                        });
                        $($this.options.listItem, $this).removeClass($this.options.classClosedBranch).addClass($this.options.classOpenedBranch);
                    } else {
                        showAllListItems();
                    }
                });
            });
        $('body').on('click', function (event) {
            if (!$(event.target).closest($this).length) {
                closeDropBox();
            }
        });

        //private
        let checkLongValuePath = () => {
            $($this.options.selectedValueItem).each(function () {
                let path = $(this).find($this.options.selectedValueItemTitlePath);

                $('.' + $this.options.classTitleContractionSeparator, this).remove();
                path.children().removeClass($this.options.classHidden);

                let child_length = 0;
                path.children().each(function () {
                    child_length += $(this).width();
                });

                if (path.width() < child_length) {
                    let html = '<span class="' + $this.options.classTitleContractionSeparator + '">. . . ></span>';
                    path.after(html);
                }

                child_length = 0;
                path.children().each(function () {
                    child_length += $(this).width();
                    if (child_length > path.width()) {
                        $(this).addClass($this.options.classHidden);
                    }
                });
            });
        };

        let replaceValue = function (id) {
            removeAllValues();
            addValue(id);
            $this.trigger($this.options.eventNameChange);
        };

        let addValue = function (ids) {
            if (!Array.isArray(ids)) {
                ids = [ids];
            }

            ids.forEach(function(id) {
                let selected_element = $($this.options.selectedValueItem + '[data-id="' + id + '"]', $this);
                let element = $($this.options.listItem + '[data-id="' + id + '"]', $this);
                if (!selected_element.length && element.length) {
                    let path_element = element;
                    let path_name = [];
                    let path_ids = [];
                    do {
                        path_name.push(path_element.children($this.options.listItemRow).find($this.options.listItemTitle).html());
                        path_ids.push(path_element.data('id'));
                        path_element = path_element.parent().closest($this.options.listItem, $this);
                    } while (path_element.length);
                    path_name.reverse();
                    path_ids.reverse();

                    let element_id = element.data('id');
                    let form_name = $this.data('formName');
                    let html = '<li class="tree-select_selected-value-item" data-id="' + element_id + '" data-path-ids=\'' + JSON.stringify(path_ids) + '\'>';
                    html += '<input type="hidden" name="' + form_name + '" value="' + element_id + '">';
                    html += '<span class="tree-select_selected-value-item_title-path">';
                    $.each(path_name.slice(0, -1), function (index, value) {
                        html += '<span class="tree-select_selected-value-item_title-path-part">' + value + '</span>';
                    });
                    html += '</span>';
                    html += '<span class="tree-select_selected-value-item_title-end-part">' + path_name.slice(-1)[0] + '</span>';
                    html += '<i class="tree-select_selected-value-item_remove zmdi zmdi-close"><!----></i>';
                    html += '</li>';

                    $($this.options.selectedValues, $this).append(html);
                    checkListItem(element.data('id'));
                }
            });

            checkLongValuePath();
            $this.trigger($this.options.eventNameChange);
        };

        let addValueStub = () => {
            let html = '<li class="tree-select_selected-value-stub">' + lang.t('- Ничего не выбрано -') + '</li>';
            $($this.options.selectedValues, $this).append(html);
        };

        let removeValue = function (ids) {
            if (!Array.isArray(ids)) {
                ids = [ids];
            }

            ids.forEach(function(id) {
                $($this.options.selectedValueItem + '[data-id="' + id + '"]', $this).remove();
                if (!$($this.options.selectedValueItem, $this).length) {
                    addValueStub();
                }
                uncheckListItem(id);
            });

            $this.trigger($this.options.eventNameChange);
        };

        let removeAllValues = function () {
            $($this.options.selectedValues, $this).html('');
            uncheckAllListItems();
        };

        let getIdsSelectedByShift = function (id_from, id_to) {
            let result = [];
            let temp;
            let element_from = $($this.options.listItem + '[data-id="' + id_from + '"]', $this);
            let element_to = $($this.options.listItem + '[data-id="' + id_to + '"]', $this);

            if (element_from.offset().top > element_to.offset().top) {
                temp = element_to;
                element_to = element_from;
                element_from = temp;
            }

            temp = element_from;
            let skip_element = false;
            let break_id = element_to.data('id');
            while (true) {
                let skipped = skip_element;
                skip_element = false;
                if (!skipped) {
                    result.push(temp.data('id'));
                }

                if (temp.data('id') == break_id) {
                    break;
                }

                if (temp.is('.' + $this.options.classOpenedBranch) && !skipped) {
                    temp = temp.find($this.options.listItem).first();
                } else if (temp.next().length > 0) {
                    temp = temp.next();
                } else if (temp.parent().closest($this.options.listItem).length) {
                    temp = temp.parent().closest($this.options.listItem);
                    skip_element = true;
                } else {
                    break;
                }
            }

            return result;
        };

        let goToValue = function (id) {
            let element = $($this.options.selectedValueItem + '[data-id="' + id + '"]', $this);
            let path_ids = element.data('pathIds');
            let path_branches = path_ids.slice(0, -1);
            loadNodes(path_branches, true).then(() => {
                let list_element = $($this.options.listItem + '[data-id="' + id + '"]', $this);
                let list = list_element.closest($this.options.list, $this);
                let list_top = list.children().first().offset().top;
                list.scrollTop(list_element.offset().top - list_top);
            });
        };

        let loadAllNodes = () => {
            let ids = [];
            $($this.options.listItem + '.' + $this.options.classNeedInitialize, $this).each(function () {
                ids.push($(this).data('id'));
            });
            return loadNodes(ids, false, true);
        };

        let loadNodes = function (ids, open_loaded_branches = false, load_recursive) {
            return new Promise((resolve, reject) => {
                let load_ids = [];
                $.each(ids, function (index, value) {
                    let branch = $($this.options.listItem + '[data-id="' + value + '"]', $this);
                    if (!branch.length || branch.hasClass($this.options.classNeedInitialize)) {
                        branch.addClass($this.options.classLoading).removeClass($this.options.classNeedInitialize);
                        load_ids.push(value);
                    } else if (open_loaded_branches) {
                        branch.removeClass($this.options.classClosedBranch).addClass($this.options.classOpenedBranch);
                    }
                });

                if (load_ids.length) {
                    let data = {
                        ids: load_ids,
                        recursive: load_recursive
                    };

                    $.ajaxQuery({
                        url: $this.data('treeListUrl'),
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                $.each(response.branches, function (index, value) {
                                    let element = $($this.options.listItem + '[data-id="' + index + '"]', $this);
                                    element.find($this.options.listItemSublist).html(value);
                                    element.removeClass($this.options.classLoading).removeClass($this.options.classNeedInitialize);
                                    if (open_loaded_branches) {
                                        element.removeClass($this.options.classClosedBranch).addClass($this.options.classOpenedBranch);
                                    }
                                    highlightSelectedItems();
                                });
                                resolve();
                            } else {
                                reject(response);
                            }
                        }
                    });
                } else {
                    resolve();
                }
            });
        };

        let showListItem = (id) => {
            let element = getListItemById(id);
            do {
                element.removeClass($this.options.classHidden);
                element = element.parent().closest($this.options.listItem, $this);
            } while (element.length);
        };

        let showAllListItems = () => {
            $($this.options.listItem, $this).removeClass($this.options.classHidden);
        };

        let hideAllListItems = () => {
            $($this.options.listItem, $this).addClass($this.options.classHidden);
        };

        let highlightSelectedItems = () => {
            $($this.options.selectedValueItem, $this).each(function () {
                checkListItem($(this).data('id'));
            });
        };

        let uncheckAllListItems = () => {
            $($this.options.listItem, $this).removeClass($this.options.classChecked);
        };

        let checkListItem = (id) => {
            getListItemById(id).addClass($this.options.classChecked);
        };

        let uncheckListItem = (id) => {
            getListItemById(id).removeClass($this.options.classChecked);
        };

        let toggleDropBox = function () {
            $this.toggleClass($this.options.classOpen);
        };

        let openDropBox = function () {
            $this.addClass($this.options.classOpen);
        };

        let closeDropBox = function () {
            $this.removeClass($this.options.classOpen);
        };

        let getListItemById = (id) => {
            return $($this.options.listItem + '[data-id="' + id + '"]', $this);
        };

        checkLongValuePath();
        highlightSelectedItems();
    };

    $.contentReady(function () {
        $('.tree-select').each(function () {
            $(this).treeSelect($(this).data('treeSelectOptions'));
        });
    });
})(jQuery);
/**
    * Plugin, активирующий вкладку "характеристики" у товаров
    */
(function($){
    $.fn.propertyBlock = function(method) {
        var defaults = {
            addButton: '.add-property',
            someAddButton: '.add-some-property',
            collapseButton: '.close',
            saveButton: '.property-table .add',
            someSaveButton: '.some-property-table .add-some',
            propertyForm: '.property-form',
            somePropertyForm: '.some-property-form',
            addNewListValue: '.p-add-new-value',
            removeListValue: '.p-remove-val',
            propertyActions: '.property-actions',
            propertyList: '.property-container',
            propertyLoading: '.ploading',
            propertyItem: '.property-item',
            successText: '.success-text',
            setSelfVal: '.set-self-val',
            errors: {
                title: '.p-title-block .field-error'
            },
            blocks: {
                title: '.p-title-block',
                proplist: '.p-proplist-block',
                values: '.p-values-block',
                val: '.p-val-block',
                value: '.p-value-block',
                group: '.p-group-block',
                listValues: '.p-list-values'
                
            },
            formFields: {
                propertyList: '.p-proplist',
                parent: '.p-parent_id',
                title: '.p-title',
                type: '.p-type',
                unit: '.p-unit',
                values: '.p-values',
                val: '.p-val',
                step: '.p-step',
                newGroup: '.p-new-group',
                useVal: '.h-useval',
                uValue: '.h-val, .h-val-linked',
                hPublic: '.h-public',
                hExpanded: '.h-expanded',
                someProps: '.some-props',
                pNewValue: '.p-new-value'
            },
            tools: {
                edit: '.p-edit',
                del: '.p-del'
            },
            getPropertyUrl: '' //Инициализируется из аттрибута data-get-property-url
            
        }, 
        fullPropertyList,
        whenListLoad = $.Deferred(),
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), options;
            
            var methods = {
                init: function(initoptions) {                    
                    if ($this.data('propertyBlock')) return false;
                    $this.data('propertyBlock', {});
                    options = $.extend({}, defaults, initoptions);
                    options.getPropertyUrl = $this.data('getPropertyUrl');
                    options.getPropertyValueUrl = $this.data('getPropertyValueUrl');
                    options.createPropertyValueUrl = $this.data('createPropertyValueUrl');
                    options.removePropertyValueUrl = $this.data('removePropertyValueUrl');
                    $this
                        .on('click', options.addButton, methods.addProperty)
                        .on('click', options.collapseButton, methods.collapseForm)
                        .on('change', options.formFields.propertyList, onPropertySelect)
                        .on('change', options.formFields.type, onTypeChange)
                        //.on('change', options.formFields.values, onValuesChange)
                        .on('keyup', options.formFields.newGroup, onNewGroupChange)
                        .on('click', options.saveButton, onSave)
                        .on('change', options.formFields.useVal, onUseValChange)
                        .on('click', options.tools.edit, editProperty)
                        .on('click', options.tools.del, deleteProperty)
                        .on('click', options.someAddButton, methods.addSomeProperty)
                        .on('click', options.someSaveButton, onInsertSome)
                        .on('click', options.setSelfVal, onSetSelfVal)
                        .on('click', options.addNewListValue, onAddNewListValue)
                        .on('keypress', options.formFields.pNewValue, function(e) {
                            if ( e.keyCode == 13 ) onAddNewListValue();
                        })
                        .on('click', options.removeListValue, onRemoveListValue);
                        
                    $(options.formFields.useVal, $this).each(onUseValChange);
                    updateTmpId();
                },
                
                addProperty: function() {
                    var hasAct = $(this).hasClass('act');
                    methods.collapseForm();                    
                    if (!hasAct) {
                        $(this).addClass('act');                        
                        cancelEditProperty();
                        $(options.formFields.propertyList).val('new').trigger('change');
                        methods.expandForm();
                    }
                    $this.trigger('contentSizeChanged');
                    return false;
                },
                
                addSomeProperty: function() {
                    var hasAct = $(this).hasClass('act');
                    methods.collapseForm();
                    if (!hasAct) {
                        $(this).addClass('act');
                        cancelEditProperty();
                        loadPropertyList();
                        
                        $(options.somePropertyForm, $this).show();
                        $this.trigger('contentSizeChanged');
                    }                    
                    
                },
                
                expandForm: function() {
                    $form = $(options.propertyForm, $this);
                    loadPropertyList();
                    $form.show();
                    $this.trigger('disableBottomToolbar', 'property-edit');
                },
                
                collapseForm: function() {
                    cancelEditProperty();
                    $(options.propertyActions+' .act').removeClass('act');
                    $(options.somePropertyForm, $this).hide();
                    $this.trigger('enableBottomToolbar', 'property-edit');
                    return false;
                }
            }
            
            //private 
            
            /**
            * Загружаем список свойств
            */
            var loadPropertyList = function()
            {
                if (whenListLoad.state() != 'resolved' || !fullPropertyList) {
                    $(options.propertyLoading).show();
                    $(options.formFields.propertyList).prop('disabled', true);
                    $(options.formFields.parent).prop('disabled', true);
                    $(options.formFields.newGroup).prop('disabled', true);
                    
                    initList(function() {
                        $(options.propertyLoading).hide();
                        $(options.formFields.propertyList).prop('disabled', false);
                        $(options.formFields.parent).prop('disabled', false);
                        $(options.formFields.newGroup).prop('disabled', false);                        
                        
                        fillSelect();
                        whenListLoad.resolve();
                    });
                }
            },

            initList = function(callback)
            {
                $.ajaxQuery({
                    url: options.getPropertyUrl,
                    success: function(response) {                        
                        fullPropertyList = response;
                        callback.call();                        
                    }
                });
            },
            
            fillSelect = function()
            {
                //Загружаем значения в список
                var select = $(options.formFields.propertyList, $this).empty();
                var group_select = $(options.formFields.parent, $this).empty();
                var cur_group;
                var optgroup;
                
                select.append('<option value="new">' + lang.t('Новая характеристика') + '</option>');
                group_select.append('<option value="0">' + lang.t('Без группы') + '</option>');
                
                //Генерируем корректно отсортированный список характеристик
                fullPropertyList.properties = {};
                for(var i in fullPropertyList.properties_sorted) 
                {
                    var item = fullPropertyList.properties_sorted[i];
                    fullPropertyList.properties[item.id] = item;
                    if (cur_group === undefined || parseInt(item.parent_id) != cur_group) {
                        cur_group = parseInt(item.parent_id);
                        optgroup = $('<optgroup></optgroup>').attr({
                                        label: fullPropertyList.groups[cur_group].title
                                    }).appendTo(select);
                    }
                    $('<option></option>').attr('value', item.id).text(item.title).appendTo(optgroup);
                }
                
                group_select.html('');
                for (i in fullPropertyList.groups) {
                    var item = fullPropertyList.groups[i];
                    $('<option></option>').attr('value', i).text(item.title).appendTo(group_select);
                }
                
                //Если присутствует множественная вставка свойств, то заполняем и её
                var $someSelect = $(options.formFields.someProps, $this);
                if ($someSelect.length) {
                    select.children().clone().appendTo($someSelect.empty());
                    $('option[value="new"]:first', $someSelect).remove();
                    $someSelect.removeAttr('disabled');
                }
                
                $(options.saveButton, $this).removeClass('disabled');
                $(options.someSaveButton, $this).removeClass('disabled');
            },
            
            onNewGroupChange = function()
            {
                $(options.formFields.parent).prop('disabled', $(this).val() != '');
                
            },
            
                
            decode = function(encodedStr) {
                return $("<div/>").html(encodedStr).text();
            },             
            
            onPropertySelect = function()
            {
                var index = $(this).val();
                if (index == 'new' || typeof(fullPropertyList) == 'undefined' ) {
                    var params = {
                        title: '',
                        type: 'string',
                        values: '',
                        unit: '',
                    }
                } else {
                    var params = fullPropertyList.properties[index];
                };
                
                //Устанавливаем значения в диалоговом окне
                $(options.formFields.title, $this).val( decode(params.title) );
                $(options.formFields.type, $this).val( params.type );
                $(options.formFields.values, $this).val( decode(params.values) );
                $(options.formFields.unit, $this).val( decode(params.unit) );
                $(options.formFields.step, $this).val( params.step );
                $(options.formFields.parent, $this).val( params.parent_id );
                
                if (index == 'new') {
                    //Режим создания нового свойства
                    $(options.blocks.value+','+options.blocks.title+','+options.blocks.group, $this).show();
                    //$(options.formFields.type +','+ options.formFields.parent +','+ options.formFields.title, $this).removeAttr('disabled');
                    $(options.blocks.proplist+','+options.blocks.group , $this).show();                    
                    $('.p-type-block', $this).show();
                    $(options.saveButton, $this).text( $(options.saveButton, $this).data('addText'));
                } else {
                    if ($(options.propertyForm, $this).data('propertyEditMode')) {
                        //Режим редактирования
                        $(options.blocks.title).show();
                        $(options.blocks.value+','+options.blocks.proplist+','+options.blocks.group , $this).hide();
                        $(options.formFields.type, $this).removeAttr('disabled');
                        $(options.saveButton, $this).text( $(options.saveButton, $this).data('editText'));
                    } else {
                        //Режим добавления
                        $(options.blocks.title+','+options.blocks.group, $this).hide();
                        $(options.saveButton, $this).text( $(options.saveButton, $this).data('addText'));
                        //$(options.formFields.type+','+options.formFields.parent, $this).attr('disabled','disabled');
                    }
                    $('.p-type-block', $this).hide();
                }
                
                onTypeChange();
            },
            
            onTypeChange = function()
            {
                var prop_id = $(options.formFields.propertyList, $this).val();
                var el = $(options.formFields.type, $this);
                var is_list = $('option:selected', el).data('isList');
                var edit_mode = $(options.propertyForm, $this).data('propertyEditMode');
                
                //Определяем контейнер для формы со значениями
                var value_container = edit_mode ? $('.now-edit .item-val', $this) : $('.p-val-block', $this);
                
                if (is_list) {
                    $('.p-new-value-block', $this).show();
                    $('.p-value-block', $this).toggle(!edit_mode);
                } else {
                    $('.p-new-value-block', $this).hide();
                    if (!edit_mode) {
                        $('.p-value-block', $this).show();
                    }
                }
                
                //В режиме редактирования тип не изменяется, поэтому не обновляем поля со значениями
                if (edit_mode) return;
                
                //Получаем соответствующую типу характеристики форму
                var new_input = getInputByType(el.val(), is_list, '', edit_mode);
                value_container.empty().append(new_input);                
                
                if (is_list) {                                    
                    //Подгружаем значения характеристики
                    if (prop_id != 'new' && fullPropertyList) {
                        var loader = $('<div class="loading-p-values">' + lang.t('Загрузка...') + '</div>').appendTo(new_input);
                                            
                        var prop_params = fullPropertyList.properties[prop_id];                    
                        //Если характеристика списковая, то загрузим значения
                        if (fullPropertyList.types[prop_params.type].is_list) {
                            if (typeof(prop_params.list_values) == 'undefined') {
                                $.ajaxQuery({
                                    url:options.getPropertyValueUrl,
                                    data:{
                                        prop_id: prop_id
                                    },
                                    success: function(response) {
                                        loader.remove();                                        
                                        prop_params.list_values = response.property_values;
                                        showListValues(prop_params.list_values, value_container);
                                    }
                                });
                            } else {
                                loader.remove();                                
                                showListValues(prop_params.list_values, value_container);
                            }
                        }
                    }
                    checkEmptyListValues();
                }
            },
            
            onAddNewListValue = function() {
                var prop_id = $(options.formFields.propertyList, $this).val();
                var new_value = $(options.formFields.pNewValue).val();
                
                //Если характеристика новая, то используем временный ID характеристики
                if (prop_id == 'new') prop_id = $this.data('tmpId');
                
                $.ajaxQuery({
                    method:'POST',
                    url:options.createPropertyValueUrl,
                    data: {
                        prop_id: prop_id,
                        value:new_value
                    },
                    success: function(response) {
                        if (response.success) {
                            response.item_value.is_checked = true;
                            var edit_mode = $(options.propertyForm, $this).data('propertyEditMode');
                            var item = getOneListValue(response.item_value, edit_mode);
                            
                            if (edit_mode) {
                                // большие списки сами обрабатывают добавление новых значений
                                let big_list = $('.property-item.now-edit .item-val .property-type-big-list');
                                if (big_list.length) {
                                    big_list.propertyTypeBigList('addNewValue', response.item_value.id, response.item_value.value);
                                } else {
                                    $('.property-item.now-edit .item-val').append(item);
                                }
                            } else {
                                $(options.blocks.listValues).append(item);
                            }
                            
                            $(options.formFields.pNewValue).val('');
                            checkEmptyListValues();
                        }
                    }
                });
                
            },
            
            showListValues = function(values, value_container) {
                let ul = $(options.blocks.listValues, value_container).empty();

                if (values.length > 20) {
                    ul.append('<span class="inline-item">' + lang.t('Указать значения можно будет после добавления') + '</span>');
                } else {
                    $.each(values, function(i, data) {
                        let li = getOneListValue(data, false);
                        ul.append(li);
                    });
                }

                checkEmptyListValues();
            },
            
            getOneListValue = function(data, edit_mode) {
                var item = $('<span class="inline-item property-type-list">\
                    <input type="checkbox">\
                    <label></label>\
                    <a class="p-remove-val">&times;</a>\
                </span>');
                
                var input = item.find('input').attr({
                    value: data.id,
                    id:'ch_' + data.id
                })
                .addClass(edit_mode ? 'h-val' : 'p-val')
                .prop('checked', data.is_checked);
                
                if (edit_mode) {
                    input.attr('name', 'prop[' + data.prop_id + '][value][]');
                }
                
                item.find('.p-remove-val').attr('title', lang.t('Удалить значение из характеристики'));
                item.find('label').attr('for', 'ch_' + data.id).text(data.value);
                return item;
            },
            
            onRemoveListValue = function() {
                if (confirm(lang.t('Вы действительно хотите удалить значение списковой характеристики? Связь с этим значением будет удалена у всех товаров.'))) {
                    var value_id = $(this).closest('.inline-item').find('input').val();
                    var _this = this;
                    
                    $.ajaxQuery({
                        method:'POST',
                        url:options.removePropertyValueUrl,
                        data: {
                            id:value_id
                        },
                        success: function(response) {
                            if (response.success) {
                                $(_this).closest('.inline-item').remove();
                                checkEmptyListValues();                                
                            }
                        }
                    });
                    
                }
            },

            getInputByType = function(type, is_list, value, edit_mode)
            {
                var class_name = edit_mode ? 'h-val' : 'p-val';
                if (is_list) {
                    var val_input = $('<div class="p-list-values"></div>');
                } else if (type == 'bool') {
                    var val_input = $('<input type="checkbox" value="1">').addClass(class_name).prop('checked', value != '');
                } else {
                    var val_input = $('<input type="text">').addClass(class_name).val(value);
                }
                
                return val_input;
            },
            
            checkEmptyListValues = function() {
                            
                if (!$('.p-list-values').children().length) {
                    $('<div class="p-list-empty">' + lang.t('нет значений') + '</div>').appendTo($('.p-list-values', $this));
                } else {
                    $('.p-list-empty', $this).remove();
                }
            },
             
            onInsertSome = function()
            {
                if ($(this).hasClass('disabled')) return false;
                
                var ids = [];
                $(options.formFields.someProps+' option:selected', $this).each(function() {
                    ids.push({
                        name: 'ids[]',
                        value: $(this).val()
                    });
                });
                
                $.ajaxQuery({
                    url: $this.data('getSomeProperties'),
                    type:'POST',
                    data: ids,
                    success: function(response) {
                        for(var i in response.result) {
                            if (!$(options.propertyItem+'[data-property-id="'+response.result[i].prop.id+'"]', $this).length) {
                                if (response.result[i].group.length == 0) {
                                    response.result[i].group.id = 0;
                                }
                                
                                var target_group = $('tbody[data-group-id="'+response.result[i].group.id+'"]', $this);
                                
                                if ( target_group.length ) {
                                    target_group.append(response.result[i].property_html);
                                } else {
                                    $(options.propertyList, $this).append( response.result[i].group_html );
                                    $('tbody[data-group-id="'+response.result[i].group.id+'"]', $this)
                                        .append(response.result[i].property_html)
                                        .trigger('new-content');
                                }
                                
                            }
                        }
                    }
                });
            },
            
            /**
            * Устанавливает tmp_id, которое будет использоваться при создании новой характеристики
            */
            updateTmpId = function() {
                var timestamp = new Date().getTime();
                $this.data('tmpId', -Math.floor(timestamp/1000));
            },
            
            onSave = function()
            {
                if ($(this).hasClass('disabled')) return false;
                
                var $form = $(options.propertyForm, $this);
                var $item = $form.data('propertyItem'), 
                    $context,
                    edit_mode = $form.data('propertyEditMode');
                
                if (edit_mode) {
                    val_class = '.h-val'; $context = $item;
                } else {
                    val_class = '.p-val'; $context = $form;
                }
                
                var item = {
                    title:          $(options.formFields.title, $form).val(),
                    type:           $(options.formFields.type, $form).val(),
                    values:         $(options.formFields.values, $form).val(),
                    value:          $(val_class, $context).val(),
                    unit:           $(options.formFields.unit, $form).val(),
                    step:           $(options.formFields.step, $form).val(),
                    parent_id:      $(options.formFields.parent, $form).val(),
                };
                if (!edit_mode) {
                    item['new_group_title'] = $(options.formFields.newGroup, $form).val();
                }
                var val_class;
                
                var id = $(options.formFields.propertyList, $form).val();
                var is_list = $(options.formFields.type+' option:selected', $form).data('isList');
                item.id = (id == 'new') ? $this.data('tmpId') : id;
                item.is_my = 1;
                item.owner_type = $this.data('ownerType');
                
                if (edit_mode) {
                    item['public'] = $(options.formFields.hPublic+':checked', $item).length>0 ? 1 : 0;
                    item['is_expanded'] = $(options.formFields.hExpanded+':checked', $item).length>0 ? 1 : 0;
                    item['useval'] = $(options.formFields.useVal+':checked', $item).length>0 ? 1 : 0;
                }
                
                if (id > 0) {
                    var exists = $(options.propertyItem+'[data-property-id="'+id+'"]', $this);
                    if (exists.length) {
                        item.is_my = exists.data('isMy'); //Определяем какой шаблон нам должен вернуть сервер
                    }
                }
                
                $(options.formFields.title, $form).removeClass('has-error');
                $(options.errors.title, $form).hide();

                
                if (item.type == 'bool') {
                    var checked = $(val_class+':checked', $context).length > 0;
                    item.value = (+checked);
                }

                if (is_list) {
                    var val_list = [];
                    $(val_class+':checked', $context).each(function() {
                        val_list.push($(this).val());
                    });
                    item.value = val_list;
                }
                
                $.ajaxQuery({
                    url: $this.data('savePropertyUrl'),
                    type:'POST',
                    data: {
                        item: item
                    },
                    success: function(response) {
                        if (response.success) {
                            updateTmpId();
                            methods.collapseForm();                            
                            //Обновляем характеристику в списке
                            if (response.group.length == 0) {
                                response.group.id = 0;
                            }
                            
                            if ($('.property-item[data-property-id="'+response.prop.id+'"]', $this).length) {
                                $('.property-item[data-property-id="'+response.prop.id+'"]', $this).replaceWith(response.property_html);
                            } else {
                                var target_group = $('tbody[data-group-id="'+response.group.id+'"]', $this);
                                if ( target_group.length ) {
                                    target_group.append(response.property_html);
                                } else {
                                    $(options.propertyList, $this).append( response.group_html );
                                    $('tbody[data-group-id="'+response.group.id+'"]', $this)
                                        .append(response.property_html)
                                        .trigger('new-content');
                                }
                            }
                            
                            if (!edit_mode) {
                                $(options.successText, $this).fadeIn();
                                setTimeout(function() {
                                    $(options.successText, $this).fadeOut();
                                }, 7000);
                                
                            }

                            let list_updated = false;
                            for(var j in fullPropertyList.properties_sorted) {
                                if (fullPropertyList.properties[response.prop.id]) {
                                    if (fullPropertyList.properties_sorted[j].id == response.prop.id) {
                                        //Обновляем запись
                                        fullPropertyList.properties_sorted[j] = response.prop;
                                        list_updated = true;
                                    }
                                } else {
                                    if (fullPropertyList.properties_sorted[j].parent_id == response.prop.parent_id) {
                                        //Добавляем запись
                                        fullPropertyList.properties_sorted.splice(j, 0, response.prop);
                                        list_updated = true;
                                        break;
                                    }
                                }
                            }

                            if (!list_updated) {
                                if (response.group.id > 0) {
                                    fullPropertyList.groups[response.group.id] = response.group;
                                }
                                fullPropertyList.properties[response.prop.id] = response.prop;
                                fullPropertyList.properties_sorted.push(response.prop);
                            }
                           
                            fillSelect(); //Обновляем значения в сисках

                            $(options.formFields.useVal, $('.property-item[data-property-id="'+response.prop.id+'"]', $this)).each(onUseValChange);
                        } else {
                            for (var field in response.formdata.errors) {
                                
                                var errors_str = response.formdata.errors[field].errors.join('<br>');
                                if (field == '@system') field = 'title';
                                
                                $('.p-'+field, $this).addClass('has-error');
                                $('.field-error[data-field="'+field+'"]', $this).html(
                                    '<span class="text"><i class="cor"></i>'+errors_str+'</span>'
                                ).show();
                            }
                        }
                        $form.trigger('new-content');
                    }
                })
                
            },
            
            onUseValChange = function()
            {
                var item = $(this).closest('.property-item');
                if ($(this).prop('checked')) {
                    $(options.formFields.uValue, item).removeAttr('disabled');
                } else {
                    $(options.formFields.uValue, item).attr('disabled','disabled');
                }
            },
            
            deleteProperty = function()
            {
                if (confirm(lang.t('Вы действительно хотите удалить характеристику?')))
                {
                    cancelEditProperty();
                    var div = $(this).closest(options.propertyItem);
                    var my_group = div.closest('tbody');
                    div.remove();
                    
                    if (!my_group.children().length) {
                        var group_head = my_group.prev('.group-body[data-gid]').remove();
                        my_group.remove();
                    }                   
                }
            },
            
            cancelEditProperty = function()
            {
                var inEditNow = $('.now-edit', $this);
                $(options.propertyForm, $this).data('propertyEditMode', false).hide();
                $('.has-error', $this).removeClass('has-error');
                $('.field-error', $this).empty().hide();
                
                if (inEditNow.removeClass('now-edit noover').length) {
                    $(options.propertyForm, $this).insertAfter($(options.propertyActions, $this));
                    inEditNow.next('.edit-form').remove();
                }
            },
            
            editProperty = function()
            {
                var item = $(this).closest(options.propertyItem);
                var needOpen = !item.hasClass('now-edit');
                
                methods.collapseForm();
                
                if (needOpen) {
                    var editform = $('<tr class="edit-form noover"><td colspan="6"><div class="bordered"></div></td></tr>');
                    item.addClass('now-edit noover').removeClass('over');
                    var $form = $(options.propertyForm, $this).data({
                        'propertyEditMode': true,
                        'propertyItem': item
                    });
                    editform.insertAfter(item).find('.bordered').append($form);
                    methods.expandForm();
                    
                    whenListLoad.done(function() {
                        $(options.formFields.propertyList).val(item.data('propertyId')).trigger('change');
                    });
                }
                
            },
            trim = function ( str, charlist ) 
            {
                charlist = !charlist ? ' \\s\\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
                var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
                return str.replace(re, '');
            },
            onSetSelfVal = function()
            {
                $(options.formFields.useVal, $this).prop('checked', true).trigger('change');
                return false;
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
    $('#propertyblock').propertyBlock();
});
/**
 * Плагин, активирует древовидный выпадающий список
 *
 * @author ReadyScript lab.
 */
(function ($) {
    $.fn.propertyTypeBigList = function (method) {
        let $this = $(this).data('propertyTypeBigList');
        let search_timeout;

        let methods = {
            addNewValue: (id, value) => {
                addSelectedValue(id, value);
                searchValues();
            },
            init: (data) => {
                if ($this) return false;
                $this = $(this);
                $(this).data('propertyTypeBigList', $this);

                let defaults = {
                    selected: '.property-type-big-list_selected',
                    selectedItem: '.property-type-big-list_selected-item',
                    selectedItemStub: '.property-type-big-list_selected-item-stub',
                    selectedItemRemove: '.property-type-big-list_selected-item-remove',
                    dropBox: '.property-type-big-list_drop-box',
                    searchInput: '.property-type-big-list_search-input',
                    listBox: '.property-type-big-list_list-box',
                    listItem: '.property-type-big-list_list-item',
                    listItemCheckbox: '.property-type-big-list_list-item-checkbox',
                    listItemValue: '.property-type-big-list_list-item-value',
                    listPaginatorPage: '.property-type-big-list_list-paginator-page',
                    listPaginatorPrev: '.property-type-big-list_list-paginator-prev',
                    listPaginatorNext: '.property-type-big-list_list-paginator-next',
                    dropBoxToggle: '.property-type-big-list_drop-box-toggle',
                    classClosed: 'closed',
                };

                $this.options = $.extend({}, defaults, data);
                $this.options.disabled = $this.data('disabled');

                $this
                // удаление выбранного значения
                    .on('click', $this.options.selectedItemRemove, function () {
                        let id = $(this).closest($this.options.selectedItem).data('id');
                        removeSelectedValue(id);
                    })
                    // нажатие на списковое значение
                    .on('change', $this.options.listItemCheckbox, function () {
                        let id = $(this).data('id');
                        let checked = $(this).prop('checked');

                        if (!$($this.options.selectedItem + '[data-id="' + id + '"]', $this).length && checked) {
                            let value = $(this).closest($this.options.listItem).find($this.options.listItemValue).html();
                            addSelectedValue(id, value);
                        } else {
                            removeSelectedValue(id);
                        }
                    })
                    // строка поиска
                    .on('input', $this.options.searchInput, function () {
                        clearTimeout(search_timeout);
                        search_timeout = setTimeout(() => {
                            $($this.options.listPaginatorPage, $this).val(1);
                            searchValues();
                        }, 500);
                    })
                    // номер страницы
                    .on('change', $this.options.listPaginatorPage, function () {
                        searchValues();
                    })
                    // на страницу назад
                    .on('click', $this.options.listPaginatorPrev, function () {
                        let page = Number($($this.options.listPaginatorPage, $this).val());
                        if (page > 1) {
                            $($this.options.listPaginatorPage, $this).val(page - 1).change();
                        }
                    })
                    // на страницу вперёд
                    .on('click', $this.options.listPaginatorNext, function () {
                        let page = Number($($this.options.listPaginatorPage, $this).val());
                        if (page < $($this.options.listPaginatorPage, $this).data('max')) {
                            $($this.options.listPaginatorPage, $this).val(page + 1).change();
                        }
                    })
                    // открытие/закрытие блока значений
                    .on('click', $this.options.dropBoxToggle, function () {
                        $($this.options.dropBox, $this).toggleClass($this.options.classClosed);
                    });

                checkListValues();
            }
        };



        //private
        let searchValues = () => {
            let query = $($this.options.searchInput, $this).val();
            let page = $($this.options.listPaginatorPage, $this).val();
            let data = {
                query: query,
                page: page,
                disabled: $this.options.disabled
            };

            $.ajaxQuery({
                url: $this.data('searchUrl'),
                data: data,
                success: function (response) {
                    if (response.success) {
                        $($this.options.listBox, $this).html(response.html);
                        checkListValues();
                    }
                }
            });
        };

        let checkListValues = () => {
            $($this.options.selectedItem, $this).each(function () {
                $($this.options.listItemCheckbox + '[data-id="' + $(this).data('id') + '"]', $this).prop('checked', true);
            });
        };

        let removeSelectedValue = (id) => {
            $($this.options.selectedItem + '[data-id="' + id + '"]', $this).remove();
            $($this.options.listItemCheckbox + '[data-id="' + id + '"]', $this).prop('checked', false);

            if (!$($this.options.selectedItem, $this).length) {
                let html = '<span class="property-type-big-list_selected-item-stub">- ' + lang.t('Значения не указаны') + ' -</span>';
                $($this.options.selected, $this).append(html);
            }
        };

        let addSelectedValue = (id, value) => {
            let prop_id = $this.data('propId');
            let disabled = '';
            if ($this.data('disabled')) {
                disabled = 'disabled';
            }

            $($this.options.selectedItemStub, $this).remove();

            let html = '<span class="property-type-big-list_selected-item" data-id="' + id + '"><label>';
            html += '<input type="hidden" name="prop[' + prop_id + '][value][]" class="property-type-big-list_selected-item-checkbox" data-id="' + id + '" ' + disabled + ' value="' + id + '" checked>';
            html += '<span >' + value + '</span>';
            html += '<i class="property-type-big-list_selected-item-remove zmdi zmdi-close"><!----></i>';
            html += '</label></span>';

            $($this.options.selected, $this).append(html);
        };

        //checkListValues();
        if (!method) {
            method = 'init';
        }
        if (methods[method]) {
            methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else {
            console.log('propertyTypeBigList: ' + lang.t('вызов несуществующего метода') + ' "' + method + '"');
        }
    };

    $.contentReady(function () {
        $('.property-type-big-list').each(function () {
            $(this).propertyTypeBigList('init', $(this).data('propertyTypeBigListOptions'));
        });
        $('body').on('new-content',() => {
            $('.property-type-big-list').each(function () {
                $(this).propertyTypeBigList('init', $(this).data('propertyTypeBigListOptions'));
            });
        });
    });
})(jQuery);
/**
 * TableDnD plug-in for JQuery, allows you to drag and drop table rows
 * You can set up various options to control how the system will work
 * Copyright (c) Denis Howlett <denish@isocra.com>
 * Licensed like jQuery, see http://docs.jquery.com/License.
 *
 * Configuration options:
 *
 * onDragStyle
 *     This is the style that is assigned to the row during drag. There are limitations to the styles that can be
 *     associated with a row (such as you can't assign a border--well you can, but it won't be
 *     displayed). (So instead consider using onDragClass.) The CSS style to apply is specified as
 *     a map (as used in the jQuery css(...) function).
 * onDropStyle
 *     This is the style that is assigned to the row when it is dropped. As for onDragStyle, there are limitations
 *     to what you can do. Also this replaces the original style, so again consider using onDragClass which
 *     is simply added and then removed on drop.
 * onDragClass
 *     This class is added for the duration of the drag and then removed when the row is dropped. It is more
 *     flexible than using onDragStyle since it can be inherited by the row cells and other content. The default
 *     is class is tDnD_whileDrag. So to use the default, simply customise this CSS class in your
 *     stylesheet.
 * onDrop
 *     Pass a function that will be called when the row is dropped. The function takes 2 parameters: the table
 *     and the row that was dropped. You can work out the new order of the rows by using
 *     table.rows.
 * onDragStart
 *     Pass a function that will be called when the user starts dragging. The function takes 2 parameters: the
 *     table and the row which the user has started to drag.
 * onDragStop
 *     Pass a function that will be called when the user stops dragging regardless of if the rows have been
 *     rearranged. The function takes 2 parameters: the table and the row which the user was dragging.
 * onAllowDrop
 *     Pass a function that will be called as a row is over another row. If the function returns true, allow
 *     dropping on that row, otherwise not. The function takes 2 parameters: the dragged row and the row under
 *     the cursor. It returns a boolean: true allows the drop, false doesn't allow it.
 * scrollAmount
 *     This is the number of pixels to scroll if the user moves the mouse cursor to the top or bottom of the
 *     window. The page should automatically scroll up or down as appropriate (tested in IE6, IE7, Safari, FF2,
 *     FF3 beta
 * dragHandle
 *     This is a jQuery mach string for one or more cells in each row that is draggable. If you
 *     specify this, then you are responsible for setting cursor: move in the CSS and only these cells
 *     will have the drag behaviour. If you do not specify a dragHandle, then you get the old behaviour where
 *     the whole row is draggable.
 *
 * Other ways to control behaviour:
 *
 * Add class="nodrop" to any rows for which you don't want to allow dropping, and class="nodrag" to any rows
 * that you don't want to be draggable.
 *
 * Inside the onDrop method you can also call $.tableDnD.serialize() this returns a string of the form
 * <tableID>[]=<rowID1>&<tableID>[]=<rowID2> so that you can send this back to the server. The table must have
 * an ID as must all the rows.
 *
 * Other methods:
 *
 * $("...").tableDnDUpdate()
 * Will update all the matching tables, that is it will reapply the mousedown method to the rows (or handle cells).
 * This is useful if you have updated the table rows using Ajax and you want to make the table draggable again.
 * The table maintains the original configuration (so you don't have to specify it again).
 *
 * $("...").tableDnDSerialize()
 * Will serialize and return the serialized string as above, but for each of the matching tables--so it can be
 * called from anywhere and isn't dependent on the currentTable being set up correctly before calling
 *
 * Known problems:
 * - Auto-scoll has some problems with IE7  (it scrolls even when it shouldn't), work-around: set scrollAmount to 0
 *
 * Version 0.2: 2008-02-20 First public version
 * Version 0.3: 2008-02-07 Added onDragStart option
 *                         Made the scroll amount configurable (default is 5 as before)
 * Version 0.4: 2008-03-15 Changed the noDrag/noDrop attributes to nodrag/nodrop classes
 *                         Added onAllowDrop to control dropping
 *                         Fixed a bug which meant that you couldn't set the scroll amount in both directions
 *                         Added serialize method
 * Version 0.5: 2008-05-16 Changed so that if you specify a dragHandle class it doesn't make the whole row
 *                         draggable
 *                         Improved the serialize method to use a default (and settable) regular expression.
 *                         Added tableDnDupate() and tableDnDSerialize() to be called when you are outside the table
 * Version 0.6: 2011-12-02 Added support for touch devices
 * Version 0.7  2012-04-09 Now works with jQuery 1.7 and supports touch, tidied up tabs and spaces
 */
!function ($, window, document, undefined) {
// Determine if this is a touch device
var hasTouch   = 'ontouchstart' in document.documentElement,
    startEvent = 'touchstart mousedown',
    moveEvent  = 'touchmove mousemove',
    endEvent   = 'touchend mouseup';

// If we're on a touch device, then wire up the events
// see http://stackoverflow.com/a/8456194/1316086
hasTouch
    && $.each("touchstart touchmove touchend".split(" "), function(i, name) {
        $.event.fixHooks[name] = $.event.mouseHooks;
    });


$(document).ready(function () {
    function parseStyle(css) {
        var objMap = {},
            parts = css.match(/([^;:]+)/g) || [];
        while (parts.length)
            objMap[parts.shift()] = parts.shift().trim();

        return objMap;
    }
    $('table').each(function () {
        if ($(this).data('table') == 'dnd') {

            $(this).tableDnD({
                onDragStyle: $(this).data('ondragstyle') && parseStyle($(this).data('ondragstyle')) || null,
                onDropStyle: $(this).data('ondropstyle') && parseStyle($(this).data('ondropstyle')) || null,
                onDragClass: $(this).data('ondragclass') == undefined && "tDnD_whileDrag" || $(this).data('ondragclass'),
                onDrop: $(this).data('ondrop') && new Function('table', 'row', $(this).data('ondrop')), // 'return eval("'+$(this).data('ondrop')+'");') || null,
                onDragStart: $(this).data('ondragstart') && new Function('table', 'row' ,$(this).data('ondragstart')), // 'return eval("'+$(this).data('ondragstart')+'");') || null,
                onDragStop: $(this).data('ondragstop') && new Function('table', 'row' ,$(this).data('ondragstop')),
                scrollAmount: $(this).data('scrollamount') || 5,
                sensitivity: $(this).data('sensitivity') || 10,
                hierarchyLevel: $(this).data('hierarchylevel') || 0,
                indentArtifact: $(this).data('indentartifact') || '<div class="indent">&nbsp;</div>',
                autoWidthAdjust: $(this).data('autowidthadjust') || true,
                autoCleanRelations: $(this).data('autocleanrelations') || true,
                jsonPretifySeparator: $(this).data('jsonpretifyseparator') || '\t',
                serializeRegexp: $(this).data('serializeregexp') && new RegExp($(this).data('serializeregexp')) || /[^\-]*$/,
                serializeParamName: $(this).data('serializeparamname') || false,
                dragHandle: $(this).data('draghandle') || null
            });
        }


    });
});

jQuery.tableDnD = {
    /** Keep hold of the current table being dragged */
    currentTable: null,
    /** Keep hold of the current drag object if any */
    dragObject: null,
    /** The current mouse offset */
    mouseOffset: null,
    /** Remember the old value of X and Y so that we don't do too much processing */
    oldX: 0,
    oldY: 0,

    /** Actually build the structure */
    build: function(options) {
        // Set up the defaults if any

        this.each(function() {
            // This is bound to each matching table, set up the defaults and override with user options
            this.tableDnDConfig = $.extend({
                onDragStyle: null,
                onDropStyle: null,
                // Add in the default class for whileDragging
                onDragClass: "tDnD_whileDrag",
                onDrop: null,
                onDragStart: null,
                onDragStop: null,
                scrollAmount: 5,
                /** Sensitivity setting will throttle the trigger rate for movement detection */
                sensitivity: 10,
                /** Hierarchy level to support parent child. 0 switches this functionality off */
                hierarchyLevel: 0,
                /** The html artifact to prepend the first cell with as indentation */
                indentArtifact: '<div class="indent">&nbsp;</div>',
                /** Automatically adjust width of first cell */
                autoWidthAdjust: true,
                /** Automatic clean-up to ensure relationship integrity */
                autoCleanRelations: true,
                /** Specify a number (4) as number of spaces or any indent string for JSON.stringify */
                jsonPretifySeparator: '\t',
                /** The regular expression to use to trim row IDs */
                serializeRegexp: /[^\-]*$/,
                /** If you want to specify another parameter name instead of the table ID */
                serializeParamName: false,
                /** If you give the name of a class here, then only Cells with this class will be draggable */
                dragHandle: null
            }, options || {});

            // Now make the rows draggable
            $.tableDnD.makeDraggable(this);
            // Prepare hierarchy support
            this.tableDnDConfig.hierarchyLevel
                && $.tableDnD.makeIndented(this);
        });

        // Don't break the chain
        return this;
    },
    makeIndented: function (table) {
        var config = table.tableDnDConfig,
            rows = table.rows,
            firstCell = $(rows).first().find('td:first')[0],
            indentLevel = 0,
            cellWidth = 0,
            longestCell,
            tableStyle;

        if ($(table).hasClass('indtd'))
            return null;

        tableStyle = $(table).addClass('indtd').attr('style');
        $(table).css({whiteSpace: "nowrap"});

        for (var w = 0; w < rows.length; w++) {
            if (cellWidth < $(rows[w]).find('td:first').text().length) {
                cellWidth = $(rows[w]).find('td:first').text().length;
                longestCell = w;
            }
        }
        $(firstCell).css({width: 'auto'});
        for (w = 0; w < config.hierarchyLevel; w++)
            $(rows[longestCell]).find('td:first').prepend(config.indentArtifact);
        firstCell && $(firstCell).css({width: firstCell.offsetWidth});
        tableStyle && $(table).css(tableStyle);

        for (w = 0; w < config.hierarchyLevel; w++)
            $(rows[longestCell]).find('td:first').children(':first').remove();

        config.hierarchyLevel
            && $(rows).each(function () {
                indentLevel = $(this).data('level') || 0;
                indentLevel <= config.hierarchyLevel
                    && $(this).data('level', indentLevel)
                    || $(this).data('level', 0);
                for (var i = 0; i < $(this).data('level'); i++)
                    $(this).find('td:first').prepend(config.indentArtifact);
            });

        return this;
    },
    /** This function makes all the rows on the table draggable apart from those marked as "NoDrag" */
    makeDraggable: function(table) {
        var config = table.tableDnDConfig;

        config.dragHandle
            // We only need to add the event to the specified cells
            && $(config.dragHandle, table).each(function() {
                // The cell is bound to "this"
                $(this).bind(startEvent, function(e) {
                    $.tableDnD.initialiseDrag($(this).parents('tr')[0], table, this, e, config);
                    return false;
                });
            })
            // For backwards compatibility, we add the event to the whole row
            // get all the rows as a wrapped set
            || $(table.rows).each(function() {
                // Iterate through each row, the row is bound to "this"
                if (! $(this).hasClass("nodrag")) {
                    $(this).bind(startEvent, function(e) {
                        if (e.target.tagName == "TD") {
                            $.tableDnD.initialiseDrag(this, table, this, e, config);
                            return false;
                        }
                    }).css("cursor", "move"); // Store the tableDnD object
                } else {
                    $(this).css("cursor", ""); // Remove the cursor if we don't have the nodrag class
                }
            });
    },
    currentOrder: function() {
        var rows = this.currentTable.rows;
        return $.map(rows, function (val) {
            return ($(val).data('level') + val.id).replace(/\s/g, '');
        }).join('');
    },
    initialiseDrag: function(dragObject, table, target, e, config) {
        this.dragObject    = dragObject;
        this.currentTable  = table;
        this.mouseOffset   = this.getMouseOffset(target, e);
        this.originalOrder = this.currentOrder();

        // Now we need to capture the mouse up and mouse move event
        // We can use bind so that we don't interfere with other event handlers
        $(document)
            .bind(moveEvent, this.mousemove)
            .bind(endEvent, this.mouseup);

        // Call the onDragStart method if there is one
        config.onDragStart
            && config.onDragStart(table, target);
    },
    updateTables: function() {
        this.each(function() {
            // this is now bound to each matching table
            if (this.tableDnDConfig)
                $.tableDnD.makeDraggable(this);
        });
    },
    /** Get the mouse coordinates from the event (allowing for browser differences) */
    mouseCoords: function(e) {
        if (e.originalEvent.changedTouches)
            return {
                x: e.originalEvent.changedTouches[0].clientX,
                y: e.originalEvent.changedTouches[0].clientY
            };

        if(e.pageX || e.pageY)
            return {
                x: e.pageX,
                y: e.pageY
            };

        return {
            x: e.clientX + document.body.scrollLeft - document.body.clientLeft,
            y: e.clientY + document.body.scrollTop  - document.body.clientTop
        };
    },
    /** Given a target element and a mouse eent, get the mouse offset from that element.
     To do this we need the element's position and the mouse position */
    getMouseOffset: function(target, e) {
        var mousePos,
            docPos;

        e = e || window.event;

        docPos    = this.getPosition(target);
        mousePos  = this.mouseCoords(e);

        return {
            x: mousePos.x - docPos.x,
            y: mousePos.y - docPos.y
        };
    },
    /** Get the position of an element by going up the DOM tree and adding up all the offsets */
    getPosition: function(element) {
        var left = 0,
            top  = 0;

        // Safari fix -- thanks to Luis Chato for this!
        // Safari 2 doesn't correctly grab the offsetTop of a table row
        // this is detailed here:
        // http://jacob.peargrove.com/blog/2006/technical/table-row-offsettop-bug-in-safari/
        // the solution is likewise noted there, grab the offset of a table cell in the row - the firstChild.
        // note that firefox will return a text node as a first child, so designing a more thorough
        // solution may need to take that into account, for now this seems to work in firefox, safari, ie
        if (element.offsetHeight == 0)
            element = element.firstChild; // a table cell

        while (element.offsetParent) {
            left   += element.offsetLeft;
            top    += element.offsetTop;
            element = element.offsetParent;
        }

        left += element.offsetLeft;
        top  += element.offsetTop;

        return {
            x: left,
            y: top
        };
    },
    autoScroll: function (mousePos) {
      var config       = this.currentTable.tableDnDConfig,
          yOffset      = window.pageYOffset,
          windowHeight = window.innerHeight
            ? window.innerHeight
            : document.documentElement.clientHeight
            ? document.documentElement.clientHeight
            : document.body.clientHeight;

        // Windows version
        // yOffset=document.body.scrollTop;
        if (document.all)
            if (typeof document.compatMode != 'undefined'
                && document.compatMode != 'BackCompat')
                yOffset = document.documentElement.scrollTop;
            else if (typeof document.body != 'undefined')
                yOffset = document.body.scrollTop;

        mousePos.y - yOffset < config.scrollAmount
            && window.scrollBy(0, - config.scrollAmount)
        || windowHeight - (mousePos.y - yOffset) < config.scrollAmount
            && window.scrollBy(0, config.scrollAmount);

    },
    moveVerticle: function (moving, currentRow) {

        if (0 != moving.vertical
            // If we're over a row then move the dragged row to there so that the user sees the
            // effect dynamically
            && currentRow
            && this.dragObject != currentRow
            && this.dragObject.parentNode == currentRow.parentNode)
            0 > moving.vertical
                && this.dragObject.parentNode.insertBefore(this.dragObject, currentRow.nextSibling)
            || 0 < moving.vertical
                && this.dragObject.parentNode.insertBefore(this.dragObject, currentRow);

    },
    moveHorizontal: function (moving, currentRow) {
        var config       = this.currentTable.tableDnDConfig,
            currentLevel;

        if (!config.hierarchyLevel
            || 0 == moving.horizontal
            // We only care if moving left or right on the current row
            || !currentRow
            || this.dragObject != currentRow)
                return null;

            currentLevel = $(currentRow).data('level');

            0 < moving.horizontal
                && currentLevel > 0
                && $(currentRow).find('td:first').children(':first').remove()
                && $(currentRow).data('level', --currentLevel);

            0 > moving.horizontal
                && currentLevel < config.hierarchyLevel
                && $(currentRow).prev().data('level') >= currentLevel
                && $(currentRow).children(':first').prepend(config.indentArtifact)
                && $(currentRow).data('level', ++currentLevel);

    },
    mousemove: function(e) {
        var dragObj      = $($.tableDnD.dragObject),
            config       = $.tableDnD.currentTable.tableDnDConfig,
            currentRow,
            mousePos,
            moving,
            x,
            y;

        e && e.preventDefault();

        if (!$.tableDnD.dragObject)
            return false;

        // prevent touch device screen scrolling
        e.type == 'touchmove'
            && event.preventDefault();

        // update the style to show we're dragging
        config.onDragClass
            && dragObj.addClass(config.onDragClass)
            || dragObj.css(config.onDragStyle);

        mousePos = $.tableDnD.mouseCoords(e);
        x = mousePos.x - $.tableDnD.mouseOffset.x;
        y = mousePos.y - $.tableDnD.mouseOffset.y;

        // auto scroll the window
        $.tableDnD.autoScroll(mousePos);

        currentRow = $.tableDnD.findDropTargetRow(dragObj, y);
        moving = $.tableDnD.findDragDirection(x, y);

        $.tableDnD.moveVerticle(moving, currentRow);
        $.tableDnD.moveHorizontal(moving, currentRow);

        return false;
    },
    findDragDirection: function (x,y) {
        var sensitivity = this.currentTable.tableDnDConfig.sensitivity,
            oldX        = this.oldX,
            oldY        = this.oldY,
            xMin        = oldX - sensitivity,
            xMax        = oldX + sensitivity,
            yMin        = oldY - sensitivity,
            yMax        = oldY + sensitivity,
            moving      = {
                horizontal: x >= xMin && x <= xMax ? 0 : x > oldX ? -1 : 1,
                vertical  : y >= yMin && y <= yMax ? 0 : y > oldY ? -1 : 1
            };

        // update the old value
        if (moving.horizontal != 0)
            this.oldX    = x;
        if (moving.vertical   != 0)
            this.oldY    = y;

        return moving;
    },
    /** We're only worried about the y position really, because we can only move rows up and down */
    findDropTargetRow: function(draggedRow, y) {
        var rowHeight = 0,
            rows      = this.currentTable.rows,
            config    = this.currentTable.tableDnDConfig,
            rowY      = 0,
            row       = null;

        for (var i = 0; i < rows.length; i++) {
            row       = rows[i];
            rowY      = this.getPosition(row).y;
            rowHeight = parseInt(row.offsetHeight) / 2;
            if (row.offsetHeight == 0) {
                rowY      = this.getPosition(row.firstChild).y;
                rowHeight = parseInt(row.firstChild.offsetHeight) / 2;
            }
            // Because we always have to insert before, we need to offset the height a bit
            if (y > (rowY - rowHeight) && y < (rowY + rowHeight))
                // that's the row we're over
                // If it's the same as the current row, ignore it
                if (draggedRow.is(row)
                    || (config.onAllowDrop
                    && !config.onAllowDrop(draggedRow, row))
                    // If a row has nodrop class, then don't allow dropping (inspired by John Tarr and Famic)
                    || $(row).hasClass("nodrop"))
                        return null;
                else
                    return row;
        }
        return null;
    },
    processMouseup: function() {
        if (!this.currentTable || !this.dragObject)
            return null;

        var config      = this.currentTable.tableDnDConfig,
            droppedRow  = this.dragObject,
            parentLevel = 0,
            myLevel     = 0;

        // Unbind the event handlers
        $(document)
            .unbind(moveEvent, this.mousemove)
            .unbind(endEvent,  this.mouseup);

        config.hierarchyLevel
            && config.autoCleanRelations
            && $(this.currentTable.rows).first().find('td:first').children().each(function () {
                myLevel = $(this).parents('tr:first').data('level');
                myLevel
                    && $(this).parents('tr:first').data('level', --myLevel)
                    && $(this).remove();
            })
            && config.hierarchyLevel > 1
            && $(this.currentTable.rows).each(function () {
                myLevel = $(this).data('level');
                if (myLevel > 1) {
                    parentLevel = $(this).prev().data('level');
                    while (myLevel > parentLevel + 1) {
                        $(this).find('td:first').children(':first').remove();
                        $(this).data('level', --myLevel);
                    }
                }
            });

        // If we have a dragObject, then we need to release it,
        // The row will already have been moved to the right place so we just reset stuff
        config.onDragClass
            && $(droppedRow).removeClass(config.onDragClass)
            || $(droppedRow).css(config.onDropStyle);

        this.dragObject = null;
        // Call the onDrop method if there is one

        config.onDrop
            //&& this.originalOrder != this.currentOrder()
            && $(droppedRow).hide().fadeIn('fast')
            && config.onDrop(this.currentTable, droppedRow);

        // Call the onDragStop method if there is one
        config.onDragStop
            && config.onDragStop(this.currentTable, droppedRow);

        this.currentTable = null; // let go of the table too
    },
    mouseup: function(e) {
        e && e.preventDefault();
        $.tableDnD.processMouseup();
        return false;
    },
    jsonize: function(pretify) {
        var table = this.currentTable;
        if (pretify)
            return JSON.stringify(
                this.tableData(table),
                null,
                table.tableDnDConfig.jsonPretifySeparator
            );
        return JSON.stringify(this.tableData(table));
    },
    serialize: function() {
        return $.param(this.tableData(this.currentTable));
    },
    serializeTable: function(table) {
        var result = "";
        var paramName = table.tableDnDConfig.serializeParamName || table.id;
        var rows = table.rows;
        for (var i=0; i<rows.length; i++) {
            if (result.length > 0) result += "&";
            var rowId = rows[i].id;
            if (rowId && table.tableDnDConfig && table.tableDnDConfig.serializeRegexp) {
                rowId = rowId.match(table.tableDnDConfig.serializeRegexp)[0];
                result += paramName + '[]=' + rowId;
            }
        }
        return result;
    },
    serializeTables: function() {
        var result = [];
        $('table').each(function() {
            this.id && result.push($.param(this.tableData(this)));
        });
        return result.join('&');
    },
    tableData: function (table) {
        var config = table.tableDnDConfig,
            previousIDs  = [],
            currentLevel = 0,
            indentLevel  = 0,
            rowID        = null,
            data         = {},
            getSerializeRegexp,
            paramName,
            currentID,
            rows;

        if (!table)
            table = this.currentTable;
        if (!table || !table.rows || !table.rows.length)
            return {error: { code: 500, message: "Not a valid table."}};
        if (!table.id && !config.serializeParamName)
            return {error: { code: 500, message: "No serializable unique id provided."}};
        
        rows      = config.autoCleanRelations
                        && table.rows
                        || $.makeArray(table.rows);
        paramName = config.serializeParamName || table.id;
        currentID = paramName;

        getSerializeRegexp = function (rowId) {
            if (rowId && config && config.serializeRegexp)
                return rowId.match(config.serializeRegexp)[0];
            return rowId;
        };

        data[currentID] = [];
        !config.autoCleanRelations
            && $(rows[0]).data('level')
            && rows.unshift({id: 'undefined'});



        for (var i=0; i < rows.length; i++) {
            if (config.hierarchyLevel) {
                indentLevel = $(rows[i]).data('level') || 0;
                if (indentLevel == 0) {
                    currentID   = paramName;
                    previousIDs = [];
                }
                else if (indentLevel > currentLevel) {
                    previousIDs.push([currentID, currentLevel]);
                    currentID = getSerializeRegexp(rows[i-1].id);
                }
                else if (indentLevel < currentLevel) {
                    for (var h = 0; h < previousIDs.length; h++) {
                        if (previousIDs[h][1] == indentLevel)
                            currentID         = previousIDs[h][0];
                        if (previousIDs[h][1] >= currentLevel)
                            previousIDs[h][1] = 0;
                    }
                }
                currentLevel = indentLevel;

                if (!$.isArray(data[currentID]))
                    data[currentID] = [];
                rowID = getSerializeRegexp(rows[i].id);
                rowID && data[currentID].push(rowID);
            }
            else {
                rowID = getSerializeRegexp(rows[i].id);
                rowID && data[currentID].push(rowID);
            }
        }
        return data;
    }
};

jQuery.fn.extend(
    {
        tableDnD             : $.tableDnD.build,
        tableDnDUpdate       : $.tableDnD.updateTables,
        tableDnDSerialize    : $.proxy($.tableDnD.serialize, $.tableDnD),
        tableDnDSerializeAll : $.tableDnD.serializeTables,
        tableDnDData         : $.proxy($.tableDnD.tableData, $.tableDnD)
    }
);

$.contentReady(function() {
    $('table', this).has(".dndsort").each(function(){
        //Если сортировка включена
        if (!$('.sortdot',this).hasClass('asc'))
        { //Если сортировка по другому полю
            $('.dndsort',this).addClass('notActive');
            $('.dndsort',this).closest('td').each(function(){
                $(this).removeClass('drag-handle').addClass('drag-handle-nonactive');
            });
        }


     });
    $('table', this).has(".dndsort").tableDnD({
        dragHandle: ".drag-handle",
        onDragClass: "in-drag",
        onDrop: function(table, row) {
            var from = $('[data-sortid]',row).data('sortid');
            var next = $(row).next();
            var to;
            var flag;
            if (next.length) {
                to = $('[data-sortid]', next).data('sortid');
                flag = 'up';
            } else {
                var prev = $(row).prev();
                to = $('[data-sortid]', prev).data('sortid');
                flag = 'down';
            }
            var url = $(table).data('sortRequest');
            if (url) {
                $.getJSON(url, {from:from, to: to, flag: flag}, function(response) {
                    //Успех
                });
            } else {
                alert('Не задан атрибут у таблицы - data-sort-request');
            }
        }
    });
});



}(jQuery, window, window.document);

/**
* Plugin, активирующий вкладку "комплектации" у товаров
* также плагин управляет назначением фото для комплектаций из вкладки Фото
*/
(function($){
    
$.fn.offer = function(method) {

    var defaults = {
        offerBlock       : '.offer-block',            //Класс для оборачивающего общего блока с компл. 
        offers           : '.offer-list',             //Класс для списка компл. 
        addOffer         : '.add-offer',              //Класс для добавления компл. 
        
        productPrice     : '[name^="excost"]',        //Цены товара
        mainOffer        : '.main-offer',               
        offerLine        : '.item',                   //Класс одной строки комплектаций
        offerEmptyLine   : '.empty-row',              //Класс пустой строки   
        newOffer         : '.new-item',               //Класс новой строки комплектации
        offerEditLine    : '.edit-form',              //Класс строки редактирования комплектации
        editOffer        : '.offer-edit, .offer-list td.clickable', //Ссылки на редактирование комплектации
        offerChangeWithMain : '.offer-change-with-main',   //Класс для функции "сделать комплектацию основной"
        
        offersImagesRow  : '.offer-images-line',       //Строка с фото у комплектаций
        offersImages     : '.offer-images-line a',
        productPhotos    : '.photo-list',            //Блок с фотографиями товара
        productOnePhoto  : '.photo-one',             //Одна фотография товара
        removeOffer      : '.delete',                //Класс для удаления компл. 
        barcode          : 'input[name="barcode"]',  //Поле Артикул
        sku              : 'input[name="sku"]',      //Поле Штрихкод
        
        offersPhotoDialog: {
            addOfferLink : '.add-offer-link',           //Кнопка добавить фото к комплектации
            addOffersLink: '.add-offers-link',          //Кнопка добавить несколько фото к комплектации
            applySelect  : '.apply-photo-offer-filter', //Кнопка Выбрать
            clearSelect  : '.clear-photo-offer-filter', //Кнопка Снять отметки
            offerZone    : '.photo-select',             //Элемент Select со списком связанных комплектаций
            save         : '.offer-photo-actions .save', //Кнопка Назначить
            reset        : '.offer-photo-actions .offer-photo-clear' //Ссылка снять отметки с комплектаций
        },
        
        //Параметры для многомерных комплектаций
        multiOfferWrapId : '#multioffer-wrap',       //Оборачивающий контейнер всего блока многомерных комплектаций
        useMultiOffer    : '#use-multioffer',        //Галка включающая многомерные компл. 
        crAutoOffers     : '#create-auto-offers',     //Галка "Создавать комплектации"
        multiOfferName   : 'multioffers[levels]',   //Атрибут name у уровня многомерной комплектации
        multiOfferPhoto  : 'multioffers[is_photo]', //Атрибут name галки с фото
        multiOfferWrap   : '.multioffer-wrap',       //Оборачивающий общий котейнер
        offersBody       : '.offers-body',           //Контейнер со всеми комплектациями
        addLevel         : '.add-level',             //Кнопка добавить уровень мн. компл. 
        deleteLevel      : '.delete-level',          //Кнопка удалить уровень комплектации
        createComplexs   : '.create-complexs',       //Кнопка создания многомерных комплектаций 
        hide             : 'cant-use',                  //Строка уровнем компл.
        rowMO            : '.line',                  //Строка уровнем компл.
        virtualForm      : '.virtual-form',         //Класс виртуальной формы
        virtualSubmit    : '.virtual-form .virtual-submit, .virtual-form button[type="submit"]', //Класс элементов отправки виртуальной формы
        tableInlineEdit  : '.table-inline-edit',    //Таблица, оборачивающая форму редактирования
        tableInlineEditCancel      : '.cancel',     //Кнопка "Отменить" в форме редактирования
        offerDeleteButton: '.offer-del',            //Кнопка "Удалить" одну комплектацию
        multiDeleteButton : '.group-toolbar .delete', //Кнопка "Удалить" выбранные комплектации
        multiEditButton   : '.group-toolbar .edit'    //Кнопка "Редактировать" выбранные комплектации
    },
    args = arguments;
    
    return this.each(function() {
        var $this = $(this),
            xhr,
            data = $this.data('offer');
        
        var methods = {
            /**
            * Инициализация, назначение действий
            * 
            * @param initoptions
            */
            init: function(initoptions) {
                if (data) return;
                data = {}; $this.data('offer', data);
                data.options = $.extend({}, defaults, initoptions);

                $this
                    .on('click', data.options.virtualSubmit, submitVirtualForm)
                    //Перехватываем нажатие Enter в форме фильтрации
                    .on('keydown', data.options.virtualForm + ' input', function(e) {
                        if (e.keyCode == 13) {
                            e.preventDefault();
                            e.stopPropagation();
                            submitVirtualForm.call(this, e);
                        }
                    })
                    .on('click', data.options.addOffer, methods.addOffer)
                    .on('click', data.options.editOffer, function() {
                        var offer_id = $(this).closest(data.options.offerLine).data('id');
                        methods.editOffer(offer_id);
                    })
                    .on('click', data.options.tableInlineEdit+' '+data.options.tableInlineEditCancel, function() {
                        var offer_id = $(this).closest(data.options.offerEditLine).data('id')
                        methods.editOffer(offer_id);
                    })
                    .on('click', data.options.virtualForm + ' a[data-href]', function(e) {
                        methods.refresh($(this).data('href'));
                        e.preventDefault();
                    })
                    .on('click', data.options.offerChangeWithMain, function(e) {
                        var offer_id = $(this).closest('[data-id]').data('id'),
                            offer_title = $(this).closest('[data-id]').find('.title').text(),
                            post_data = [{name: 'offer_id', value: offer_id}];
                        
                        if (offer_id == 0){return;}
                                 
                        post_data = post_data.concat(getMainOfferData()); 
                        post_data = post_data.concat(getProductPriceData()); 
                        post_data = post_data.concat(getProductBarcode());
                        post_data = post_data.concat(getProductSku());
                                
                        if (confirm(lang.t('Вы действительно хотите сделать коплектацию "%title" основной?', {title: offer_title}))) {
                            $.ajaxQuery({
                                url: $this.data('urls').offerChangeWithMain,
                                method: 'post',
                                data: post_data,
                                success: function(response) {   
                                    var form = $this.closest('.crud-form');
                                    $.each(response.excost, function(i, item){
                                        $('[name="excost['+i+'][cost_original_val]"]', form).val(item.cost_original_val);
                                        $('[name="excost['+i+'][cost_original_currency]"]', form).val(item.cost_original_currency);
                                    }); 
                                    $(data.options.barcode, form).val(response.barcode);
                                    $(data.options.sku, form).val(response.sku);
                                    if (response.success) methods.refresh(null, null, 'all-offers');
                                }
                            });
                        }
                    })
                    .on('click', data.options.offerDeleteButton, function(e) {
                        var offer_id = $(this).closest('[data-id]').data('id'),
                            offer_title = $(this).closest('[data-id]').find('.title').text();
                        
                        if (offer_id == 0) {
                            methods.editOffer(offer_id);
                            return;
                        }
                        
                        if (confirm(lang.t('Вы действительно хотите удалить комплектацию "%title"?', {title: offer_title}))) {
                            $.ajaxQuery({
                                url: $this.data('urls').offerDelete,
                                data: {
                                    offers:[offer_id]
                                },
                                success: function(response) {
                                    if (response.success) methods.refresh();
                                }
                            });
                        }
                    })
                    .on('click', data.options.multiDeleteButton, function(e) {
                        var offers_id = getSelectedOffers();
                        var count = $(data.options.offers + ' .select-all:checked', $this).length ? $('.total_value', $this).text() : offers_id.length;
                        
                        if (offers_id.length && confirm(lang.t('Вы действительно хотите удалить выбранные комплектации(%count)', {count:count}))) {
                            $.ajaxQuery({
                                url: $this.data('urls').offerDelete,
                                data:offers_id,
                                success: function(response) {
                                    if (response.success) methods.refresh();
                                }
                            });
                        }
                    })
                    .on('click', data.options.multiEditButton, function(e) {
                        var selected = $(data.options.offers + ' input[type="checkbox"]:checked', $this).length;
                        if (!selected) return;
                        
                        var offer_id = $(data.options.offers + ' input[type="checkbox"]:checked:last', $this).val();
                        methods.editOffer(offer_id, selected>1);
                    })
                    .on('change', data.options.offers + ' input[type="checkbox"]', function() {
                        //Если есть отмеченные элементы, то посылаем событие - Запретить действия над товаром, иначе - разрешить
                        if ($(data.options.offers + ' input[type="checkbox"]:checked', $this).length) {
                            $this.trigger('disableBottomToolbar', 'offer-checked');
                        } else {
                            $this.trigger('enableBottomToolbar', 'offer-checked');
                        }
                    })
                    .on('click', data.options.offersImages, function() {
                        var value = $(this).data('id');
                        
                        if ($(this).is('.act')) {
                            $(this).removeClass('act');
                            $(this).parent().find('input[value="'+value+'"]').remove();
                        } else {
                            var name = $(this).data('name');
                            $(this).addClass('act');
                            $(this).parent().append('<input type="hidden" name="'+name+'" value="'+value+'">');
                        }
                    });
                    
                    $this.closest('.dialog-window').on('dialogBeforeDestroy', function() {
                        $this
                            .trigger('enableBottomToolbar', 'offer-checked')
                            .trigger('enableBottomToolbar', 'offer-edit');
                    });
                    
                    $this.closest('.crud-form')
                            .on('changePhoto', onChangePhoto)
                            .on('click', data.options.offersPhotoDialog.addOfferLink, function() {
                                methods.openPhotoLinkDialog([$(this).data('id')]);
                            })
                            .on('click', data.options.offersPhotoDialog.addOffersLink, function() {
                                var ids = [];
                                $(data.options.productPhotos + ' .chk input:checked').each(function() {
                                    ids.push($(this).val());
                                });
                                methods.openPhotoLinkDialog(ids);
                            });
                    
                    
                                         
                //Многомерные комплектации                   
                $(data.options.multiOfferWrap,$this)
                                    .on('click',data.options.addLevel, methods.addMultiOfferLevel)    //Добавить уровень комплектаций
                                    .on('click',data.options.deleteLevel, methods.delMultiOfferLevel) //Добавить уровень комплектаций
                                    .on('click',data.options.createComplexs, methods.createComplexs)  //Создание многомерных комплектаций
                                    .on('click',data.options.crAutoOffers, toggleCreateMOffers)       //Вкл./выкл. галки "Создавать комплектации"
                                    .on('change','select[name^="'+data.options.multiOfferName+'"]',onMultiOfferLevelChange); //Событие на изменение уровня многомерной комплектации
                                     
                $('body').on('on-tab-open',tabChange ); //Создание многомерных комплектаций
                $(data.options.useMultiOffer,$this).on('click', showMOffers);
                
                if (!$(data.options.useMultiOffer,$this).prop('checked')){
                   $(data.options.multiOfferWrap,$this).hide(); 
                }
                
                checkMODelete(); //Проверяет можно ли удалять многомерные комплектации
                
                //Проверим существуют ли характеристики списковые, и если нет, то скороем опцию многомерных комплектаций
                var props = getActualPropListsIds(); 
                if (props.length==0){
                   $(data.options.multiOfferWrapId,$this).addClass(data.options.hide); 
                   $(data.options.multiOfferWrap,$this).hide(); 
                }
                
            },
            
            postForm: function(form, post_params) {
                $.ajaxQuery({
                    url: form.data('action'),
                    data: post_params,
                    method: 'post',
                    success: function(response) {
                        //Если это пост виртуальной формы, то отображаем ошибки формы, если они есть
                        if (response.success) {
                            methods.refresh();
                        } else {
                            $('.crud-form-error', form).fillError(response.formdata.errors, form);
                        }
                    }
                });                
            },
            
            /**
            * Открывает диалог связи комплектации и фотографии
            */
            openPhotoLinkDialog: function(photos_id) {
                
                var applySelection = function(enable, dialog) {
                     var filter = {};
                        $('.params-row select', dialog).each(function() {
                            filter[$(this).data('name')] = $(this).val();
                        });
                        
                        $('.offer-photo-select option', dialog).each(function() {
                            var match = true;
                            var option_params = $(this).data('params');
                            if (option_params !== null) {
                                $.each(filter, function (param_key, param_val) {
                                    if (param_val != '') {
                                        if (typeof (option_params[param_key]) == undefined || option_params[param_key] != param_val) {
                                            match = false;
                                        }
                                    }
                                });
                                if (match)
                                    $(this).prop('selected', enable);
                            }
                        });
                },
                                
                linkPhotoWithOffers = function(dialog) {
                    //Закроем открытую вкладку комплектаций
                    methods.closeOfferEdit();
                    
                    var post_data = $('#offers-photo-form').serializeArray().concat(getMainOfferData());
                    
                    $.ajaxQuery({
                        url: $this.data('urls').offerLinkPhotoSave,
                        method:'post',
                        data: post_data,
                        success: function(response) {
                            //Отметим фотографии Основной комплектации
                            
                            if (response.success) {
                                $(data.options.mainOffer + ' ' + data.options.offersImagesRow + ' input', $this).remove();
                                $(data.options.mainOffer + ' ' + data.options.offersImages, $this)
                                    .removeClass('act')
                                    .each(function() {
                                    
                                    if (response['main_offer_photos'].indexOf($(this).data('id').toString()) != -1) {
                                        $(this).click();
                                    }
                                });
                            }
                        }
                    });
                    
                    dialog.dialog('close');
                };                
                
                var post_data = getMainOfferData();
                
                $(photos_id).each(function(key, photo_id) {
                    post_data.push({
                        name: "photos_id[]",
                        value: photo_id
                    });
                });
                
                $.rs.openDialog({
                    url: $this.data('urls').offerLinkPhoto,
                    dialogOptions: {
                        width:510,
                        height:700,
                        title: lang.t('Назначить фотографии комплектациям'),
                        dialogClass: 'photolink-dialog',
                    },
                    ajaxOptions: {
                        method: 'POST',
                        data: post_data
                    },
                    afterOpen: function(dialog) {
                        dialog
                            .on('click', data.options.offersPhotoDialog.reset, function() {
                                $(data.options.offersPhotoDialog.offerZone+' option', dialog).prop('selected', false);
                            })
                            .on('click', data.options.offersPhotoDialog.applySelect, function() {
                                applySelection(true, dialog);
                            })
                            .on('click', data.options.offersPhotoDialog.clearSelect, function() {
                                applySelection(false, dialog);
                            })
                            .on('click', data.options.offersPhotoDialog.save, function() {
                                linkPhotoWithOffers(dialog);
                            });
                        $this.trigger('disableBottomToolbar', 'offer-link');
                    },
                    close: function() {
                        $this.trigger('enableBottomToolbar', 'offer-link');
                    }
                });
                
            },
            
            /**
            * Обновляет список комплектаций
            */
            refresh: function(url, post_params, render_type) {
                
                if (!url) url = $(data.options.offers).data('refreshUrl');
                if (!post_params) post_params = [];
                if (!render_type) render_type = 'ext-offers';
                
                post_params.push({
                    name: 'offer_render_type',
                    value:render_type
                });
                
                $.ajaxQuery({
                    url: url,
                    data: post_params,
                    method: 'post',
                    //dataType: 'html',
                    success: function(response) {
                        if (response.success != false) {
                            $('#'+render_type, $this).html(response.html).trigger('new-content');
                            $this.trigger('enableBottomToolbar', 'offer-edit');
                            $this.trigger('enableBottomToolbar', 'offer-checked');
                        }
                    }
                });
                
            },            
            
            /**
            * Добавление пустой копмлектации товару
            * 
            * @var array props - массив характеристик, для добавления
            */
            addOffer: function(props) {
                //Добавляем новую строку в таблицу комплектаций
                var tr = $('<tr class="item new-item" data-id="0">\
                            <td class="chk"></td>\
                            <td class="drag drag-handle"></td>\
                            <td class="title">'+lang.t('Новая комплектация')+'</td>\
                            <td class="barcode"></td>\
                            <td class="amount"></td>\
                            <td class="price"></td>\
                            <td class="actions va-m-c">\
                                <span class="loader"></span>\
                                <a class="offer-del zmdi zmdi-close f-18 m-r-5"></a>\
                            </td>\
                        </tr>');
                
                $(data.options.offers).prepend(tr);
                $(data.options.offers + ' ' + data.options.offerEmptyLine).hide();
                methods.editOffer(0);
            },
            
            /**
            * Закрывает форму редактирования комплектации
            */
            closeOfferEdit: function() {
                var open_offer_id = $(data.options.offerLine + '.now-edit, ' + data.options.offerLine + '.now-multiedit', $this).first().data('id');
                if (open_offer_id) {
                    methods.editOffer(open_offer_id);
                }
            },
            
            /**
            * Редактирование комплектации 
            */
            editOffer: function(offer_id, is_multiedit) {
                //Закрываем форму, если она была открыта раннее.
                var edit_line = $(data.options.offerLine + '[data-id="'+offer_id+'"]', $this);
                var is_opened = edit_line.is('.now-edit') || edit_line.is('.now-multiedit');
                
                //Удаляем строку несозданной комплектации, если таковая была
                if (offer_id>0 || is_opened) $(data.options.offers + ' ' + data.options.newOffer, $this).remove();
                
                $(data.options.offerLine, $this).removeClass('now-multiedit now-edit load');
                $(data.options.offerEditLine, $this).remove();
                $(data.options.offers + ' > tbody > tr', $this).removeClass('nodrag nodrop');

                if (is_opened || !is_multiedit) {
                    $(data.options.offers + ' input[type="checkbox"]', $this).prop({'disabled': false, 'checked': false}).change();
                } 
                
                if (xhr) xhr.abort();
                
                if (is_opened) { //Закрытие режима редактирования
                    $this.trigger('enableBottomToolbar', 'offer-edit');
                    $(data.options.offers + ' ' + data.options.offerEmptyLine).show();
                    return;
                }
                
                $(data.options.offers + ' input[type="checkbox"]', $this).prop('disabled', true);
                $this.trigger('disableBottomToolbar', 'offer-edit');
                
                edit_line.addClass(is_multiedit ? 'now-multiedit' : 'now-edit load');
                
                var product_barcode = $this.closest('.crud-form').find(data.options.barcode).val();
                
                if (is_multiedit) {
                    var url = $this.data('urls').offerMultiEdit;
                    var post_data = getSelectedOffers();
                } else {
                    var url = $this.data('urls').offerEdit;
                    var post_data = {
                        offer_id: offer_id,
                        product_barcode: product_barcode
                    };
                }
                             
                xhr = $.ajaxQuery({
                    url: url,
                    data: post_data,
                    success: function(response) {
                        edit_line.removeClass('load');
                        var edit_wrap = $('<tr class="edit-form no-over">'+
                                            '<td colspan="7">'+
                                                '<div class="bordered"></div>'+
                                            '</td>'+
                                         '</tr>');
                             
                        edit_wrap.data('id', offer_id).find('.bordered').html(response.html);
                        edit_wrap.insertAfter(edit_line).trigger('new-content');
                        
                        //Отключаем сортировку
                        $(data.options.offers + ' > tbody > tr').addClass('nodrag nodrop');
                        
                        //Включаем обработчик блока цен
                        edit_wrap.on('click.offer', '.oneprice', function() {
                            $(this).siblings('.vtable').toggle(!this.checked);
                            $(this).siblings('.oneprice-data').toggle(this.checked);
                        })
                    }
                });
                
            
            },
            
            //Многомерные комплектации
            /**
            * Показывает только актуальные характристики
            * 
            */
            showOnlyActualProps: function () {
               //Обновляем список свойств
               //Загружаем значения в список
               $(data.options.multiOfferWrap+" "+data.options.rowMO+" select", $this).each(function() {
                    fillPropToSelect(this);
               });
                
               //Сначала все характеристики скрыты 
               $(data.options.multiOfferWrap+" "+data.options.rowMO+" option", $this).addClass('hide').prop('disabled',true);
               var props = getActualPropListsIds(); 
               
               //Теперь покажем характеристики у которых тип - список
               for(var i=0;i<props.length;i++){
                  $(data.options.multiOfferWrap+" "+data.options.rowMO+" option[value='"+props[i]+"']", $this).removeClass('hide').prop('disabled',false);
               }
               
               $(data.options.multiOfferWrap+" "+data.options.rowMO+" select", $this).each(function() {
                   if ($('option:enabled:selected', $(this)).length == 0) { 
                       var count = $(data.options.multiOfferWrap + " " + data.options.rowMO, $this).length;
                       if (count == 1) {
                            $('option:enabled:first', $(this)).prop('selected', true);
                        } else {
                           //Если по какой-то причине выбрана неактивная характеристика, удаляем строку
                           //$(this).closest(data.options.rowMO).remove();
                        }
                   } 

                   //Скроем Группы в списке у которых все элементы скрыты
                   $("optgroup",$(this)).each(function(){
                       $(this).toggle( $('option:enabled', $(this)).length > 0 );
                   });
               });
               
            },
            
            /**
            * Создание многомерных комплектаций из добавленных
            * 
            */
            createComplexs: function() 
            {
               //Покажем актуальные характеристики 
               methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны 
               
               //Подготим данные для отправки на сервер
               var post_data = [{
                   name: 'product_barcode',
                   value: $this.closest('.crud-form').find(data.options.barcode).val()
               }];
               
               $(data.options.multiOfferWrap+' '+data.options.rowMO+' option:selected', $this).each(function() {
                   var product_form = $this.closest('.crud-form');
                   var multioffer_title = $(this).closest(data.options.rowMO).find('.key input').val();
                   
                   post_data.push({
                       name: 'prop['+$(this).val()+'][title]',
                       value: multioffer_title
                   });

                   let selected_value_selector = '.property-container .property-item[data-property-id="' + $(this).val() + '"] input.h-val:checked,' +
                                                '.property-container .property-item[data-property-id="' + $(this).val() + '"] .property-type-big-list_selected-item-checkbox';
                   
                   $(selected_value_selector, product_form).each(function() {
                       post_data.push({
                           name: $(this).attr('name'),
                           value:$(this).val()
                       });
                   });
               });
               
               //Обновляем сведения
               methods.refresh($this.data('urls').offerMakeFromMultioffer, post_data, 'all-offers');
               
               //Спрячем кнопку
               $(data.options.crAutoOffers,$this).prop('checked',false);
               toggleCreateMOffers();
               
               return false;
            },
            /**
            * Добавляет уровень многомерной комплектации
            * 
            */
            addMultiOfferLevel: function (){
                //Клонируем строку уровня 
               var offerLevel = $(tmpl('multioffer-line', {}));

               var row = $(offerLevel);
               $(data.options.multiOfferWrap+" "+data.options.offersBody, $this).append(row);
               fillPropToSelect(row.find('select'));
               
               methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны               
                //Получаем следующую характеристику                                  
               var rows = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this);
               var selected = $("option:enabled:selected", rows),
                   next_prop = selected.val(), 
                   next_title = selected.text();
                                  
               $("option:enabled", rows).each(function() {
                    if ( !$(this).is(':selected') ) {
                        next_prop = $(this).val();
                        next_title = $(this).text();
                        return false;
                    }
               });               
               
               row.find('select').val(next_prop);
               row.find('input').val(next_title);
               
               checkMODelete();               //Проверка на показ кнопки удалить  
               checkMOAdd();                  //Проверка на показ кнопки добавить
               remakeMONames();               //Переформирование имен в нужном порядке 
               return false;
            },
            
            /**
            * Удаляет уровень многомерной комплектации
            * 
            */
            delMultiOfferLevel: function (){
               var wrap = $(this).closest(data.options.rowMO);
               $(wrap).remove();
               $.messenger('hideAll');
               remakeMONames();
               checkMODelete();
               methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны
               checkMOAdd();
               return false;
            }
        }
        
        //private
        /**
        * Постит данные из виртуальной формы
        */
        var submitVirtualForm = function(e) 
        {
            var form = $(this).closest(data.options.virtualForm);
            var real_form = $('<form />');
            form.find('input, select, textarea').each(function() {
                var element = $(this).clone();
                if (element.is('select,textarea')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(real_form);
            });
            
            var params = real_form.serializeArray();
            
            if (form.data('hasValidation')) {
                methods.postForm(form, params);
            } else {
                methods.refresh(form.data('action'), params);
            }
            
            e.preventDefault();
        },        
        
        /**
        * Возвращает данные по ценам продукта
        * 
        */
        getProductPriceData = function() {
            var virtual_form = $('<form />');
            
            $(data.options.productPrice).each(function() {
                var element = $(this).clone();
                if (element.is('select')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(virtual_form);
            });
            
            return virtual_form.serializeArray();
        },
        
        /**
        * Возвращает данные по артикулу продукта
        */
        getProductBarcode = function() {
            var product_barcode = $this.closest('.crud-form').find(data.options.barcode).val();
            return [{name:'barcode', value:product_barcode}];
        },
        
        /**
        * Возвращает данные по штрихкоду продукта
        */
        getProductSku = function() {
            var product_sku = $this.closest('.crud-form').find(data.options.sku).val();
            return [{name:'sku', value:product_sku}];
        },
        
        /**
        * Возвращает данные основной комплектации
        * 
        */
        getMainOfferData = function() {
            var virtual_form = $('<form />');
        
            $(data.options.mainOffer, $this).find('input, select').each(function() {
                var element = $(this).clone();
                if (element.is('select')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(virtual_form);
            });
            
            return virtual_form.serializeArray();
        },  
        
        /**
        * Вызывается при добавлении, удалении фото, сортировке фото товара
        */
        onChangePhoto = function(e, photo_block) 
        {
            $(data.options.offersImagesRow, $this).each(function() {
                var main_offer_selected_photos = [];
                var main_offer_photo_line = $(this);
                $('.act', main_offer_photo_line).each(function() {
                    main_offer_selected_photos.push($(this).data('id'));
                });
                main_offer_photo_line.empty();
                
                //Обновим фотографии у основной комплектации
                $(photo_block).find(data.options.productPhotos + ' ' + data.options.productOnePhoto).each(function() {
                    var mini_photo = $('<a>')
                                        .attr('data-name', 'offers[main][photos_arr][]')
                                        .attr('data-id', $(this).data('id'))
                                        .append( $('<img />').attr('src', $(this).find('.image').data('smallImage') ));
                                        
                    main_offer_photo_line.append(mini_photo);

                    if (main_offer_selected_photos.indexOf($(this).data('id')) != -1) {
                        mini_photo.click();
                    }                
                });            
            });
        },
        
        /**
        * Возвращает отмеченные комплектации
        */
        getSelectedOffers = function() 
        {
            var items = [];
            $(data.options.offers + ' input[name][type="checkbox"]:checked').each(function() {
                items.push({
                     name: $(this).attr('name'),
                     value: $(this).val()
                });
            });
            return items;
        },
        
        /**
        * Наполняет SELECT значениями свойств
        */
        fillPropToSelect = function(select) 
        {
            select = $(select);            
            if ( $('.p-proplist > *').length > 1 ) {
                var before_val = select.val();
                select.empty().html( $('.p-proplist > *').clone() ).find('[value="new"]').remove();
                select.val(before_val);
            }
        },
         
        //Многомерные комплектации
        /**
        * Переключает состояния кнопки создать
        */
        toggleCreateMOffers = function (){
            $(data.options.createComplexs,$this).toggle();
        },
        /**
        * Событие при изменении выбора в селекторе уровня многомерных компл.
        * 
        */
        onMultiOfferLevelChange = function (){
            $(this).data('prop-id',$(this).val()); //Выставляем доп информацию
        }, 
        /**
        * Переключение между вкладками
        */
        tabChange = function(){  
            var props     = getActualPropListsIds(); 
            var props_cnt = props.length;
            
            if (props_cnt==0){
                $(data.options.multiOfferWrap,$this).hide();
                $(data.options.useMultiOffer,$this).prop('checked',false);
            }else{
                //Проверим существование, соотвествует ли количество 
                //строк с уровнями, включённым списковым характеристикам 
                var rows = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this);
                var rows_cnt = rows.length;

                if (rows_cnt > props_cnt){ //Если больше чем надо, то удалим строки с уровнями
                    rows.each(function() {
                        var property_id = $(this).find('option:selected').val();
                        if (props.indexOf(parseInt(property_id)) == -1) $(this).remove();
                    });
                }
            }
            methods.showOnlyActualProps(); 
            checkMOAdd();  
            checkMODelete();
            
            if (!$(data.options.useMultiOffer,$this).prop('checked')){
               $(data.options.multiOfferWrap,$this).hide(); 
            }

            if (props.length==0){
                $(data.options.multiOfferWrapId,$this).addClass(data.options.hide); 
                $(data.options.useMultiOffer,$this).prop('checked',false); 
            }else{
                $(data.options.multiOfferWrapId,$this).removeClass(data.options.hide); 
            }                                 
        },
        /**
        * Получает в виде массива актуальные id списковых характеристик, у которых есть отмечанные галочки
        * беря их из вкладки характеристики
        */
        getActualPropListsIds = function(){
           var props = new Array();
           //Соберём сведения по характеристикам с галочками
           $(".property-container .property-item").each(function(){
               if ($('.item-val input[type="checkbox"]:checked, .property-type-big-list_selected-item',$(this)).length>0){
                  var i = props.length; 
                  
                  props[i] = $(this).data('property-id'); 
               } 
           }); 
           
           return props;
        },
        /**
        * Переформировывает атрибут name у уровней комплектаций
        * Проставляет name по по порядку
        */
        remakeMONames = function(){
           var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO,$this).length; 

           //Галка "с фото" у комплектаций
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" input[type='radio']",$this).each(function(i){
               $(this).val(i+1);
           });
           //Название комплектации
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" input[type='text']",$this).each(function(i){
               $(this).attr('name',data.options.multiOfferName + '['+i+'][title]');
           });
           //Выпадающий список у многомерных комплектаций
           $(data.options.multiOfferWrap+" "+data.options.rowMO+" select", $this).each(function(i){
               $(this).attr('name',data.options.multiOfferName + '['+i+'][prop]');
           });
           
           $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).removeClass('error');
        },
        /**
        * Проверяет можно ли добавлять уровень комплектации
        */
        checkMOAdd = function(){
            var props     = getActualPropListsIds(); 
            var cnt       = props.length; //Количество характеристик
            var rows_cnt  = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).length; //Количество созданных уровней
            if (cnt>rows_cnt){
               $(data.options.multiOfferWrap+" "+data.options.addLevel).show(); 
            }else{
               $(data.options.multiOfferWrap+" "+data.options.addLevel).hide();  
            }    
        },
        /**
        * Проверяет право на удаление многомерных комплектаций
        */
        checkMODelete = function(){
            var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).length; 
            if (cnt>1){
                $(data.options.multiOfferWrap+" "+data.options.deleteLevel,$this).show();
            }else{
                $(data.options.multiOfferWrap+" "+data.options.deleteLevel,$this).hide();
            }
            return false;
        },
        /**
        * Показывает окно многомерных комплектаций
        * Клик на галочке мн. компл.
        */
        showMOffers = function(){
            openMOffers();
            var cnt       = $(data.options.multiOfferWrap+" "+data.options.rowMO, $this).length; 
            if (cnt==0){
                methods.addMultiOfferLevel(); //Добавим нулевой уровень если требуется
            }
            if ($(this).prop('checked')){
               $(data.options.multiOfferWrap,$this).show(); 
            }else{
               $(data.options.multiOfferWrap,$this).hide();  
            }
        },
        /**
        * Открывает окно многомерных комплектаций
        */
        openMOffers = function(){
            $(data.options.multiOfferWrap,$this).show();
            $(this).hide();
            methods.showOnlyActualProps(); //Скроем характеристики которые галками не отмечаны
            return false;
        }
          
        if ( methods[method] ) {
            methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, args );
        }
    });
}
})(jQuery);
(function(a){"use strict";var b=function(a,c){var d=/[^\w\-\.:]/.test(a)?new Function(b.arg+",tmpl","var _e=tmpl.encode"+b.helper+",_s='"+a.replace(b.regexp,b.func)+"';return _s;"):b.cache[a]=b.cache[a]||b(b.load(a));return c?d(c,b):function(a){return d(a,b)}};b.cache={},b.load=function(a){return document.getElementById(a).innerHTML},b.regexp=/([\s'\\])(?![^%]*%\})|(?:\{%(=|#)([\s\S]+?)%\})|(\{%)|(%\})/g,b.func=function(a,b,c,d,e,f){if(b)return{"\n":"\\n","\r":"\\r","\t":"\\t"," ":" "}[a]||"\\"+a;if(c)return c==="="?"'+_e("+d+")+'":"'+("+d+"||'')+'";if(e)return"';";if(f)return"_s+='"},b.encReg=/[<>&"'\x00]/g,b.encMap={"<":"&lt;",">":"&gt;","&":"&amp;",'"':"&quot;","'":"&#39;"},b.encode=function(a){return String(a||"").replace(b.encReg,function(a){return b.encMap[a]||""})},b.arg="o",b.helper=",print=function(s,e){_s+=e&&(s||'')||_e(s);},include=function(s,d){_s+=tmpl(s,d);}",typeof define=="function"&&define.amd?define(function(){return b}):a.tmpl=b})(this);
/**
* Плагин инициализирует в административной панели работу редакторов ключ => значение
* зависит от jquery.tableDnd
*
* @author ReadyScript lab.
*/
(function( $ ){

    $.fn.keyvalEditor = function( method ) {
        var defaults = {
            table: '.keyvalTable',
            addButton: '.add-pair',
            removeButton: '.remove',
            line: 'tr',
            tbody: 'tbody'
        },
        args = arguments;
        
        return this.each(function() {
            var $this = $(this), 
                data = $this.data('keyvalEditor');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('keyvalEditor', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    data.varName = $this.data('var');
                    $this
                        .on('click', data.opt.addButton, function() { methods.add(); })
                        .on('click', data.opt.removeButton, remove);
                    
                    bindEvents();
                },
                /**
                * Добавляет строку с ключём и значением
                * 
                * @param string key - ключ
                * @param string val - значение
                */
                add: function(key, val) {
                    var line = 
                        $('<tr>'+
                            '<td class="kv-sort">'+
                                '<div class="ksort"><i class="zmdi zmdi-unfold-more"><!----></i></div>'+
                            '</td>'+
                            '<td class="kv-key"><input type="text" name="'+data.varName+'[key][]"></td>'+
                            '<td class="kv-val"><input type="text" name="'+data.varName+'[val][]"></td>'+
                            '<td class="kv-del"><a class="remove zmdi zmdi-delete"></a></td>'+
                        '</tr>');
                    
                    var $new_element = $(line);
                    $(data.opt.tbody, $this).append($new_element);
                    
                    if (key || val) {
                        $new_element.find('.kv-key input').val(key);
                        $new_element.find('.kv-val input').val(val);
                    }
                    
                    checkEmpty();
                    bindEvents();
                },
                
                remove: function(item) {
                    $(item).closest(data.opt.line).remove();
                    checkEmpty();
                    return false;
                }
            }
            
            //private
            var remove = function() {
                methods.remove(this);
            },
            
            bindEvents = function() {
                $(data.opt.table).tableDnD({
                    dragHandle: ".kv-sort",
                    onDragClass: "in-drag",
                });                                        
            },
            
            checkEmpty = function() {
                $(data.opt.table, $this).toggleClass('hidden', $(data.opt.table+' tbody', $this).children().length==0 );
            };
            
            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    }

    $.contentReady(function() {
        $('.keyval-container', this).keyvalEditor();
    });

})( jQuery );
/**
* JQuery plug-in для активации древовидных списков
*/
(function($){
    $.fn.rstree = function(options)
    {
        var make = function()
        {
            options = $.extend({
                useajax: true,
                inputContainer: '.input-container',
                divSelected:'.selected-container',
                selectedContainer: '.selected-container .group-block',
                pathContainer: '.current-path .breadcrumb',
                hideGroupCheckbox: 0,
                hideProductCheckbox: 0,
                urls: {
                    getChild: '',
                }
            }, options);

            var plugin_context = this;
            var context = options.context;

            var divSelected = $(options.divSelected, context);
            var inputContainer = $(options.inputContainer, context);
            var fieldName = inputContainer.data('fieldName');
            var selectedContainer = $(options.selectedContainer, context);
            var loadingChilds = false;

            var onSelectItem = function()
            {
                $('li a.act', plugin_context).removeClass('act');
                $(this).addClass('act');
                options.selectItem( $(this).parents('li:first'), plugin_context);
                changeCurrentPath();
            };

            var toggle = function(e)
            {
                var offset = $(e.target).offset();
                if (e.clientY > offset.top + 19) return false;

                if (e.target.tagName == 'LI')
                {
                    var jquery_el = $(e.target);
                    var el_class = jquery_el.attr('class');

                    if (el_class.indexOf('plus') > -1) {
                        loadChild(e.target, function()
                        {
                            //Отображаем подпункты
                            var newClass = jquery_el.attr('class').replace('plus','minus');
                            jquery_el.attr('class', newClass);
                            $('ul:first', e.target).show();
                        });

                    } else if (el_class.indexOf('minus') > -1) {
                        //Скрываем подпункты
                        var newClass = jquery_el.attr('class').replace('minus','plus');
                        jquery_el.attr('class', newClass);
                        $('ul:first', e.target).hide();
                    }

                    return false;
                }
            };

            var loadChild = function(li, callback)
            {
                if (loadingChilds) return false;
                if ($('>ul', li).length>0) {
                    callback();
                } else if (options.useajax) {
                    var $img = $('img', li);
                    var $img_before = $img.attr('src');
                    $img.attr('src', '/resource/img/adminstyle/small-loader.gif');
                    loadingChilds = true;

                    $.ajaxQuery({
                        loadingProgress: false,
                        url: options.urls.getChild,
                        data: {
                            id: $(li).attr('qid'),
                            hideGroupCheckbox:options.hideGroupCheckbox,
                            hideProductCheckbox: options.hideProductCheckbox,
                        },
                        success: function(response) {
                            $img.attr('src', $img_before);
                            $(li).append(response.html);
                            if ($('>input:checkbox', li).get(0).checked) {
                                $('ul input:checkbox', li)
                                    .attr('checked', 'checked')
                                    .attr('disabled','disabled');
                            }

                            bindEvents();
                            callback();
                            loadingChilds = false;
                        }

                    })
                }
            };

            var checkboxChange = function(e, trigged)
            {
                var val = this.value;
                var parentLi = $(this).parent('li');

                parents_str = ',';
                parentLi.parents('li[qid]').each(function() {
                    parents_str = parents_str + $(this).attr('qid')+',';
                });

                if (this.checked) {
                    $('ul input:checkbox', parentLi)
                        .attr('checked', 'checked')
                        .attr('disabled','disabled');

                    if (!trigged) { //Если это событие не вызвали мы сами же при открытии диалога
                        if ($(".dirs", inputContainer).length){
                            $(".dirs", inputContainer).append('<input type="hidden" name="'+fieldName+'[group][]" value="'+val+'" data-catids="'+parents_str+'">');
                        }else{
                            $(inputContainer).append('<input type="hidden" name="'+fieldName+'[group][]" value="'+val+'" data-catids="'+parents_str+'">');
                        }


                    var li_product = $('<li class="group">'+
                            '<a class="remove">&#215</a>'+
                            '<span class="group_icon"></span>'+
                            '<span class="value"></span>'+
                        '</li>');

                    li_product.attr('val', val);
                    li_product.find('.value').text( $('a', $(this).parents('li:first')).html() );
                    li_product.find('.remove').attr('title', lang.t('удалить из списка'));
                    li_product.find('.product_icon').attr('title', lang.t('товар'));

                    if (options?.selectProductOptions?.additionalItemHtml) {
                        let additionalItemHtml = options.selectProductOptions.additionalItemHtml
                            .replaceAll('%type%', 'groups')
                            .replaceAll('%field_name%', fieldName)
                            .replaceAll('%item_id%', val);

                        li_product.append(additionalItemHtml);
                    }

                    selectedContainer.append(li_product);

                        //Удаляем выбранные ранне элементы, если отмечена более высокая по иерархии категория
                        $("input[data-catids*=',"+val+",']", inputContainer).each(function()
                        {
                            $("li[val='"+this.value+"']", divSelected).remove();
                            $(this).remove();
                        });
                    }

                } else {
                    $('ul input:checkbox', parentLi)
                        .removeAttr('checked')
                        .removeAttr('disabled');

                    if (!trigged) {
                        $("input[name='"+fieldName+"[group][]'][value="+val+"]", inputContainer).remove();
                        $("li[val="+val+"]", selectedContainer).remove();
                    }
                }
                //bindSelection();
                options.selectCheck(this, plugin_context);
                //$(context).trigger('new-content');
            };

            var watchSelectedInputs = function(callTrigger)
            {
                $('input:checkbox', plugin_context).each(function() {
                    if (!this.disabled) {
                        if ($("input[name='"+fieldName+"[group][]'][value="+this.value+"]", inputContainer).length>0) {
                            this.checked = true;
                            if (callTrigger) $(this).trigger('change',[true]);
                        }
                    }
                });
            };

            var bindEvents = function()
            {
                $('li, li > *', plugin_context).unbind();
                $('li a', plugin_context).click(onSelectItem);
                $('li', plugin_context).click(toggle);
                $('li input:checkbox', plugin_context).change(checkboxChange);
                watchSelectedInputs(true);
            };

            var onDialogOpen = function()
            {
                $('input', plugin_context).removeAttr('checked').removeAttr('disabled');
                watchSelectedInputs(true);
            };

            var changeCurrentPath = function()
            {
                var stack = [];
                var item = $('li a.act', plugin_context);
                item.parents('.admin-category li[qid]').each(function() {
                    var title = $('> a', this).text();
                    stack.push(title);
                });

                var pathContainer = $(options.pathContainer).empty();

                stack.forEach(function(value, index) {
                    pathContainer.prepend( $('<span>').text(value).wrap('<li>').parent() );
                });
                pathContainer.prepend( '<li><i class="zmdi zmdi-folder left-toggle"></i></li>' );
                pathContainer.find('li:last').addClass('active');
            };

            bindEvents();
            changeCurrentPath();
            $(plugin_context).bind('dialogOpen', onDialogOpen);
        };

        return this.each(make);
    }
})(jQuery);





/**
* JQuery plug-in для выбора товаров.
*/
(function($){
    $.fn.selectProduct = function(options)
    {
        var make = function()
        {
            //Текущие настройки
            var current = {
                page:1,
                pageSize:20,
                catid:0,
                filter:{}
            };

            var context = this;
            var urls = $.extend({
                getChild: '',
                getDialog: '',
                getProducts: ''
            }, $(context).data('urls'));

            options = $.extend({
                itemHtml: function(){
                    return $('<li class="product">'+
                            '<a class="remove">&#215</a>'+
                            '<span class="product_icon"></span>'+
                            '<span class="product_image cell-image" data-preview-url=""><img src="" alt=""/></span>'+
                            '<span class="barcode"></span>'+
                            '<span class="value"></span>'+
                        '</li>');
                },
                startButton: '.select-button',
                divLoader: '.loader',
                divResult: '.selected-goods',
                divCategory: '.admin-category',
                divProducts: '.product-container',
                tableProducts: '.product-list',
                dialog: 'productDialog',
                openDialog: false,
                additionalItemHtml: '',
                productItem: '.product-item',
                productOffersToggle: '.product-offers-toggle',

                userCost : '',
                divPaginator: '.paginator',
                pagLeft: '.pag_left',
                pagRight: '.pag_right',
                pagSubmit: '.pag_submit',
                pagPage: '.pag_page',
                pagPageSize: '.pag_pagesize',
                inputContainer: '.input-container',
                groupSelectedContainer:'.selected-container .group-block',
                selectedContainer: '.selected-container .product-block',
                urls: urls,
                selectButtonText: lang.t('Выбрать'),
                filterSet:'.set-filter',
                filterClear:'.clear-filter',
                showCostTypes: false,
                onResult: function(){},
                onCheckProduct: function() {}
            }, options);

            var inputContainer = $(options.inputContainer, context);
            var fieldName = inputContainer.data('fieldName');
            var selectedContainer = $(options.selectedContainer, context);
            var groupSelectedContainer = $(options.groupSelectedContainer, context);
            var initialized = false;
            var hideGroupCheckbox = (+$(this).hasClass('hide-group-cb')); //Скрывать checkbox у категорий
            var hideProductCheckbox = (+$(this).hasClass('hide-product-cb')); //Скрывать checkbox у категорий
            var showVirtualDirs = (+$(this).hasClass('show-virtual-dirs')); //Показывать виртальные категории
            var openDialogEvent;

            //Назначаем сортировку на элементы c товарами и категориям
            $(".product-block", $(context)).sortable({
                placeholder: "portlet-placeholder",
                //Когда останавливаемся, то сортируем список с идентификаторами
                update: function( event, ui ){
                    //Посмотрим позицию текущего элемента
                    var item_id = $(ui.item).attr('val');
                    var prev    = $(ui.item).prev(); //Определим кто перед нами
                    if (prev.length){ //Если перед нами кто-то есть, то переместимся к нему
                        $("[value='" + item_id + "']", inputContainer).insertAfter($("input[value='" + prev.attr('val') + "']", inputContainer));
                    }else{ //Если мы первые
                        $("[value='" + item_id + "']", inputContainer).insertBefore($("input:eq(0)", inputContainer));
                    }
                }
            });
            $(".group-block", $(context)).sortable();

            var tree, dialog;

            var toggleOffers = function (element) {
                if (!element.classList.contains('offers-loaded')) {
                    loadOffers(element);
                }
                element.classList.toggle('offers-open');
                toggleOffersElements(element, element.classList.contains('offers-open'));
            }

            var loadOffers = function (element) {
                element.classList.add('offers-loaded')
                $.ajaxQuery({
                    url: element.dataset.urlLoadOffers,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            element.insertAdjacentHTML('afterend', response.html);
                            toggleOffersElements(element, element.classList.contains('offers-open'));
                        }
                    }
                });
            }

            var toggleOffersElements = function (element, open) {
                while(element) {
                    element = element.nextElementSibling;
                    if (element.classList.contains('product-item-offer')) {
                        if (open) {
                            element.classList.remove('rs-hidden');
                        } else {
                            element.classList.add('rs-hidden');
                        }
                    } else {
                        break;
                    }
                }
            }

            var loadProducts = function(optVars)
            {
                $.ajaxQuery({
                    url: options.urls.getProducts,
                    data: $.extend(optVars, {
                        hideProductCheckbox: hideProductCheckbox
                    }),
                    success: function(response) {
                        dialog.removeClass('folders-open');
                        $(options.divProducts, dialog).html(response.html);
                        bindEvents();
                    }
                });
            };

            var showProductLoader = function()
            {
                var loader = $(options.divLoader, dialog);
                var container = $('.productblock', dialog);

                loader.height( container.height()+'px' );
                $('.overlay', loader).height( container.height()+'px' );
                loader.show();
            };

            var hideProductLoader = function()
            {
                $(options.divLoader).hide();

            };

            var onStartClick = function(e)
            {
                openDialogEvent = e;
                dialog = $('#'+options.dialog);

                if (!dialog.length) {
                    dialog = $('<div id="'+options.dialog+'" class="selectProduct"></div>');
                    var dialogParams = {
                        resize: onResize,
                        title: lang.t('Выберите товары или группы товаров'),
                        dialogClass:'select-product-dialog',
                        minWidth: 900,
                        width: 1000,
                        height: $(window).height()-130,
                        clickOut: false,
                        autoOpen:false,
                        modal:true,
                        resizable:false,
                        create: function() {
                            var wrapper = $('.admin-dialog-wrapper:first');
                            if (!wrapper.length) {
                                wrapper = $('<div class="admin-style admin-dialog-wrapper" />').appendTo('body');
                            }
                            $(this).closest('.ui-dialog').appendTo(wrapper);
                            $(this).data('dialogWrapper', wrapper);
                        },
                        open: function() {
                            $('.ui-widget-overlay:last').appendTo( $(this).data('dialogWrapper') );
                        },
                        buttons: {}
                    };

                    dialog.dialog(dialogParams);
                }

                var buttons = [];
                if (options.selectButtonText !== false) {
                    buttons.push({
                        text: options.selectButtonText,
                        click: onPressOk,
                        class: 'btn btn-success'
                    });
                }
                dialog.dialog('option', 'buttons', buttons);


                if (!initialized) {

                    var params = {
                        hideGroupCheckbox : hideGroupCheckbox,
                        hideProductCheckbox : hideProductCheckbox
                    };
                    if (showVirtualDirs){
                        params['showVirtualDirs'] = showVirtualDirs;
                    }

                    $.ajaxQuery({
                        url: options.urls.getDialog,
                        data: params,
                        success: function(response) {

                            dialog.html(response.html);
                            dialog.dialog('open');
                            bindFirst();
                            bindEvents();
                            onResize();

                            $(options.divCategory, dialog).rstree({
                                context: context,
                                urls: {
                                    getChild: options.urls.getChild
                                },
                                hideGroupCheckbox: hideGroupCheckbox,
                                hideProductCheckbox: hideProductCheckbox,
                                selectItem: function(li) {
                                    current.catid = li.attr('qid');
                                    current.page = 1;
                                    loadProducts(current);
                                },
                                selectCheck: function() {
                                    updateProductCheckbox();
                                },
                                selectProductOptions: options
                            });

                            // Вставка кастомных кнопок в buttonpane
                            if(options.showCostTypes){
                                $('.my-buttons-pane').remove();
                                var myPane = $('<div>').addClass('my-buttons-pane');
                                myPane.html($('.to-dialog-buttonpane'));
                                $('.ui-dialog-buttonpane').prepend(myPane);
                            }

                            initialized = true;
                        }
                    });

                } else {
                    dialog.dialog('open');
                    $(options.divCategory).trigger('dialogOpen');

                    $(options.tableProducts+' input:enabled').removeAttr('checked');
                    $(options.tableProducts+' .chk.checked').removeClass('checked');
                    watchSelectedInputs();
                }
            };

            var checkboxChange = function()
            {
                var val = $(this).val();

                if (this.checked) {
                    if ($(".products", inputContainer).length){
                        if ( !$(".products input[value='"+val+"']", inputContainer).length ) {
                            $(".products", inputContainer).append('<input type="hidden" name="'+fieldName+'[product][]" data-weight="'+$(this).data('weight')+'" data-catids="'+$(this).attr('catids')+'" value="'+val+'">');
                        }
                    }else{
                        if ( !$("input[value='"+val+"']", inputContainer).length ) {
                            $(inputContainer).append('<input type="hidden" name="' + fieldName + '[product][]" data-weight="' + $(this).data('weight') + '" data-catids="' + $(this).attr('catids') + '" value="' + val + '">');
                        }
                    }


                    var li_product = options.itemHtml();

                    li_product.attr('val', val);
                    li_product.find('.barcode').html($(this).data('barcode'));
                    li_product.find('.product_image').data('preview-url', $(this).data('preview-url'));
                    li_product.find('.product_image img').attr('src', $(this).data('image'));
                    li_product.find('.value').text( $('.title', $(this).parents('tr:first')).html() );
                    li_product.find('.remove').attr('title', lang.t('удалить из списка'));
                    li_product.find('.product_icon').attr('title', lang.t('товар'));
                    li_product.find('.onlyone')
                        .attr('title', lang.t('Всегда в количестве одна штука'))
                        .attr('name', 'concomitant_arr[onlyone]['+val+']');

                    if (options.onCheckProduct) {
                        options.onCheckProduct.call(this, val, li_product, dialog);
                    }

                    const additionalItemHtml = options.additionalItemHtml
                        .replaceAll('%type%', 'products')
                        .replaceAll('%field_name%', fieldName)
                        .replaceAll('%item_id%', val);
                    li_product.append(additionalItemHtml);

                    if (!$("li[val="+val+"]", selectedContainer).length > 0) {
                        selectedContainer.append(li_product).trigger('new-content');
                    }
                    $(this).closest('.chk').addClass('checked');
                } else {
                    $("input[name='"+fieldName+"[product][]'][value="+$(this).val()+"]", inputContainer).remove();
                    $("li[val="+val+"]", selectedContainer).remove();
                    $(this).closest('.chk').removeClass('checked');
                }
                bindSelection();
            };

            var bindEvents = function()
            {
                $(options.divProducts+' > *', dialog).unbind();

                if (options.onSelectProduct) {
                    $('tbody.product-list tr', dialog).click(function() {
                        options.onSelectProduct.call(this, {
                            openDialogEvent: openDialogEvent,
                            productTitle: $('.title', this).text(),
                            productBarcode: $('.barcode', this).text(),
                            productId: $(this).data('id'),
                            dialog: dialog
                        });
                    });
                }

                $(options.tableProducts+' input:checkbox', dialog).change(checkboxChange);

                $(options.productOffersToggle).on('click', (event) => {
                    toggleOffers(event.target.closest(options.productItem));
                });

                //Пагинатор
                $(options.pagLeft+','+options.pagRight).click(function() {
                    current.page = $(this).attr('gopage');
                    loadProducts(current);
                });

                $(options.pagSubmit).click(function() {
                    var pag = $(this).closest(options.divPaginator);
                    current.page = $(options.pagPage, pag).val();
                    current.pageSize = $(options.pagPageSize, pag).val();
                    loadProducts(current);
                });

                selectAll();
                updateProductCheckbox();
                watchSelectedInputs();
                setDefaultCostType();
            };

            var setDefaultCostType = function()
            {
                if(options.userCost){
                    var selector_element = $('select[name=costtype]', dialog);
                    if(selector_element){
                        selector_element.val(options.userCost);
                    }
                }
            };

            var selectAll = function()
            {
                $('input[name="select-all"]', dialog).on('change', function(){
                    var product_inputs = $(options.tableProducts, dialog).find('input[type=checkbox][value]');
                    product_inputs.prop('checked', $(this).prop('checked'));
                    product_inputs.change();
                });
                $(options.tableProducts, dialog).find('input').on('change', function(){
                    if(!$(this).prop('checked')){
                        $('input[name="select-all"]', dialog).prop('checked', false);
                    }
                });
            };

            var updateProductCheckbox = function()
            {
                var checked_dirs = $(options.divCategory+' input:checked', dialog);

                if (checked_dirs.length>0) {
                    $(options.tableProducts+' input', dialog).each(function() {

                        var product_dirs = $(this).data('catids');
                        var checked = false;

                        for(var i=0; i<checked_dirs.length; i++) {
                            if (product_dirs.indexOf(','+$(checked_dirs[i]).val()+',') != -1) {
                                checked = true;
                            }
                        }
                        if (checked) {
                            this.checked = true;
                            this.disabled = true;
                            $(this).closest('.chk').addClass('.checked');
                        } else {
                            $(this).closest('.chk').removeClass('.checked');
                            if (this.disabled) {
                                this.checked = false;
                                this.disabled = false;
                            }
                        }
                    });
                } else {
                    $(options.divProducts+' tr input:disabled', dialog).removeAttr('checked').removeAttr('disabled');
                }
                bindSelection();
            };

            var watchSelectedInputs = function()
            {
                $(options.tableProducts+' input', dialog).each(function() {
                    if (!this.disabled) {
                        if ($("input[name='"+fieldName+"[product][]'][value="+this.value+"]", inputContainer).length>0) {
                            this.checked = true;
                            $(this).closest('.chk').addClass('checked');
                        }
                    }
                });
            };

            var bindFirst = function()
            {
                //Фильтр
                $('.filter', dialog).submit(setFilter);

                $(options.filterClear, dialog).click(function() {
                    $('.field-id', dialog).val('');
                    $('.field-title', dialog).val('');
                    $('.field-barcode', dialog).val('');
                    $('.field-sku', dialog).val('');
                   current.filter = {};
                   loadProducts(current);
                });
            };

            var setFilter = function()
            {
                current.filter.id       = $('.field-id', dialog).val();
                current.filter.title   = $('.field-title', dialog).val();
                current.filter.barcode = $('.field-barcode', dialog).val();
                current.filter.sku = $('.field-sku', dialog).val();
                
                loadProducts(current);                
            };

            var onResize = function()
            {
                $('.column-left', dialog).height( dialog.height()+'px' );
                $('.column-right', dialog).height( dialog.height()+'px' );
            };

            var onDeleteSelectedProduct = function()
            {
                var li = $(this).parents('li:first');
                $("input[name='"+fieldName+"[product][]'][value="+li.attr('val')+"]", inputContainer).remove();
                li.remove();
            };

            var onDeleteSelectedGroup = function()
            {
                var li = $(this).parents('li:first');
                $("input[name='"+fieldName+"[group][]'][value="+li.attr('val')+"]", inputContainer).remove();
                li.remove();
            };

            /**
            * Поставить обработчики событий на список выбранных групп товаров
            */
            var bindSelection = function()
            {
                $('li.product a', selectedContainer).off('.selectproduct');
                $('li.group a', groupSelectedContainer).off('.selectproduct');

                $('li.product a', selectedContainer).on('click.selectproduct', onDeleteSelectedProduct);
                $('li.group a', groupSelectedContainer).on('click.selectproduct', onDeleteSelectedGroup);
            };

            /**
            * При нажатии кнопки выбрать (зафиксировать результат выбора)
            */
            var onPressOk = function()
            {
                options.onResult({
                    openDialogEvent: openDialogEvent,
                });
                closeDialog();
            };

            var closeDialog = function()
            {
                dialog.dialog('close');
            };

            if (options.openDialog){
                onStartClick();
            }
            $(context).on('click', options.startButton, onStartClick);
            bindSelection(); //Если в форме по умолчанию присутствуют выбранные раннее элементы
        };

        return this.each(make);
    }
})(jQuery);

//Событие нужно для блока дизайнера
var productSelectInitedEvel = new CustomEvent('product-select.inited');
if (document.querySelector('body')){
    document.querySelector('body').dispatchEvent(productSelectInitedEvel);
}

/**
 * Скрипт инициализирует колонку просмотра фотографий в таблице
 *
 * @author ReadyScript lab.
 */
(function($) {

    $.contentReady(function() {
        $('.cell-image[data-preview-url]').each(function() {
            $(this).hover(function() {
                var previewUrl = $(this).data('previewUrl');
                if (previewUrl != '') {
                    $('#imagePreviewWin').remove();
                    var win = $('<div id="imagePreviewWin" />')
                        .append('<i />')
                        .append($('<img />').attr('src', previewUrl ))
                        .css({
                            top: $(this).offset().top,
                            left: $(this).offset().left + $(this).width() + 20
                        }).appendTo('body');
                }
            }, function() {
                $('#imagePreviewWin').remove();
            });
        });
    });

})(jQuery);
/**
* JQuery plug-in для активации древовидных списков
*/
(function($){
    $.fn.rstree = function(options)
    {
        var make = function()
        {
            options = $.extend({
                useajax: true,
                inputContainer: '.input-container',
                divSelected:'.selected-container',
                selectedContainer: '.selected-container .group-block',
                pathContainer: '.current-path .breadcrumb',
                hideGroupCheckbox: 0,
                hideProductCheckbox: 0,
                urls: {
                    getChild: '',
                }
            }, options);

            var plugin_context = this;
            var context = options.context;

            var divSelected = $(options.divSelected, context);
            var inputContainer = $(options.inputContainer, context);
            var fieldName = inputContainer.data('fieldName');
            var selectedContainer = $(options.selectedContainer, context);
            var loadingChilds = false;

            var onSelectItem = function()
            {
                $('li a.act', plugin_context).removeClass('act');
                $(this).addClass('act');
                options.selectItem( $(this).parents('li:first'), plugin_context);
                changeCurrentPath();
            };

            var toggle = function(e)
            {
                var offset = $(e.target).offset();
                if (e.clientY > offset.top + 19) return false;

                if (e.target.tagName == 'LI')
                {
                    var jquery_el = $(e.target);
                    var el_class = jquery_el.attr('class');

                    if (el_class.indexOf('plus') > -1) {
                        loadChild(e.target, function()
                        {
                            //Отображаем подпункты
                            var newClass = jquery_el.attr('class').replace('plus','minus');
                            jquery_el.attr('class', newClass);
                            $('ul:first', e.target).show();
                        });

                    } else if (el_class.indexOf('minus') > -1) {
                        //Скрываем подпункты
                        var newClass = jquery_el.attr('class').replace('minus','plus');
                        jquery_el.attr('class', newClass);
                        $('ul:first', e.target).hide();
                    }

                    return false;
                }
            };

            var loadChild = function(li, callback)
            {
                if (loadingChilds) return false;
                if ($('>ul', li).length>0) {
                    callback();
                } else if (options.useajax) {
                    var $img = $('img', li);
                    var $img_before = $img.attr('src');
                    $img.attr('src', '/resource/img/adminstyle/small-loader.gif');
                    loadingChilds = true;

                    $.ajaxQuery({
                        loadingProgress: false,
                        url: options.urls.getChild,
                        data: {
                            id: $(li).attr('qid'),
                            hideGroupCheckbox:options.hideGroupCheckbox,
                            hideProductCheckbox: options.hideProductCheckbox,
                        },
                        success: function(response) {
                            $img.attr('src', $img_before);
                            $(li).append(response.html);
                            if ($('>input:checkbox', li).get(0).checked) {
                                $('ul input:checkbox', li)
                                    .attr('checked', 'checked')
                                    .attr('disabled','disabled');
                            }

                            bindEvents();
                            callback();
                            loadingChilds = false;
                        }

                    })
                }
            };

            var checkboxChange = function(e, trigged)
            {
                var val = this.value;
                var parentLi = $(this).parent('li');

                parents_str = ',';
                parentLi.parents('li[qid]').each(function() {
                    parents_str = parents_str + $(this).attr('qid')+',';
                });

                if (this.checked) {
                    $('ul input:checkbox', parentLi)
                        .attr('checked', 'checked')
                        .attr('disabled','disabled');

                    if (!trigged) { //Если это событие не вызвали мы сами же при открытии диалога
                        if ($(".dirs", inputContainer).length){
                            $(".dirs", inputContainer).append('<input type="hidden" name="'+fieldName+'[group][]" value="'+val+'" data-catids="'+parents_str+'">');
                        }else{
                            $(inputContainer).append('<input type="hidden" name="'+fieldName+'[group][]" value="'+val+'" data-catids="'+parents_str+'">');
                        }


                    var li_product = $('<li class="group">'+
                            '<a class="remove">&#215</a>'+
                            '<span class="group_icon"></span>'+
                            '<span class="value"></span>'+
                        '</li>');

                    li_product.attr('val', val);
                    li_product.find('.value').text( $('a', $(this).parents('li:first')).html() );
                    li_product.find('.remove').attr('title', lang.t('удалить из списка'));
                    li_product.find('.product_icon').attr('title', lang.t('товар'));

                    if (options?.selectProductOptions?.additionalItemHtml) {
                        let additionalItemHtml = options.selectProductOptions.additionalItemHtml
                            .replaceAll('%type%', 'groups')
                            .replaceAll('%field_name%', fieldName)
                            .replaceAll('%item_id%', val);

                        li_product.append(additionalItemHtml);
                    }

                    selectedContainer.append(li_product);

                        //Удаляем выбранные ранне элементы, если отмечена более высокая по иерархии категория
                        $("input[data-catids*=',"+val+",']", inputContainer).each(function()
                        {
                            $("li[val='"+this.value+"']", divSelected).remove();
                            $(this).remove();
                        });
                    }

                } else {
                    $('ul input:checkbox', parentLi)
                        .removeAttr('checked')
                        .removeAttr('disabled');

                    if (!trigged) {
                        $("input[name='"+fieldName+"[group][]'][value="+val+"]", inputContainer).remove();
                        $("li[val="+val+"]", selectedContainer).remove();
                    }
                }
                //bindSelection();
                options.selectCheck(this, plugin_context);
                //$(context).trigger('new-content');
            };

            var watchSelectedInputs = function(callTrigger)
            {
                $('input:checkbox', plugin_context).each(function() {
                    if (!this.disabled) {
                        if ($("input[name='"+fieldName+"[group][]'][value="+this.value+"]", inputContainer).length>0) {
                            this.checked = true;
                            if (callTrigger) $(this).trigger('change',[true]);
                        }
                    }
                });
            };

            var bindEvents = function()
            {
                $('li, li > *', plugin_context).unbind();
                $('li a', plugin_context).click(onSelectItem);
                $('li', plugin_context).click(toggle);
                $('li input:checkbox', plugin_context).change(checkboxChange);
                watchSelectedInputs(true);
            };

            var onDialogOpen = function()
            {
                $('input', plugin_context).removeAttr('checked').removeAttr('disabled');
                watchSelectedInputs(true);
            };

            var changeCurrentPath = function()
            {
                var stack = [];
                var item = $('li a.act', plugin_context);
                item.parents('.admin-category li[qid]').each(function() {
                    var title = $('> a', this).text();
                    stack.push(title);
                });

                var pathContainer = $(options.pathContainer).empty();

                stack.forEach(function(value, index) {
                    pathContainer.prepend( $('<span>').text(value).wrap('<li>').parent() );
                });
                pathContainer.prepend( '<li><i class="zmdi zmdi-folder left-toggle"></i></li>' );
                pathContainer.find('li:last').addClass('active');
            };

            bindEvents();
            changeCurrentPath();
            $(plugin_context).bind('dialogOpen', onDialogOpen);
        };

        return this.each(make);
    }
})(jQuery);





/**
* JQuery plug-in для выбора товаров.
*/
(function($){
    $.fn.selectProduct = function(options)
    {
        var make = function()
        {
            //Текущие настройки
            var current = {
                page:1,
                pageSize:20,
                catid:0,
                filter:{}
            };

            var context = this;
            var urls = $.extend({
                getChild: '',
                getDialog: '',
                getProducts: ''
            }, $(context).data('urls'));

            options = $.extend({
                itemHtml: function(){
                    return $('<li class="product">'+
                            '<a class="remove">&#215</a>'+
                            '<span class="product_icon"></span>'+
                            '<span class="product_image cell-image" data-preview-url=""><img src="" alt=""/></span>'+
                            '<span class="barcode"></span>'+
                            '<span class="value"></span>'+
                        '</li>');
                },
                startButton: '.select-button',
                divLoader: '.loader',
                divResult: '.selected-goods',
                divCategory: '.admin-category',
                divProducts: '.product-container',
                tableProducts: '.product-list',
                dialog: 'productDialog',
                openDialog: false,
                additionalItemHtml: '',
                productItem: '.product-item',
                productOffersToggle: '.product-offers-toggle',

                userCost : '',
                divPaginator: '.paginator',
                pagLeft: '.pag_left',
                pagRight: '.pag_right',
                pagSubmit: '.pag_submit',
                pagPage: '.pag_page',
                pagPageSize: '.pag_pagesize',
                inputContainer: '.input-container',
                groupSelectedContainer:'.selected-container .group-block',
                selectedContainer: '.selected-container .product-block',
                urls: urls,
                selectButtonText: lang.t('Выбрать'),
                filterSet:'.set-filter',
                filterClear:'.clear-filter',
                showCostTypes: false,
                onResult: function(){},
                onCheckProduct: function() {}
            }, options);

            var inputContainer = $(options.inputContainer, context);
            var fieldName = inputContainer.data('fieldName');
            var selectedContainer = $(options.selectedContainer, context);
            var groupSelectedContainer = $(options.groupSelectedContainer, context);
            var initialized = false;
            var hideGroupCheckbox = (+$(this).hasClass('hide-group-cb')); //Скрывать checkbox у категорий
            var hideProductCheckbox = (+$(this).hasClass('hide-product-cb')); //Скрывать checkbox у категорий
            var showVirtualDirs = (+$(this).hasClass('show-virtual-dirs')); //Показывать виртальные категории
            var openDialogEvent;

            //Назначаем сортировку на элементы c товарами и категориям
            $(".product-block", $(context)).sortable({
                placeholder: "portlet-placeholder",
                //Когда останавливаемся, то сортируем список с идентификаторами
                update: function( event, ui ){
                    //Посмотрим позицию текущего элемента
                    var item_id = $(ui.item).attr('val');
                    var prev    = $(ui.item).prev(); //Определим кто перед нами
                    if (prev.length){ //Если перед нами кто-то есть, то переместимся к нему
                        $("[value='" + item_id + "']", inputContainer).insertAfter($("input[value='" + prev.attr('val') + "']", inputContainer));
                    }else{ //Если мы первые
                        $("[value='" + item_id + "']", inputContainer).insertBefore($("input:eq(0)", inputContainer));
                    }
                }
            });
            $(".group-block", $(context)).sortable();

            var tree, dialog;

            var toggleOffers = function (element) {
                if (!element.classList.contains('offers-loaded')) {
                    loadOffers(element);
                }
                element.classList.toggle('offers-open');
                toggleOffersElements(element, element.classList.contains('offers-open'));
            }

            var loadOffers = function (element) {
                element.classList.add('offers-loaded')
                $.ajaxQuery({
                    url: element.dataset.urlLoadOffers,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            element.insertAdjacentHTML('afterend', response.html);
                            toggleOffersElements(element, element.classList.contains('offers-open'));
                        }
                    }
                });
            }

            var toggleOffersElements = function (element, open) {
                while(element) {
                    element = element.nextElementSibling;
                    if (element.classList.contains('product-item-offer')) {
                        if (open) {
                            element.classList.remove('rs-hidden');
                        } else {
                            element.classList.add('rs-hidden');
                        }
                    } else {
                        break;
                    }
                }
            }

            var loadProducts = function(optVars)
            {
                $.ajaxQuery({
                    url: options.urls.getProducts,
                    data: $.extend(optVars, {
                        hideProductCheckbox: hideProductCheckbox
                    }),
                    success: function(response) {
                        dialog.removeClass('folders-open');
                        $(options.divProducts, dialog).html(response.html);
                        bindEvents();
                    }
                });
            };

            var showProductLoader = function()
            {
                var loader = $(options.divLoader, dialog);
                var container = $('.productblock', dialog);

                loader.height( container.height()+'px' );
                $('.overlay', loader).height( container.height()+'px' );
                loader.show();
            };

            var hideProductLoader = function()
            {
                $(options.divLoader).hide();

            };

            var onStartClick = function(e)
            {
                openDialogEvent = e;
                dialog = $('#'+options.dialog);

                if (!dialog.length) {
                    dialog = $('<div id="'+options.dialog+'" class="selectProduct"></div>');
                    var dialogParams = {
                        resize: onResize,
                        title: lang.t('Выберите товары или группы товаров'),
                        dialogClass:'select-product-dialog',
                        minWidth: 900,
                        width: 1000,
                        height: $(window).height()-130,
                        clickOut: false,
                        autoOpen:false,
                        modal:true,
                        resizable:false,
                        create: function() {
                            var wrapper = $('.admin-dialog-wrapper:first');
                            if (!wrapper.length) {
                                wrapper = $('<div class="admin-style admin-dialog-wrapper" />').appendTo('body');
                            }
                            $(this).closest('.ui-dialog').appendTo(wrapper);
                            $(this).data('dialogWrapper', wrapper);
                        },
                        open: function() {
                            $('.ui-widget-overlay:last').appendTo( $(this).data('dialogWrapper') );
                        },
                        buttons: {}
                    };

                    dialog.dialog(dialogParams);
                }

                var buttons = [];
                if (options.selectButtonText !== false) {
                    buttons.push({
                        text: options.selectButtonText,
                        click: onPressOk,
                        class: 'btn btn-success'
                    });
                }
                dialog.dialog('option', 'buttons', buttons);


                if (!initialized) {

                    var params = {
                        hideGroupCheckbox : hideGroupCheckbox,
                        hideProductCheckbox : hideProductCheckbox
                    };
                    if (showVirtualDirs){
                        params['showVirtualDirs'] = showVirtualDirs;
                    }

                    $.ajaxQuery({
                        url: options.urls.getDialog,
                        data: params,
                        success: function(response) {

                            dialog.html(response.html);
                            dialog.dialog('open');
                            bindFirst();
                            bindEvents();
                            onResize();

                            $(options.divCategory, dialog).rstree({
                                context: context,
                                urls: {
                                    getChild: options.urls.getChild
                                },
                                hideGroupCheckbox: hideGroupCheckbox,
                                hideProductCheckbox: hideProductCheckbox,
                                selectItem: function(li) {
                                    current.catid = li.attr('qid');
                                    current.page = 1;
                                    loadProducts(current);
                                },
                                selectCheck: function() {
                                    updateProductCheckbox();
                                },
                                selectProductOptions: options
                            });

                            // Вставка кастомных кнопок в buttonpane
                            if(options.showCostTypes){
                                $('.my-buttons-pane').remove();
                                var myPane = $('<div>').addClass('my-buttons-pane');
                                myPane.html($('.to-dialog-buttonpane'));
                                $('.ui-dialog-buttonpane').prepend(myPane);
                            }

                            initialized = true;
                        }
                    });

                } else {
                    dialog.dialog('open');
                    $(options.divCategory).trigger('dialogOpen');

                    $(options.tableProducts+' input:enabled').removeAttr('checked');
                    $(options.tableProducts+' .chk.checked').removeClass('checked');
                    watchSelectedInputs();
                }
            };

            var checkboxChange = function()
            {
                var val = $(this).val();

                if (this.checked) {
                    if ($(".products", inputContainer).length){
                        if ( !$(".products input[value='"+val+"']", inputContainer).length ) {
                            $(".products", inputContainer).append('<input type="hidden" name="'+fieldName+'[product][]" data-weight="'+$(this).data('weight')+'" data-catids="'+$(this).attr('catids')+'" value="'+val+'">');
                        }
                    }else{
                        if ( !$("input[value='"+val+"']", inputContainer).length ) {
                            $(inputContainer).append('<input type="hidden" name="' + fieldName + '[product][]" data-weight="' + $(this).data('weight') + '" data-catids="' + $(this).attr('catids') + '" value="' + val + '">');
                        }
                    }


                    var li_product = options.itemHtml();

                    li_product.attr('val', val);
                    li_product.find('.barcode').html($(this).data('barcode'));
                    li_product.find('.product_image').data('preview-url', $(this).data('preview-url'));
                    li_product.find('.product_image img').attr('src', $(this).data('image'));
                    li_product.find('.value').text( $('.title', $(this).parents('tr:first')).html() );
                    li_product.find('.remove').attr('title', lang.t('удалить из списка'));
                    li_product.find('.product_icon').attr('title', lang.t('товар'));
                    li_product.find('.onlyone')
                        .attr('title', lang.t('Всегда в количестве одна штука'))
                        .attr('name', 'concomitant_arr[onlyone]['+val+']');

                    if (options.onCheckProduct) {
                        options.onCheckProduct.call(this, val, li_product, dialog);
                    }

                    const additionalItemHtml = options.additionalItemHtml
                        .replaceAll('%type%', 'products')
                        .replaceAll('%field_name%', fieldName)
                        .replaceAll('%item_id%', val);
                    li_product.append(additionalItemHtml);

                    if (!$("li[val="+val+"]", selectedContainer).length > 0) {
                        selectedContainer.append(li_product).trigger('new-content');
                    }
                    $(this).closest('.chk').addClass('checked');
                } else {
                    $("input[name='"+fieldName+"[product][]'][value="+$(this).val()+"]", inputContainer).remove();
                    $("li[val="+val+"]", selectedContainer).remove();
                    $(this).closest('.chk').removeClass('checked');
                }
                bindSelection();
            };

            var bindEvents = function()
            {
                $(options.divProducts+' > *', dialog).unbind();

                if (options.onSelectProduct) {
                    $('tbody.product-list tr', dialog).click(function() {
                        options.onSelectProduct.call(this, {
                            openDialogEvent: openDialogEvent,
                            productTitle: $('.title', this).text(),
                            productBarcode: $('.barcode', this).text(),
                            productId: $(this).data('id'),
                            dialog: dialog
                        });
                    });
                }

                $(options.tableProducts+' input:checkbox', dialog).change(checkboxChange);

                $(options.productOffersToggle).on('click', (event) => {
                    toggleOffers(event.target.closest(options.productItem));
                });

                //Пагинатор
                $(options.pagLeft+','+options.pagRight).click(function() {
                    current.page = $(this).attr('gopage');
                    loadProducts(current);
                });

                $(options.pagSubmit).click(function() {
                    var pag = $(this).closest(options.divPaginator);
                    current.page = $(options.pagPage, pag).val();
                    current.pageSize = $(options.pagPageSize, pag).val();
                    loadProducts(current);
                });

                selectAll();
                updateProductCheckbox();
                watchSelectedInputs();
                setDefaultCostType();
            };

            var setDefaultCostType = function()
            {
                if(options.userCost){
                    var selector_element = $('select[name=costtype]', dialog);
                    if(selector_element){
                        selector_element.val(options.userCost);
                    }
                }
            };

            var selectAll = function()
            {
                $('input[name="select-all"]', dialog).on('change', function(){
                    var product_inputs = $(options.tableProducts, dialog).find('input[type=checkbox][value]');
                    product_inputs.prop('checked', $(this).prop('checked'));
                    product_inputs.change();
                });
                $(options.tableProducts, dialog).find('input').on('change', function(){
                    if(!$(this).prop('checked')){
                        $('input[name="select-all"]', dialog).prop('checked', false);
                    }
                });
            };

            var updateProductCheckbox = function()
            {
                var checked_dirs = $(options.divCategory+' input:checked', dialog);

                if (checked_dirs.length>0) {
                    $(options.tableProducts+' input', dialog).each(function() {

                        var product_dirs = $(this).data('catids');
                        var checked = false;

                        for(var i=0; i<checked_dirs.length; i++) {
                            if (product_dirs.indexOf(','+$(checked_dirs[i]).val()+',') != -1) {
                                checked = true;
                            }
                        }
                        if (checked) {
                            this.checked = true;
                            this.disabled = true;
                            $(this).closest('.chk').addClass('.checked');
                        } else {
                            $(this).closest('.chk').removeClass('.checked');
                            if (this.disabled) {
                                this.checked = false;
                                this.disabled = false;
                            }
                        }
                    });
                } else {
                    $(options.divProducts+' tr input:disabled', dialog).removeAttr('checked').removeAttr('disabled');
                }
                bindSelection();
            };

            var watchSelectedInputs = function()
            {
                $(options.tableProducts+' input', dialog).each(function() {
                    if (!this.disabled) {
                        if ($("input[name='"+fieldName+"[product][]'][value="+this.value+"]", inputContainer).length>0) {
                            this.checked = true;
                            $(this).closest('.chk').addClass('checked');
                        }
                    }
                });
            };

            var bindFirst = function()
            {
                //Фильтр
                $('.filter', dialog).submit(setFilter);

                $(options.filterClear, dialog).click(function() {
                    $('.field-id', dialog).val('');
                    $('.field-title', dialog).val('');
                    $('.field-barcode', dialog).val('');
                    $('.field-sku', dialog).val('');
                   current.filter = {};
                   loadProducts(current);
                });
            };

            var setFilter = function()
            {
                current.filter.id       = $('.field-id', dialog).val();
                current.filter.title   = $('.field-title', dialog).val();
                current.filter.barcode = $('.field-barcode', dialog).val();
                current.filter.sku = $('.field-sku', dialog).val();
                
                loadProducts(current);                
            };

            var onResize = function()
            {
                $('.column-left', dialog).height( dialog.height()+'px' );
                $('.column-right', dialog).height( dialog.height()+'px' );
            };

            var onDeleteSelectedProduct = function()
            {
                var li = $(this).parents('li:first');
                $("input[name='"+fieldName+"[product][]'][value="+li.attr('val')+"]", inputContainer).remove();
                li.remove();
            };

            var onDeleteSelectedGroup = function()
            {
                var li = $(this).parents('li:first');
                $("input[name='"+fieldName+"[group][]'][value="+li.attr('val')+"]", inputContainer).remove();
                li.remove();
            };

            /**
            * Поставить обработчики событий на список выбранных групп товаров
            */
            var bindSelection = function()
            {
                $('li.product a', selectedContainer).off('.selectproduct');
                $('li.group a', groupSelectedContainer).off('.selectproduct');

                $('li.product a', selectedContainer).on('click.selectproduct', onDeleteSelectedProduct);
                $('li.group a', groupSelectedContainer).on('click.selectproduct', onDeleteSelectedGroup);
            };

            /**
            * При нажатии кнопки выбрать (зафиксировать результат выбора)
            */
            var onPressOk = function()
            {
                options.onResult({
                    openDialogEvent: openDialogEvent,
                });
                closeDialog();
            };

            var closeDialog = function()
            {
                dialog.dialog('close');
            };

            if (options.openDialog){
                onStartClick();
            }
            $(context).on('click', options.startButton, onStartClick);
            bindSelection(); //Если в форме по умолчанию присутствуют выбранные раннее элементы
        };

        return this.each(make);
    }
})(jQuery);

//Событие нужно для блока дизайнера
var productSelectInitedEvel = new CustomEvent('product-select.inited');
if (document.querySelector('body')){
    document.querySelector('body').dispatchEvent(productSelectInitedEvel);
}

/*! lightgallery - v1.2.1 - 2015-09-07
* http://sachinchoolur.github.io/lightGallery/
* Copyright (c) 2015 Sachin N; Licensed Apache 2.0 */
!function(a,b,c,d){"use strict";function e(b,d){return this.el=b,this.$el=a(b),this.s=a.extend({},f,d),this.modules={},this.lGalleryOn=!1,this.lgBusy=!1,this.hideBartimeout=!1,this.isTouch="ontouchstart"in c.documentElement,this.s.slideEndAnimatoin&&(this.s.hideControlOnEnd=!1),this.s.dynamic?this.$items=this.s.dynamicEl:"this"===this.s.selector?this.$items=this.$el:""!==this.s.selector?this.$items=this.$el.find(a(this.s.selector)):this.$items=this.$el.children(),this.$slide="",this.$outer="",this.init(),this}var f={mode:"lg-slide",cssEasing:"cubic-bezier(0.25, 0, 0.25, 1)",easing:"linear",speed:600,height:"100%",width:"100%",addClass:"",startClass:"lg-start-zoom",backdropDuration:150,hideBarsDelay:6e3,useLeft:!1,closable:!0,loop:!0,escKey:!0,keyPress:!0,controls:!0,slideEndAnimatoin:!0,hideControlOnEnd:!1,mousewheel:!0,appendSubHtmlTo:".lg-sub-html",preload:1,showAfterLoad:!0,selector:"",nextHtml:"",prevHtml:"",index:!1,iframeMaxWidth:"100%",download:!0,counter:!0,appendCounterTo:".lg-toolbar",swipeThreshold:50,enableSwipe:!0,enableDrag:!0,dynamic:!1,dynamicEl:[],galleryId:1};e.prototype.init=function(){var c=this;c.s.preload>c.$items.length&&(c.s.preload=c.$items.length);var d=b.location.hash;d.indexOf("lg="+this.s.galleryId)>0&&(c.index=parseInt(d.split("&slide=")[1],10),a("body").addClass("lg-from-hash"),a("body").hasClass("lg-on")||setTimeout(function(){c.build(c.index),a("body").addClass("lg-on")})),c.s.dynamic?(c.$el.trigger("onBeforeOpen.lg"),c.index=c.s.index||0,a("body").hasClass("lg-on")||setTimeout(function(){c.build(c.index),a("body").addClass("lg-on")})):c.$items.on("click.lgcustom",function(b){try{b.preventDefault(),b.preventDefault()}catch(d){b.returnValue=!1}c.$el.trigger("onBeforeOpen.lg"),c.index=c.s.index||c.$items.index(this),a("body").hasClass("lg-on")||(c.build(c.index),a("body").addClass("lg-on"))})},e.prototype.build=function(b){var c=this;c.structure(),a.each(a.fn.lightGallery.modules,function(b){c.modules[b]=new a.fn.lightGallery.modules[b](c.el)}),c.slide(b,!1,!1),c.s.keyPress&&c.keyPress(),c.$items.length>1&&(c.arrow(),setTimeout(function(){c.enableDrag(),c.enableSwipe()},50),c.s.mousewheel&&c.mousewheel()),c.counter(),c.closeGallery(),c.$el.trigger("onAfterOpen.lg"),c.$outer.on("mousemove.lg click.lg touchstart.lg",function(){c.$outer.removeClass("lg-hide-items"),clearTimeout(c.hideBartimeout),c.hideBartimeout=setTimeout(function(){c.$outer.addClass("lg-hide-items")},c.s.hideBarsDelay)})},e.prototype.structure=function(){var c,d="",e="",f=0,g="",h=this;for(a("body").append('<div class="lg-backdrop"></div>'),a(".lg-backdrop").css("transition-duration",this.s.backdropDuration+"ms"),f=0;f<this.$items.length;f++)d+='<div class="lg-item"></div>';if(this.s.controls&&this.$items.length>1&&(e='<div class="lg-actions"><div class="lg-prev lg-icon">'+this.s.prevHtml+'</div><div class="lg-next lg-icon">'+this.s.nextHtml+"</div></div>"),".lg-sub-html"===this.s.appendSubHtmlTo&&(g='<div class="lg-sub-html"></div>'),c='<div class="lg-outer '+this.s.addClass+" "+this.s.startClass+'"><div class="lg" style="width:'+this.s.width+"; height:"+this.s.height+'"><div class="lg-inner">'+d+'</div><div class="lg-toolbar group"><span class="lg-close lg-icon"></span></div>'+e+g+"</div></div>",a("body").append(c),this.$outer=a(".lg-outer"),this.$slide=this.$outer.find(".lg-item"),this.s.useLeft?this.$outer.addClass("lg-use-left"):this.$outer.addClass("lg-use-css3"),h.setTop(),a(b).on("resize.lg orientationchange.lg",function(){setTimeout(function(){h.setTop()},100)}),this.$slide.eq(this.index).addClass("lg-current"),this.doCss()?this.$outer.addClass("lg-css3"):this.$outer.addClass("lg-css"),this.$outer.addClass(this.s.mode),this.s.enableDrag&&this.$items.length>1&&this.$outer.addClass("lg-grab"),this.s.showAfterLoad&&this.$outer.addClass("lg-show-after-load"),this.doCss()){var i=this.$outer.find(".lg-inner");i.css("transition-timing-function",this.s.cssEasing),i.css("transition-duration",this.s.speed+"ms")}a(".lg-backdrop").addClass("in"),setTimeout(function(){h.$outer.addClass("lg-visible")},this.s.backdropDuration),this.s.download&&this.$outer.find(".lg-toolbar").append('<a id="lg-download" target="_blank" download class="lg-download lg-icon"></a>')},e.prototype.setTop=function(){if("100%"!==this.s.height){var c=a(b).height(),d=(c-parseInt(this.s.height,10))/2,e=this.$outer.find(".lg");c>=parseInt(this.s.height,10)?e.css("top",d+"px"):e.css("top","0px")}},e.prototype.doCss=function(){var a=function(){var a=["transition","MozTransition","WebkitTransition","OTransition","msTransition","KhtmlTransition"],b=c.documentElement,d=0;for(d=0;d<a.length;d++)if(a[d]in b.style)return!0};return a()?!0:!1},e.prototype.isVideo=function(a,b){var c;if(c=this.s.dynamic?this.s.dynamicEl[b].html:this.$items.eq(b).attr("data-html"),!a&&c)return{html5:!0};var d=a.match(/\/\/(?:www\.)?youtu(?:\.be|be\.com)\/(?:watch\?v=|embed\/)?([a-z0-9\-]+)/i),e=a.match(/\/\/(?:www\.)?vimeo.com\/([0-9a-z\-_]+)/i),f=a.match(/\/\/(?:www\.)?dai.ly\/([0-9a-z\-_]+)/i);return d?{youtube:d}:e?{vimeo:e}:f?{dailymotion:f}:void 0},e.prototype.counter=function(){this.s.counter&&a(this.s.appendCounterTo).append('<div id="lg-counter"><span id="lg-counter-current">'+(parseInt(this.index,10)+1)+'</span> / <span id="lg-counter-all">'+this.$items.length+"</span></div>")},e.prototype.addHtml=function(b){var c=null;if(c=this.s.dynamic?this.s.dynamicEl[b].subHtml:this.$items.eq(b).attr("data-sub-html"),"undefined"!=typeof c&&null!==c){var d=c.substring(0,1);c="."===d||"#"===d?a(c).html():c}else c="";".lg-sub-html"===this.s.appendSubHtmlTo?(this.$outer.find(this.s.appendSubHtmlTo).html(c),""===c?this.$outer.find(this.s.appendSubHtmlTo).addClass("lg-empty-html"):this.$outer.find(this.s.appendSubHtmlTo).removeClass("lg-empty-html")):this.$slide.eq(b).append(c),this.$el.trigger("onAfterAppendSubHtml.lg",[b])},e.prototype.preload=function(a){var b=1,c=1;for(b=1;b<=this.s.preload&&!(b>=this.$items.length-a);b++)this.loadContent(a+b,!1,0);for(c=1;c<=this.s.preload&&!(0>a-c);c++)this.loadContent(a-c,!1,0)},e.prototype.loadContent=function(c,d,e){var f,g,h,i,j,k,l=this,m=!1,n=function(c){for(var d=[],e=[],f=0;f<c.length;f++){var h=c[f].split(" ");""===h[0]&&h.splice(0,1),e.push(h[0]),d.push(h[1])}for(var i=a(b).width(),j=0;j<d.length;j++)if(parseInt(d[j],10)>i){g=e[j];break}};if(l.s.dynamic){if(l.s.dynamicEl[c].poster&&(m=!0,h=l.s.dynamicEl[c].poster),k=l.s.dynamicEl[c].html,g=l.s.dynamicEl[c].src,l.s.dynamicEl[c].responsive){var o=l.s.dynamicEl[c].responsive.split(",");n(o)}i=l.s.dynamicEl[c].srcset,j=l.s.dynamicEl[c].sizes}else{if(l.$items.eq(c).attr("data-poster")&&(m=!0,h=l.$items.eq(c).attr("data-poster")),k=l.$items.eq(c).attr("data-html"),g=l.$items.eq(c).attr("href")||l.$items.eq(c).attr("data-src"),l.$items.eq(c).attr("data-responsive")){var p=l.$items.eq(c).attr("data-responsive").split(",");n(p)}i=l.$items.eq(c).attr("data-srcset"),j=l.$items.eq(c).attr("data-sizes")}var q=!1;l.s.dynamic?l.s.dynamicEl[c].iframe&&(q=!0):"true"===l.$items.eq(c).attr("data-iframe")&&(q=!0);var r=l.isVideo(g,c);if(!l.$slide.eq(c).hasClass("lg-loaded")){if(q)l.$slide.eq(c).prepend('<div class="lg-video-cont" style="max-width:'+l.s.iframeMaxWidth+'"><div class="lg-video"><iframe class="lg-object" frameborder="0" src="'+g+'"  allowfullscreen="true"></iframe></div></div>');else if(m){var s="";s=r&&r.youtube?"lg-has-youtube":r&&r.vimeo?"lg-has-vimeo":"lg-has-html5",l.$slide.eq(c).prepend('<div class="lg-video-cont '+s+' "><div class="lg-video"><span class="lg-video-play"></span><img class="lg-object lg-has-poster" src="'+h+'" /></div></div>')}else r?(l.$slide.eq(c).prepend('<div class="lg-video-cont "><div class="lg-video"></div></div>'),l.$el.trigger("hasVideo.lg",[c,g,k])):l.$slide.eq(c).prepend('<div class="lg-img-wrap"> <img class="lg-object lg-image" src="'+g+'" /> </div>');if(l.$el.trigger("onAferAppendSlide.lg",[c]),f=l.$slide.eq(c).find(".lg-object"),j&&f.attr("sizes",j),i){f.attr("srcset",i);try{picturefill({elements:[f[0]]})}catch(t){console.error("Make sure you have included Picturefill version 2")}}".lg-sub-html"!==this.s.appendSubHtmlTo&&l.addHtml(c),l.$slide.eq(c).addClass("lg-loaded")}l.$slide.eq(c).find(".lg-object").on("load.lg error.lg",function(){var b=0;e&&!a("body").hasClass("lg-from-hash")&&(b=e),setTimeout(function(){l.$slide.eq(c).addClass("lg-complete"),l.$el.trigger("onSlideItemLoad.lg",[c,e||0])},b)}),r&&r.html5&&!m&&l.$slide.eq(c).addClass("lg-complete"),d===!0&&(l.$slide.eq(c).hasClass("lg-complete")?l.preload(c):l.$slide.eq(c).find(".lg-object").on("load.lg error.lg",function(){l.preload(c)}))},e.prototype.slide=function(b,c,d){var e=this.$outer.find(".lg-current").index(),f=this;if(!f.lGalleryOn||e!==b){var g=this.$slide.length,h=f.lGalleryOn?this.s.speed:0,i=!1,j=!1;if(!f.lgBusy){if(this.$el.trigger("onBeforeSlide.lg",[e,b,c,d]),f.lgBusy=!0,clearTimeout(f.hideBartimeout),".lg-sub-html"===this.s.appendSubHtmlTo&&setTimeout(function(){f.addHtml(b)},h),this.arrowDisable(b),c){var k=b-1,l=b+1;0===b&&e===g-1?(l=0,k=g-1):b===g-1&&0===e&&(l=0,k=g-1),this.$slide.removeClass("lg-prev-slide lg-current lg-next-slide"),f.$slide.eq(k).addClass("lg-prev-slide"),f.$slide.eq(l).addClass("lg-next-slide"),f.$slide.eq(b).addClass("lg-current")}else f.$outer.addClass("lg-no-trans"),this.$slide.removeClass("lg-prev-slide lg-next-slide"),e>b?(j=!0,0!==b||e!==g-1||d||(j=!1,i=!0)):b>e&&(i=!0,b!==g-1||0!==e||d||(j=!0,i=!1)),j?(this.$slide.eq(b).addClass("lg-prev-slide"),this.$slide.eq(e).addClass("lg-next-slide")):i&&(this.$slide.eq(b).addClass("lg-next-slide"),this.$slide.eq(e).addClass("lg-prev-slide")),setTimeout(function(){f.$slide.removeClass("lg-current"),f.$slide.eq(b).addClass("lg-current"),f.$outer.removeClass("lg-no-trans")},50);if(f.lGalleryOn?(setTimeout(function(){f.loadContent(b,!0,0)},this.s.speed+50),setTimeout(function(){f.lgBusy=!1,f.$el.trigger("onAfterSlide.lg",[e,b,c,d])},this.s.speed),f.doCss()||(f.$slide.fadeOut(f.s.speed),f.$slide.eq(b).fadeIn(f.s.speed))):(f.loadContent(b,!0,f.s.backdropDuration),f.lgBusy=!1,f.$el.trigger("onAfterSlide.lg",[e,b,c,d]),f.doCss()||(f.$slide.fadeOut(50),f.$slide.eq(b).fadeIn(50))),this.s.download){var m;m=f.s.dynamic?f.s.dynamicEl[b].downloadUrl||f.s.dynamicEl[b].src:f.$items.eq(b).attr("data-download-url")||f.$items.eq(b).attr("href")||f.$items.eq(b).attr("data-src"),a("#lg-download").attr("href",m)}f.lGalleryOn=!0,this.s.counter&&a("#lg-counter-current").text(b+1)}}},e.prototype.goToNextSlide=function(a){var b=this;b.lgBusy||(b.index+1<b.$slide.length?(b.index++,b.$el.trigger("onBeforeNextSlide.lg",[b.index]),b.slide(b.index,a,!1)):b.s.loop?(b.index=0,b.$el.trigger("onBeforeNextSlide.lg",[b.index]),b.slide(b.index,a,!1)):b.s.slideEndAnimatoin&&(b.$outer.addClass("lg-right-end"),setTimeout(function(){b.$outer.removeClass("lg-right-end")},400)))},e.prototype.goToPrevSlide=function(a){var b=this;b.lgBusy||(b.index>0?(b.index--,b.$el.trigger("onBeforePrevSlide.lg",[b.index,a]),b.slide(b.index,a,!1)):b.s.loop?(b.index=b.$items.length-1,b.$el.trigger("onBeforePrevSlide.lg",[b.index,a]),b.slide(b.index,a,!1)):b.s.slideEndAnimatoin&&(b.$outer.addClass("lg-left-end"),setTimeout(function(){b.$outer.removeClass("lg-left-end")},400)))},e.prototype.keyPress=function(){var c=this;this.$items.length>1&&a(b).on("keyup.lg",function(a){c.$items.length>1&&(37===a.keyCode&&(a.preventDefault(),c.goToPrevSlide()),39===a.keyCode&&(a.preventDefault(),c.goToNextSlide()))}),a(b).on("keydown.lg",function(a){c.s.escKey===!0&&27===a.keyCode&&(a.preventDefault(),c.$outer.hasClass("lg-thumb-open")?c.$outer.removeClass("lg-thumb-open"):c.destroy())})},e.prototype.arrow=function(){var a=this;this.$outer.find(".lg-prev").on("click.lg",function(){a.goToPrevSlide()}),this.$outer.find(".lg-next").on("click.lg",function(){a.goToNextSlide()})},e.prototype.arrowDisable=function(a){!this.s.loop&&this.s.hideControlOnEnd&&(a+1<this.$slide.length?this.$outer.find(".lg-next").removeAttr("disabled").removeClass("disabled"):this.$outer.find(".lg-next").attr("disabled","disabled").addClass("disabled"),a>0?this.$outer.find(".lg-prev").removeAttr("disabled").removeClass("disabled"):this.$outer.find(".lg-prev").attr("disabled","disabled").addClass("disabled"))},e.prototype.setTranslate=function(a,b,c){this.s.useLeft?a.css("left",b):a.css({transform:"translate3d("+b+"px, "+c+"px, 0px)"})},e.prototype.touchMove=function(b,c){var d=c-b;this.$outer.addClass("lg-dragging"),this.setTranslate(this.$slide.eq(this.index),d,0),this.setTranslate(a(".lg-prev-slide"),-this.$slide.eq(this.index).width()+d,0),this.setTranslate(a(".lg-next-slide"),this.$slide.eq(this.index).width()+d,0)},e.prototype.touchEnd=function(a){var b=this;"lg-slide"!==b.s.mode&&b.$outer.addClass("lg-slide"),this.$slide.not(".lg-current, .lg-prev-slide, .lg-next-slide").css("opacity","0"),setTimeout(function(){b.$outer.removeClass("lg-dragging"),0>a&&Math.abs(a)>b.s.swipeThreshold?b.goToNextSlide(!0):a>0&&Math.abs(a)>b.s.swipeThreshold?b.goToPrevSlide(!0):Math.abs(a)<5&&b.$el.trigger("onSlideClick.lg"),b.$slide.removeAttr("style")}),setTimeout(function(){b.$outer.hasClass("lg-dragging")||"lg-slide"===b.s.mode||b.$outer.removeClass("lg-slide")},b.s.speed+100)},e.prototype.enableSwipe=function(){var a=this,b=0,c=0,d=!1;a.s.enableSwipe&&a.isTouch&&a.doCss()&&(a.$slide.on("touchstart.lg",function(c){a.$outer.hasClass("lg-zoomed")||a.lgBusy||(c.preventDefault(),a.manageSwipeClass(),b=c.originalEvent.targetTouches[0].pageX)}),a.$slide.on("touchmove.lg",function(e){a.$outer.hasClass("lg-zoomed")||(e.preventDefault(),c=e.originalEvent.targetTouches[0].pageX,a.touchMove(b,c),d=!0)}),a.$slide.on("touchend.lg",function(){a.$outer.hasClass("lg-zoomed")||(d?(d=!1,a.touchEnd(c-b)):a.$el.trigger("onSlideClick.lg"))}))},e.prototype.enableDrag=function(){var c=this,d=0,e=0,f=!1,g=!1;c.s.enableDrag&&!c.isTouch&&c.doCss()&&(c.$slide.on("mousedown.lg",function(b){c.$outer.hasClass("lg-zoomed")||(a(b.target).hasClass("lg-object")||a(b.target).hasClass("lg-video-play"))&&(b.preventDefault(),c.lgBusy||(c.manageSwipeClass(),d=b.pageX,f=!0,c.$outer.scrollLeft+=1,c.$outer.scrollLeft-=1,c.$outer.removeClass("lg-grab").addClass("lg-grabbing"),c.$el.trigger("onDragstart.lg")))}),a(b).on("mousemove.lg",function(a){f&&(g=!0,e=a.pageX,c.touchMove(d,e),c.$el.trigger("onDragmove.lg"))}),a(b).on("mouseup.lg",function(b){g?(g=!1,c.touchEnd(e-d),c.$el.trigger("onDragend.lg")):(a(b.target).hasClass("lg-object")||a(b.target).hasClass("lg-video-play"))&&c.$el.trigger("onSlideClick.lg"),f&&(f=!1,c.$outer.removeClass("lg-grabbing").addClass("lg-grab"))}))},e.prototype.manageSwipeClass=function(){var a=this.index+1,b=this.index-1,c=this.$slide.length;this.s.loop&&(0===this.index?b=c-1:this.index===c-1&&(a=0)),this.$slide.removeClass("lg-next-slide lg-prev-slide"),b>-1&&this.$slide.eq(b).addClass("lg-prev-slide"),this.$slide.eq(a).addClass("lg-next-slide")},e.prototype.mousewheel=function(){var a=this;a.$outer.on("mousewheel.lg",function(b){b.deltaY>0?a.goToPrevSlide():a.goToNextSlide(),b.preventDefault()})},e.prototype.closeGallery=function(){var b=this,c=!1;this.$outer.find(".lg-close").on("click.lg",function(){b.destroy()}),b.s.closable&&(b.$outer.on("mousedown.lg",function(b){c=a(b.target).is(".lg-outer")||a(b.target).is(".lg-item ")||a(b.target).is(".lg-img-wrap")?!0:!1}),b.$outer.on("mouseup.lg",function(d){(a(d.target).is(".lg-outer")||a(d.target).is(".lg-item ")||a(d.target).is(".lg-img-wrap")&&c)&&(b.$outer.hasClass("lg-dragging")||b.destroy())}))},e.prototype.destroy=function(c){var d=this;d.$el.trigger("onBeforeClose.lg"),c&&(this.$items.off("click.lg click.lgcustom"),a.removeData(d.el,"lightGallery")),this.$el.off(".lg.tm"),a.each(a.fn.lightGallery.modules,function(a){d.modules[a]&&d.modules[a].destroy()}),this.lGalleryOn=!1,clearTimeout(d.hideBartimeout),this.hideBartimeout=!1,a(b).off(".lg"),a("body").removeClass("lg-on lg-from-hash"),d.$outer&&d.$outer.removeClass("lg-visible"),a(".lg-backdrop").removeClass("in"),setTimeout(function(){d.$outer&&d.$outer.remove(),a(".lg-backdrop").remove(),d.$el.trigger("onCloseAfter.lg")},d.s.backdropDuration+50)},a.fn.lightGallery=function(b){return this.each(function(){if(a.data(this,"lightGallery"))try{a(this).data("lightGallery").init()}catch(c){console.error("lightGallery has not initiated properly")}else a.data(this,"lightGallery",new e(this,b))})},a.fn.lightGallery.modules={}}(jQuery,window,document),function(a,b,c,d){"use strict";var e={autoplay:!1,pause:5e3,progressBar:!0,fourceAutoplay:!1,autoplayControls:!0,appendAutoplayControlsTo:".lg-toolbar"},f=function(b){return this.core=a(b).data("lightGallery"),this.$el=a(b),this.core.$items.length<2?!1:(this.core.s=a.extend({},e,this.core.s),this.interval=!1,this.fromAuto=!0,this.canceledOnTouch=!1,this.fourceAutoplayTemp=this.core.s.fourceAutoplay,this.core.doCss()||(this.core.s.progressBar=!1),this.init(),this)};f.prototype.init=function(){var a=this;a.core.s.autoplayControls&&a.controls(),a.core.s.progressBar&&a.core.$outer.find(".lg").append('<div class="lg-progress-bar"><div class="lg-progress"></div></div>'),a.progress(),a.core.s.autoplay&&a.startlAuto(),a.$el.on("onDragstart.lg.tm touchstart.lg.tm",function(){a.interval&&(a.cancelAuto(),a.canceledOnTouch=!0)}),a.$el.on("onDragend.lg.tm touchend.lg.tm onSlideClick.lg.tm",function(){!a.interval&&a.canceledOnTouch&&(a.startlAuto(),a.canceledOnTouch=!1)})},f.prototype.progress=function(){var a,b,c=this;c.$el.on("onBeforeSlide.lg.tm",function(){c.core.s.progressBar&&c.fromAuto&&(a=c.core.$outer.find(".lg-progress-bar"),b=c.core.$outer.find(".lg-progress"),c.interval&&(b.removeAttr("style"),a.removeClass("lg-start"),setTimeout(function(){b.css("transition","width "+(c.core.s.speed+c.core.s.pause)+"ms ease 0s"),a.addClass("lg-start")},20))),c.fromAuto||c.core.s.fourceAutoplay||c.cancelAuto(),c.fromAuto=!1})},f.prototype.controls=function(){var b=this,c='<span class="lg-autoplay-button lg-icon"></span>';a(this.core.s.appendAutoplayControlsTo).append(c),b.core.$outer.find(".lg-autoplay-button").on("click.lg",function(){a(b.core.$outer).hasClass("lg-show-autoplay")?(b.cancelAuto(),b.core.s.fourceAutoplay=!1):b.interval||(b.startlAuto(),b.core.s.fourceAutoplay=b.fourceAutoplayTemp)})},f.prototype.startlAuto=function(){var a=this;a.core.$outer.find(".lg-progress").css("transition","width "+(a.core.s.speed+a.core.s.pause)+"ms ease 0s"),a.core.$outer.addClass("lg-show-autoplay"),a.core.$outer.find(".lg-progress-bar").addClass("lg-start"),a.interval=setInterval(function(){a.core.index+1<a.core.$items.length?a.core.index=a.core.index:a.core.index=-1,a.core.index++,a.fromAuto=!0,a.core.slide(a.core.index,!1,!1)},a.core.s.speed+a.core.s.pause)},f.prototype.cancelAuto=function(){clearInterval(this.interval),this.interval=!1,this.core.$outer.find(".lg-progress").removeAttr("style"),this.core.$outer.removeClass("lg-show-autoplay"),this.core.$outer.find(".lg-progress-bar").removeClass("lg-start")},f.prototype.destroy=function(){this.cancelAuto(),this.core.$outer.find(".lg-progress-bar").remove()},a.fn.lightGallery.modules.autoplay=f}(jQuery,window,document),function(a,b,c,d){"use strict";var e={fullScreen:!0},f=function(b){return this.core=a(b).data("lightGallery"),this.$el=a(b),this.core.s=a.extend({},e,this.core.s),this.init(),this};f.prototype.init=function(){var a="";if(this.core.s.fullScreen){if(!(c.fullscreenEnabled||c.webkitFullscreenEnabled||c.mozFullScreenEnabled||c.msFullscreenEnabled))return;a='<span class="lg-fullscreen lg-icon"></span>',this.core.$outer.find(".lg-toolbar").append(a),this.fullScreen()}},f.prototype.reuestFullscreen=function(){var a=c.documentElement;a.requestFullscreen?a.requestFullscreen():a.msRequestFullscreen?a.msRequestFullscreen():a.mozRequestFullScreen?a.mozRequestFullScreen():a.webkitRequestFullscreen&&a.webkitRequestFullscreen()},f.prototype.exitFullscreen=function(){c.exitFullscreen?c.exitFullscreen():c.msExitFullscreen?c.msExitFullscreen():c.mozCancelFullScreen?c.mozCancelFullScreen():c.webkitExitFullscreen&&c.webkitExitFullscreen()},f.prototype.fullScreen=function(){var b=this;a(c).on("fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg",function(){b.core.$outer.toggleClass("lg-fullscreen-on")}),this.core.$outer.find(".lg-fullscreen").on("click.lg",function(){c.fullscreenElement||c.mozFullScreenElement||c.webkitFullscreenElement||c.msFullscreenElement?b.exitFullscreen():b.reuestFullscreen()})},f.prototype.destroy=function(){this.exitFullscreen(),a(c).off("fullscreenchange.lg webkitfullscreenchange.lg mozfullscreenchange.lg MSFullscreenChange.lg")},a.fn.lightGallery.modules.fullscreen=f}(jQuery,window,document),function(a,b,c,d){"use strict";var e={pager:!1},f=function(b){return this.core=a(b).data("lightGallery"),this.$el=a(b),this.core.s=a.extend({},e,this.core.s),this.core.s.pager&&this.core.$items.length>1&&this.init(),this};f.prototype.init=function(){var b,c,d,e=this,f="";if(e.core.$outer.find(".lg").append('<div class="lg-pager-outer"></div>'),e.core.s.dynamic)for(var g=0;g<e.core.s.dynamicEl.length;g++)f+='<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="'+e.core.s.dynamicEl[g].thumb+'" /></div></span>';else e.core.$items.each(function(){f+=e.core.s.exThumbImage?'<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="'+a(this).attr(e.core.s.exThumbImage)+'" /></div></span>':'<span class="lg-pager-cont"> <span class="lg-pager"></span><div class="lg-pager-thumb-cont"><span class="lg-caret"></span> <img src="'+a(this).find("img").attr("src")+'" /></div></span>'});c=e.core.$outer.find(".lg-pager-outer"),c.html(f),b=e.core.$outer.find(".lg-pager-cont"),b.on("click.lg touchend.lg",function(){var b=a(this);e.core.index=b.index(),e.core.slide(e.core.index,!1,!1)}),c.on("mouseover.lg",function(){clearTimeout(d),c.addClass("lg-pager-hover")}),c.on("mouseout.lg",function(){d=setTimeout(function(){c.removeClass("lg-pager-hover")})}),e.core.$el.on("onBeforeSlide.lg.tm",function(a,c,d){b.removeClass("lg-pager-active"),b.eq(d).addClass("lg-pager-active")})},f.prototype.destroy=function(){},a.fn.lightGallery.modules.pager=f}(jQuery,window,document),function(a,b,c,d){"use strict";var e={thumbnail:!0,animateThumb:!0,currentPagerPosition:"middle",thumbWidth:100,thumbContHeight:100,thumbMargin:5,exThumbImage:!1,showThumbByDefault:!0,toogleThumb:!0,enableThumbDrag:!0,enableThumbSwipe:!0,swipeThreshold:50,loadYoutubeThumbnail:!0,youtubeThumbSize:1,loadVimeoThumbnail:!0,vimeoThumbSize:"thumbnail_small",loadDailymotionThumbnail:!0},f=function(b){return this.core=a(b).data("lightGallery"),this.core.s=a.extend({},e,this.core.s),this.$el=a(b),this.$thumbOuter=null,this.thumbOuterWidth=0,this.thumbTotalWidth=this.core.$items.length*(this.core.s.thumbWidth+this.core.s.thumbMargin),this.thumbIndex=this.core.index,this.left=0,this.init(),this};f.prototype.init=function(){this.core.s.thumbnail&&this.core.$items.length>1&&(this.core.s.showThumbByDefault&&this.core.$outer.addClass("lg-thumb-open"),this.build(),this.core.s.animateThumb?(this.core.s.enableThumbDrag&&!this.core.isTouch&&this.core.doCss()&&this.enableThumbDrag(),this.core.s.enableThumbSwipe&&this.core.isTouch&&this.core.doCss()&&this.enableThumbSwipe(),this.thumbClickable=!1):this.thumbClickable=!0,this.toogle(),this.thumbkeyPress())},f.prototype.build=function(){function c(a,b,c){var d,h=e.core.isVideo(a,c)||{},i="";h.youtube||h.vimeo||h.dailymotion?h.youtube?d=e.core.s.loadYoutubeThumbnail?"//img.youtube.com/vi/"+h.youtube[1]+"/"+e.core.s.youtubeThumbSize+".jpg":b:h.vimeo?e.core.s.loadVimeoThumbnail?(d="//i.vimeocdn.com/video/error_"+g+".jpg",i=h.vimeo[1]):d=b:h.dailymotion&&(d=e.core.s.loadDailymotionThumbnail?"//www.dailymotion.com/thumbnail/video/"+h.dailymotion[1]:b):d=b,f+='<div data-vimeo-id="'+i+'" class="lg-thumb-item" style="width:'+e.core.s.thumbWidth+"px; margin-right: "+e.core.s.thumbMargin+'px"><img src="'+d+'" /></div>',i=""}var d,e=this,f="",g="",h='<div class="lg-thumb-outer"><div class="lg-thumb group"></div></div>';switch(this.core.s.vimeoThumbSize){case"thumbnail_large":g="640";break;case"thumbnail_medium":g="200x150";break;case"thumbnail_small":g="100x75"}if(e.core.$outer.addClass("lg-has-thumb"),e.core.$outer.find(".lg").append(h),e.$thumbOuter=e.core.$outer.find(".lg-thumb-outer"),e.thumbOuterWidth=e.$thumbOuter.width(),e.core.s.animateThumb&&e.core.$outer.find(".lg-thumb").css({width:e.thumbTotalWidth+"px",position:"relative"}),this.core.s.animateThumb&&e.$thumbOuter.css("height",e.core.s.thumbContHeight+"px"),e.core.s.dynamic)for(var i=0;i<e.core.s.dynamicEl.length;i++)c(e.core.s.dynamicEl[i].src,e.core.s.dynamicEl[i].thumb,i);else e.core.$items.each(function(b){e.core.s.exThumbImage?c(a(this).attr("href")||a(this).attr("data-src"),a(this).attr(e.core.s.exThumbImage),b):c(a(this).attr("href")||a(this).attr("data-src"),a(this).find("img").attr("src"),b)});e.core.$outer.find(".lg-thumb").html(f),d=e.core.$outer.find(".lg-thumb-item"),d.each(function(){var b=a(this),c=b.attr("data-vimeo-id");c&&a.getJSON("http://www.vimeo.com/api/v2/video/"+c+".json?callback=?",{format:"json"},function(a){b.find("img").attr("src",a[0][e.core.s.vimeoThumbSize])})}),d.eq(e.core.index).addClass("active"),e.core.$el.on("onBeforeSlide.lg.tm",function(){d.removeClass("active"),d.eq(e.core.index).addClass("active")}),d.on("click.lg touchend.lg",function(){var b=a(this);setTimeout(function(){(e.thumbClickable&&!e.core.lgBusy||!e.core.doCss())&&(e.core.index=b.index(),e.core.slide(e.core.index,!1,!0))},50)}),e.core.$el.on("onBeforeSlide.lg.tm",function(){e.animateThumb(e.core.index)}),a(b).on("resize.lg.thumb orientationchange.lg.thumb",function(){setTimeout(function(){e.animateThumb(e.core.index),e.thumbOuterWidth=e.$thumbOuter.width()},200)})},f.prototype.setTranslate=function(a){this.core.$outer.find(".lg-thumb").css({transform:"translate3d(-"+a+"px, 0px, 0px)"})},f.prototype.animateThumb=function(a){var b=this.core.$outer.find(".lg-thumb");if(this.core.s.animateThumb){var c;switch(this.core.s.currentPagerPosition){case"left":c=0;break;case"middle":c=this.thumbOuterWidth/2-this.core.s.thumbWidth/2;break;case"right":c=this.thumbOuterWidth-this.core.s.thumbWidth}this.left=(this.core.s.thumbWidth+this.core.s.thumbMargin)*a-1-c,this.left>this.thumbTotalWidth-this.thumbOuterWidth&&(this.left=this.thumbTotalWidth-this.thumbOuterWidth),this.left<0&&(this.left=0),this.core.lGalleryOn?(b.hasClass("on")||this.core.$outer.find(".lg-thumb").css("transition-duration",this.core.s.speed+"ms"),this.core.doCss()||b.animate({left:-this.left+"px"},this.core.s.speed)):this.core.doCss()||b.css("left",-this.left+"px"),this.setTranslate(this.left)}},f.prototype.enableThumbDrag=function(){var c=this,d=0,e=0,f=!1,g=!1,h=0;c.$thumbOuter.addClass("lg-grab"),c.core.$outer.find(".lg-thumb").on("mousedown.lg.thumb",function(a){c.thumbTotalWidth>c.thumbOuterWidth&&(a.preventDefault(),d=a.pageX,f=!0,c.core.$outer.scrollLeft+=1,c.core.$outer.scrollLeft-=1,c.thumbClickable=!1,c.$thumbOuter.removeClass("lg-grab").addClass("lg-grabbing"))}),a(b).on("mousemove.lg.thumb",function(a){f&&(h=c.left,g=!0,e=a.pageX,c.$thumbOuter.addClass("lg-dragging"),h-=e-d,h>c.thumbTotalWidth-c.thumbOuterWidth&&(h=c.thumbTotalWidth-c.thumbOuterWidth),0>h&&(h=0),c.setTranslate(h))}),a(b).on("mouseup.lg.thumb",function(){g?(g=!1,c.$thumbOuter.removeClass("lg-dragging"),c.left=h,Math.abs(e-d)<c.core.s.swipeThreshold&&(c.thumbClickable=!0)):c.thumbClickable=!0,f&&(f=!1,c.$thumbOuter.removeClass("lg-grabbing").addClass("lg-grab"))})},f.prototype.enableThumbSwipe=function(){var a=this,b=0,c=0,d=!1,e=0;a.core.$outer.find(".lg-thumb").on("touchstart.lg",function(c){a.thumbTotalWidth>a.thumbOuterWidth&&(c.preventDefault(),b=c.originalEvent.targetTouches[0].pageX,a.thumbClickable=!1)}),a.core.$outer.find(".lg-thumb").on("touchmove.lg",function(f){a.thumbTotalWidth>a.thumbOuterWidth&&(f.preventDefault(),c=f.originalEvent.targetTouches[0].pageX,d=!0,a.$thumbOuter.addClass("lg-dragging"),e=a.left,e-=c-b,e>a.thumbTotalWidth-a.thumbOuterWidth&&(e=a.thumbTotalWidth-a.thumbOuterWidth),0>e&&(e=0),a.setTranslate(e))}),a.core.$outer.find(".lg-thumb").on("touchend.lg",function(){a.thumbTotalWidth>a.thumbOuterWidth&&d?(d=!1,a.$thumbOuter.removeClass("lg-dragging"),Math.abs(c-b)<a.core.s.swipeThreshold&&(a.thumbClickable=!0),a.left=e):a.thumbClickable=!0})},f.prototype.toogle=function(){var a=this;a.core.s.toogleThumb?(a.core.$outer.addClass("lg-can-toggle"),a.$thumbOuter.append('<span class="lg-toogle-thumb lg-icon"></span>'),a.core.$outer.find(".lg-toogle-thumb").on("click.lg",function(){a.core.$outer.toggleClass("lg-thumb-open")})):a.core.s.animateThumb&&a.core.$outer.addClass("lg-cant-toggle")},f.prototype.thumbkeyPress=function(){var c=this;a(b).on("keydown.lg.thumb",function(a){38===a.keyCode?(a.preventDefault(),c.core.$outer.addClass("lg-thumb-open")):40===a.keyCode&&(a.preventDefault(),c.core.$outer.removeClass("lg-thumb-open"))})},f.prototype.destroy=function(){this.core.s.thumbnail&&this.core.$items.length>1&&(a(b).off("resize.lg.thumb orientationchange.lg.thumb keydown.lg.thumb"),this.$thumbOuter.remove(),this.core.$outer.removeClass("lg-has-thumb"))},a.fn.lightGallery.modules.Thumbnail=f}(jQuery,window,document),function(a,b,c,d){"use strict";var e={videoMaxWidth:"855px",youtubePlayerParams:!1,vimeoPlayerParams:!1,dailymotionPlayerParams:!1,videojs:!1},f=function(b){return this.core=a(b).data("lightGallery"),this.$el=a(b),this.core.s=a.extend({},e,this.core.s),this.videoLoaded=!1,this.init(),this};f.prototype.init=function(){var b=this;b.core.$el.on("hasVideo.lg.tm",function(a,c,d,e){if(b.core.$slide.eq(c).find(".lg-video").append(b.loadVideo(d,"lg-object",!0,c,e)),e)if(b.core.s.videojs)try{videojs(b.core.$slide.eq(c).find(".lg-html5").get(0),{},function(){b.videoLoaded||this.play()})}catch(f){console.error("Make sure you have included videojs")}else b.core.$slide.eq(c).find(".lg-html5").get(0).play()}),b.core.$el.on("onAferAppendSlide.lg.tm",function(a,c){b.core.$slide.eq(c).find(".lg-video-cont").css("max-width",b.core.s.videoMaxWidth),b.videoLoaded=!0});var c=function(a){if(a.find(".lg-object").hasClass("lg-has-poster"))if(a.hasClass("lg-has-video")){var c=a.find(".lg-youtube").get(0),d=a.find(".lg-vimeo").get(0),e=a.find(".lg-dailymotion").get(0),f=a.find(".lg-html5").get(0);if(c)c.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}',"*");else if(d)try{$f(d).api("play")}catch(g){console.error("Make sure you have included froogaloop2 js")}else if(e)e.contentWindow.postMessage("play","*");else if(f)if(b.core.s.videojs)try{videojs(f).play()}catch(g){console.error("Make sure you have included videojs")}else f.play();a.addClass("lg-video-palying")}else{a.addClass("lg-video-palying lg-has-video");var h,i,j=function(c,d){if(a.find(".lg-video").append(b.loadVideo(c,"",!1,b.core.index,d)),d)if(b.core.s.videojs)try{videojs(b.core.$slide.eq(b.core.index).find(".lg-html5").get(0),{},function(){this.play()})}catch(e){console.error("Make sure you have included videojs")}else b.core.$slide.eq(b.core.index).find(".lg-html5").get(0).play()};b.core.s.dynamic?(h=b.core.s.dynamicEl[b.core.index].src,i=b.core.s.dynamicEl[b.core.index].html,j(h,i)):(h=b.core.$items.eq(b.core.index).attr("data-src"),i=b.core.$items.eq(b.core.index).attr("data-html"),j(h,i));var k=a.find(".lg-object");a.find(".lg-video").append(k),a.find(".lg-video-object").hasClass("lg-html5")||(a.removeClass("lg-complete"),a.find(".lg-video-object").on("load.lg error.lg",function(){a.addClass("lg-complete")}))}};b.core.doCss()?b.core.$el.on("onSlideClick.lg.tm",function(){var a=b.core.$slide.eq(b.core.index);c(a)}):b.core.$slide.on("click.lg",function(){c(a(this))}),b.core.$el.on("onBeforeSlide.lg.tm",function(a,c){var d=b.core.$slide.eq(c),e=d.find(".lg-youtube").get(0),f=d.find(".lg-vimeo").get(0),g=d.find(".lg-dailymotion").get(0),h=d.find(".lg-html5").get(0);
if(e)e.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}',"*");else if(f)try{$f(f).api("pause")}catch(i){console.error("Make sure you have included froogaloop2 js")}else if(g)g.contentWindow.postMessage("pause","*");else if(h)if(b.core.s.videojs)try{videojs(h).pause()}catch(i){console.error("Make sure you have included videojs")}else h.pause()}),b.core.$el.on("onAfterSlide.lg.tm",function(a,c){b.core.$slide.eq(c).removeClass("lg-video-palying")})},f.prototype.loadVideo=function(b,c,d,e,f){var g="",h=1,i="",j=this.core.isVideo(b,e)||{};if(d&&(h=this.videoLoaded?0:1),j.youtube)i="?wmode=opaque&autoplay="+h+"&enablejsapi=1",this.core.s.youtubePlayerParams&&(i=i+"&"+a.param(this.core.s.youtubePlayerParams)),g='<iframe class="lg-video-object lg-youtube '+c+'" width="560" height="315" src="//www.youtube.com/embed/'+j.youtube[1]+i+'" frameborder="0" allowfullscreen></iframe>';else if(j.vimeo)i="?autoplay="+h+"&api=1",this.core.s.vimeoPlayerParams&&(i=i+"&"+a.param(this.core.s.vimeoPlayerParams)),g='<iframe class="lg-video-object lg-vimeo '+c+'" width="560" height="315"  src="http://player.vimeo.com/video/'+j.vimeo[1]+i+'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';else if(j.dailymotion)i="?wmode=opaque&autoplay="+h+"&api=postMessage",this.core.s.dailymotionPlayerParams&&(i=i+"&"+a.param(this.core.s.dailymotionPlayerParams)),g='<iframe class="lg-video-object lg-dailymotion '+c+'" width="560" height="315" src="//www.dailymotion.com/embed/video/'+j.dailymotion[1]+i+'" frameborder="0" allowfullscreen></iframe>';else if(j.html5){var k=f.substring(0,1);("."===k||"#"===k)&&(f=a(f).html()),g=f}return g},f.prototype.destroy=function(){this.videoLoaded=!1},a.fn.lightGallery.modules.video=f}(jQuery,window,document),function(a,b,c,d){"use strict";var e={scale:1,zoom:!0,enableZoomAfter:300},f=function(c){return this.core=a(c).data("lightGallery"),this.core.s=a.extend({},e,this.core.s),this.core.s.zoom&&this.core.doCss()&&(this.init(),this.zoomabletimeout=!1,this.pageX=a(b).width()/2,this.pageY=a(b).height()/2+a(b).scrollTop()),this};f.prototype.init=function(){var c=this,d='<span id="lg-zoom-in" class="lg-icon"></span><span id="lg-zoom-out" class="lg-icon"></span>';this.core.$outer.find(".lg-toolbar").append(d),c.core.$el.on("onSlideItemLoad.lg.tm.zoom",function(b,d,e){var f=c.core.s.enableZoomAfter+e;a("body").hasClass("lg-from-hash")&&e?f=0:a("body").removeClass("lg-from-hash"),c.zoomabletimeout=setTimeout(function(){c.core.$slide.eq(d).addClass("lg-zoomable")},f+30)});var e=1,f=function(d){var e,f,g=c.core.$outer.find(".lg-current .lg-image"),h=(a(b).width()-g.width())/2,i=(a(b).height()-g.height())/2+a(b).scrollTop();e=c.pageX-h,f=c.pageY-i;var j=(d-1)*e,k=(d-1)*f;g.css("transform","scale3d("+d+", "+d+", 1)").attr("data-scale",d),g.parent().css("transform","translate3d(-"+j+"px, -"+k+"px, 0)").attr("data-x",j).attr("data-y",k)},g=function(){e>1?c.core.$outer.addClass("lg-zoomed"):c.resetZoom(),1>e&&(e=1),f(e)};c.core.$el.on("onAferAppendSlide.lg.tm.zoom",function(a,b){var d=c.core.$slide.eq(b).find(".lg-image");d.dblclick(function(a){var f,h=d.width(),i=c.core.$items.eq(b).attr("data-width")||d[0].naturalWidth||h;c.core.$outer.hasClass("lg-zoomed")?e=1:i>h&&(f=i/h,e=f||2),c.pageX=a.pageX,c.pageY=a.pageY,g(),setTimeout(function(){c.core.$outer.removeClass("lg-grabbing").addClass("lg-grab")},10)})}),a(b).on("resize.lg.zoom scroll.lg.zoom orientationchange.lg.zoom",function(){c.pageX=a(b).width()/2,c.pageY=a(b).height()/2+a(b).scrollTop(),f(e)}),a("#lg-zoom-out").on("click.lg",function(){c.core.$outer.find(".lg-current .lg-image").length&&(e-=c.core.s.scale,g())}),a("#lg-zoom-in").on("click.lg",function(){c.core.$outer.find(".lg-current .lg-image").length&&(e+=c.core.s.scale,g())}),c.core.$el.on("onBeforeSlide.lg.tm",function(){c.resetZoom()}),c.core.isTouch||c.zoomDrag(),c.core.isTouch&&c.zoomSwipe()},f.prototype.resetZoom=function(){this.core.$outer.removeClass("lg-zoomed"),this.core.$slide.find(".lg-img-wrap").removeAttr("style data-x data-y"),this.core.$slide.find(".lg-image").removeAttr("style data-scale"),this.pageX=a(b).width()/2,this.pageY=a(b).height()/2+a(b).scrollTop()},f.prototype.zoomSwipe=function(){var a=this,b={},c={},d=!1,e=!1,f=!1;a.core.$slide.on("touchstart.lg",function(c){if(a.core.$outer.hasClass("lg-zoomed")){var d=a.core.$slide.eq(a.core.index).find(".lg-object");f=d.outerHeight()*d.attr("data-scale")>a.core.$outer.find(".lg").height(),e=d.outerWidth()*d.attr("data-scale")>a.core.$outer.find(".lg").width(),(e||f)&&(c.preventDefault(),b={x:c.originalEvent.targetTouches[0].pageX,y:c.originalEvent.targetTouches[0].pageY})}}),a.core.$slide.on("touchmove.lg",function(g){if(a.core.$outer.hasClass("lg-zoomed")){var h,i,j=a.core.$slide.eq(a.core.index).find(".lg-img-wrap");g.preventDefault(),d=!0,c=g.originalEvent.targetTouches[0].pageX,c={x:g.originalEvent.targetTouches[0].pageX,y:g.originalEvent.targetTouches[0].pageY},a.core.$outer.addClass("lg-zoom-dragging"),i=f?-Math.abs(j.attr("data-y"))+(c.y-b.y):-Math.abs(j.attr("data-y")),h=e?-Math.abs(j.attr("data-x"))+(c.x-b.x):-Math.abs(j.attr("data-x")),j.css("transform","translate3d("+h+"px, "+i+"px, 0)")}}),a.core.$slide.on("touchend.lg",function(){a.core.$outer.hasClass("lg-zoomed")&&d&&(d=!1,a.core.$outer.removeClass("lg-zoom-dragging"),a.touchendZoom(b,c,e,f))})},f.prototype.zoomDrag=function(){var c=this,d={},e={},f=!1,g=!1,h=!1,i=!1;c.core.$slide.on("mousedown.lg.zoom",function(b){var e=c.core.$slide.eq(c.core.index).find(".lg-object");i=e.outerHeight()*e.attr("data-scale")>c.core.$outer.find(".lg").height(),h=e.outerWidth()*e.attr("data-scale")>c.core.$outer.find(".lg").width(),c.core.$outer.hasClass("lg-zoomed")&&a(b.target).hasClass("lg-object")&&(h||i)&&(b.preventDefault(),d={x:b.pageX,y:b.pageY},f=!0,c.core.$outer.scrollLeft+=1,c.core.$outer.scrollLeft-=1,c.core.$outer.removeClass("lg-grab").addClass("lg-grabbing"))}),a(b).on("mousemove.lg.zoom",function(a){if(f){var b,j,k=c.core.$slide.eq(c.core.index).find(".lg-img-wrap");g=!0,e={x:a.pageX,y:a.pageY},c.core.$outer.addClass("lg-zoom-dragging"),j=i?-Math.abs(k.attr("data-y"))+(e.y-d.y):-Math.abs(k.attr("data-y")),b=h?-Math.abs(k.attr("data-x"))+(e.x-d.x):-Math.abs(k.attr("data-x")),k.css("transform","translate3d("+b+"px, "+j+"px, 0)")}}),a(b).on("mouseup.lg.zoom",function(a){f&&(f=!1,c.core.$outer.removeClass("lg-zoom-dragging"),!g||d.x===e.x&&d.y===e.y||(e={x:a.pageX,y:a.pageY},c.touchendZoom(d,e,h,i)),g=!1),c.core.$outer.removeClass("lg-grabbing").addClass("lg-grab")})},f.prototype.touchendZoom=function(a,b,c,d){var e=this,f=e.core.$slide.eq(e.core.index).find(".lg-img-wrap"),g=e.core.$slide.eq(e.core.index).find(".lg-object"),h=-Math.abs(f.attr("data-x"))+(b.x-a.x),i=-Math.abs(f.attr("data-y"))+(b.y-a.y),j=(e.core.$outer.find(".lg").height()-g.outerHeight())/2,k=Math.abs(g.outerHeight()*Math.abs(g.attr("data-scale"))-e.core.$outer.find(".lg").height()+j),l=(e.core.$outer.find(".lg").width()-g.outerWidth())/2,m=Math.abs(g.outerWidth()*Math.abs(g.attr("data-scale"))-e.core.$outer.find(".lg").width()+l);d&&(-k>=i?i=-k:i>=-j&&(i=-j)),c&&(-m>=h?h=-m:h>=-l&&(h=-l)),d?f.attr("data-y",Math.abs(i)):i=-Math.abs(f.attr("data-y")),c?f.attr("data-x",Math.abs(h)):h=-Math.abs(f.attr("data-x")),f.css("transform","translate3d("+h+"px, "+i+"px, 0)")},f.prototype.destroy=function(){var c=this;c.core.$el.off(".lg.zoom"),a(b).off(".lg.zoom"),c.core.$slide.off(".lg.zoom"),c.core.$el.off(".lg.tm.zoom"),c.resetZoom(),clearTimeout(c.zoomabletimeout),c.zoomabletimeout=!1},a.fn.lightGallery.modules.zoom=f}(jQuery,window,document),function(a,b,c,d){"use strict";var e={hash:!0},f=function(c){return this.core=a(c).data("lightGallery"),this.core.s=a.extend({},e,this.core.s),this.core.s.hash&&(this.oldHash=b.location.hash,this.init()),this};f.prototype.init=function(){var c,d=this;d.core.$el.on("onAfterSlide.lg.tm",function(a,c,e){b.location.hash="lg="+d.core.s.galleryId+"&slide="+e}),a(b).on("hashchange",function(){c=b.location.hash;var a=parseInt(c.split("&slide=")[1],10);c.indexOf("lg="+d.core.s.galleryId)>-1?d.core.slide(a):d.core.lGalleryOn&&d.core.destroy()})},f.prototype.destroy=function(){this.oldHash&&this.oldHash.indexOf("lg="+this.core.s.galleryId)<0?b.location.hash=this.oldHash:history.pushState?history.pushState("",c.title,b.location.pathname+b.location.search):b.location.hash=""},a.fn.lightGallery.modules.hash=f}(jQuery,window,document);
/*
 * jQuery Iframe Transport Plugin 1.5
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2011, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint unparam: true, nomen: true */
/*global define, window, document */

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define(['jquery'], factory);
    } else {
        // Browser globals:
        factory(window.jQuery);
    }
}(function ($) {
    'use strict';

    // Helper variable to create unique names for the transport iframes:
    var counter = 0;

    // The iframe transport accepts three additional options:
    // options.fileInput: a jQuery collection of file input fields
    // options.paramName: the parameter name for the file form data,
    //  overrides the name property of the file input field(s),
    //  can be a string or an array of strings.
    // options.formData: an array of objects with name and value properties,
    //  equivalent to the return data of .serializeArray(), e.g.:
    //  [{name: 'a', value: 1}, {name: 'b', value: 2}]
    $.ajaxTransport('iframe', function (options) {
        if (options.async && (options.type === 'POST' || options.type === 'GET')) {
            var form,
                iframe;
            return {
                send: function (_, completeCallback) {
                    form = $('<form style="display:none;"></form>');
                    form.attr('accept-charset', options.formAcceptCharset);
                    // javascript:false as initial iframe src
                    // prevents warning popups on HTTPS in IE6.
                    // IE versions below IE8 cannot set the name property of
                    // elements that have already been added to the DOM,
                    // so we set the name along with the iframe HTML markup:
                    iframe = $(
                        '<iframe src="javascript:false;" name="iframe-transport-' +
                            (counter += 1) + '"></iframe>'
                    ).bind('load', function () {
                        var fileInputClones,
                            paramNames = $.isArray(options.paramName) ?
                                    options.paramName : [options.paramName];
                        iframe
                            .unbind('load')
                            .bind('load', function () {
                                var response;
                                // Wrap in a try/catch block to catch exceptions thrown
                                // when trying to access cross-domain iframe contents:
                                try {
                                    response = iframe.contents();
                                    // Google Chrome and Firefox do not throw an
                                    // exception when calling iframe.contents() on
                                    // cross-domain requests, so we unify the response:
                                    if (!response.length || !response[0].firstChild) {
                                        throw new Error();
                                    }
                                } catch (e) {
                                    response = undefined;
                                }
                                // The complete callback returns the
                                // iframe content document as response object:
                                completeCallback(
                                    200,
                                    'success',
                                    {'iframe': response}
                                );
                                // Fix for IE endless progress bar activity bug
                                // (happens on form submits to iframe targets):
                                $('<iframe src="javascript:false;"></iframe>')
                                    .appendTo(form);
                                form.remove();
                            });
                        form
                            .prop('target', iframe.prop('name'))
                            .prop('action', options.url)
                            .prop('method', options.type);
                        if (options.formData) {
                            $.each(options.formData, function (index, field) {
                                $('<input type="hidden"/>')
                                    .prop('name', field.name)
                                    .val(field.value)
                                    .appendTo(form);
                            });
                        }
                        if (options.fileInput && options.fileInput.length &&
                                options.type === 'POST') {
                            fileInputClones = options.fileInput.clone();
                            // Insert a clone for each file input field:
                            options.fileInput.after(function (index) {
                                return fileInputClones[index];
                            });
                            if (options.paramName) {
                                options.fileInput.each(function (index) {
                                    $(this).prop(
                                        'name',
                                        paramNames[index] || options.paramName
                                    );
                                });
                            }
                            // Appending the file input fields to the hidden form
                            // removes them from their original location:
                            form
                                .append(options.fileInput)
                                .prop('enctype', 'multipart/form-data')
                                // enctype must be set as encoding for IE:
                                .prop('encoding', 'multipart/form-data');
                        }
                        form.submit();
                        // Insert the file input fields at their original location
                        // by replacing the clones with the originals:
                        if (fileInputClones && fileInputClones.length) {
                            options.fileInput.each(function (index, input) {
                                var clone = $(fileInputClones[index]);
                                $(input).prop('name', clone.prop('name'));
                                clone.replaceWith(input);
                            });
                        }
                    });
                    form.append(iframe).appendTo(document.body);
                },
                abort: function () {
                    if (iframe) {
                        // javascript:false as iframe src aborts the request
                        // and prevents warning popups on HTTPS in IE6.
                        // concat is used to avoid the "Script URL" JSLint error:
                        iframe
                            .unbind('load')
                            .prop('src', 'javascript'.concat(':false;'));
                    }
                    if (form) {
                        form.remove();
                    }
                }
            };
        }
    });

    // The iframe transport returns the iframe content document as response.
    // The following adds converters from iframe to text, json, html, and script:
    $.ajaxSetup({
        converters: {
            'iframe text': function (iframe) {
                return $(iframe[0].body).text();
            },
            'iframe json': function (iframe) {
                return $.parseJSON($(iframe[0].body).text());
            },
            'iframe html': function (iframe) {
                return $(iframe[0].body).html();
            },
            'iframe script': function (iframe) {
                return $.globalEval($(iframe[0].body).text());
            }
        }
    });

}));

/*
 * jQuery File Upload Plugin 5.19.3
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global define, window, document, Blob, FormData, location */

(function (factory) {
    'use strict';
    if (typeof define === 'function' && define.amd) {
        // Register as an anonymous AMD module:
        define([
            'jquery',
            'jquery.ui.widget'
        ], factory);
    } else {
        // Browser globals:
        factory(window.jQuery);
    }
}(function ($) {
    'use strict';

    // The FileReader API is not actually used, but works as feature detection,
    // as e.g. Safari supports XHR file uploads via the FormData API,
    // but not non-multipart XHR file uploads:
    $.support.xhrFileUpload = !!(window.XMLHttpRequestUpload && window.FileReader);
    $.support.xhrFormDataFileUpload = !!window.FormData;

    // The fileupload widget listens for change events on file input fields defined
    // via fileInput setting and paste or drop events of the given dropZone.
    // In addition to the default jQuery Widget methods, the fileupload widget
    // exposes the "add" and "send" methods, to add or directly send files using
    // the fileupload API.
    // By default, files added via file input selection, paste, drag & drop or
    // "add" method are uploaded immediately, but it is possible to override
    // the "add" callback option to queue file uploads.
    $.widget('blueimp.fileupload', {

        options: {
            // The drop target element(s), by the default the complete document.
            // Set to null to disable drag & drop support:
            dropZone: $(document),
            // The paste target element(s), by the default the complete document.
            // Set to null to disable paste support:
            pasteZone: $(document),
            // The file input field(s), that are listened to for change events.
            // If undefined, it is set to the file input fields inside
            // of the widget element on plugin initialization.
            // Set to null to disable the change listener.
            fileInput: undefined,
            // By default, the file input field is replaced with a clone after
            // each input field change event. This is required for iframe transport
            // queues and allows change events to be fired for the same file
            // selection, but can be disabled by setting the following option to false:
            replaceFileInput: true,
            // The parameter name for the file form data (the request argument name).
            // If undefined or empty, the name property of the file input field is
            // used, or "files[]" if the file input name property is also empty,
            // can be a string or an array of strings:
            paramName: undefined,
            // By default, each file of a selection is uploaded using an individual
            // request for XHR type uploads. Set to false to upload file
            // selections in one request each:
            singleFileUploads: true,
            // To limit the number of files uploaded with one XHR request,
            // set the following option to an integer greater than 0:
            limitMultiFileUploads: undefined,
            // Set the following option to true to issue all file upload requests
            // in a sequential order:
            sequentialUploads: false,
            // To limit the number of concurrent uploads,
            // set the following option to an integer greater than 0:
            limitConcurrentUploads: undefined,
            // Set the following option to true to force iframe transport uploads:
            forceIframeTransport: false,
            // Set the following option to the location of a redirect url on the
            // origin server, for cross-domain iframe transport uploads:
            redirect: undefined,
            // The parameter name for the redirect url, sent as part of the form
            // data and set to 'redirect' if this option is empty:
            redirectParamName: undefined,
            // Set the following option to the location of a postMessage window,
            // to enable postMessage transport uploads:
            postMessage: undefined,
            // By default, XHR file uploads are sent as multipart/form-data.
            // The iframe transport is always using multipart/form-data.
            // Set to false to enable non-multipart XHR uploads:
            multipart: true,
            // To upload large files in smaller chunks, set the following option
            // to a preferred maximum chunk size. If set to 0, null or undefined,
            // or the browser does not support the required Blob API, files will
            // be uploaded as a whole.
            maxChunkSize: undefined,
            // When a non-multipart upload or a chunked multipart upload has been
            // aborted, this option can be used to resume the upload by setting
            // it to the size of the already uploaded bytes. This option is most
            // useful when modifying the options object inside of the "add" or
            // "send" callbacks, as the options are cloned for each file upload.
            uploadedBytes: undefined,
            // By default, failed (abort or error) file uploads are removed from the
            // global progress calculation. Set the following option to false to
            // prevent recalculating the global progress data:
            recalculateProgress: true,
            // Interval in milliseconds to calculate and trigger progress events:
            progressInterval: 100,
            // Interval in milliseconds to calculate progress bitrate:
            bitrateInterval: 500,

            // Additional form data to be sent along with the file uploads can be set
            // using this option, which accepts an array of objects with name and
            // value properties, a function returning such an array, a FormData
            // object (for XHR file uploads), or a simple object.
            // The form of the first fileInput is given as parameter to the function:
            formData: function (form) {
                return form.serializeArray();
            },

            // The add callback is invoked as soon as files are added to the fileupload
            // widget (via file input selection, drag & drop, paste or add API call).
            // If the singleFileUploads option is enabled, this callback will be
            // called once for each file in the selection for XHR file uplaods, else
            // once for each file selection.
            // The upload starts when the submit method is invoked on the data parameter.
            // The data object contains a files property holding the added files
            // and allows to override plugin options as well as define ajax settings.
            // Listeners for this callback can also be bound the following way:
            // .bind('fileuploadadd', func);
            // data.submit() returns a Promise object and allows to attach additional
            // handlers using jQuery's Deferred callbacks:
            // data.submit().done(func).fail(func).always(func);
            add: function (e, data) {
                data.submit();
            },

            // Other callbacks:
            // Callback for the submit event of each file upload:
            // submit: function (e, data) {}, // .bind('fileuploadsubmit', func);
            // Callback for the start of each file upload request:
            // send: function (e, data) {}, // .bind('fileuploadsend', func);
            // Callback for successful uploads:
            // done: function (e, data) {}, // .bind('fileuploaddone', func);
            // Callback for failed (abort or error) uploads:
            // fail: function (e, data) {}, // .bind('fileuploadfail', func);
            // Callback for completed (success, abort or error) requests:
            // always: function (e, data) {}, // .bind('fileuploadalways', func);
            // Callback for upload progress events:
            // progress: function (e, data) {}, // .bind('fileuploadprogress', func);
            // Callback for global upload progress events:
            // progressall: function (e, data) {}, // .bind('fileuploadprogressall', func);
            // Callback for uploads start, equivalent to the global ajaxStart event:
            // start: function (e) {}, // .bind('fileuploadstart', func);
            // Callback for uploads stop, equivalent to the global ajaxStop event:
            // stop: function (e) {}, // .bind('fileuploadstop', func);
            // Callback for change events of the fileInput(s):
            // change: function (e, data) {}, // .bind('fileuploadchange', func);
            // Callback for paste events to the pasteZone(s):
            // paste: function (e, data) {}, // .bind('fileuploadpaste', func);
            // Callback for drop events of the dropZone(s):
            // drop: function (e, data) {}, // .bind('fileuploaddrop', func);
            // Callback for dragover events of the dropZone(s):
            // dragover: function (e) {}, // .bind('fileuploaddragover', func);

            // The plugin options are used as settings object for the ajax calls.
            // The following are jQuery ajax settings required for the file uploads:
            processData: false,
            contentType: false,
            cache: false
        },

        // A list of options that require a refresh after assigning a new value:
        _refreshOptionsList: [
            'fileInput',
            'dropZone',
            'pasteZone',
            'multipart',
            'forceIframeTransport'
        ],

        _BitrateTimer: function () {
            this.timestamp = +(new Date());
            this.loaded = 0;
            this.bitrate = 0;
            this.getBitrate = function (now, loaded, interval) {
                var timeDiff = now - this.timestamp;
                if (!this.bitrate || !interval || timeDiff > interval) {
                    this.bitrate = (loaded - this.loaded) * (1000 / timeDiff) * 8;
                    this.loaded = loaded;
                    this.timestamp = now;
                }
                return this.bitrate;
            };
        },

        _isXHRUpload: function (options) {
            return !options.forceIframeTransport &&
                ((!options.multipart && $.support.xhrFileUpload) ||
                $.support.xhrFormDataFileUpload);
        },

        _getFormData: function (options) {
            var formData;
            if (typeof options.formData === 'function') {
                return options.formData(options.form);
            }
			if ($.isArray(options.formData)) {
                return options.formData;
            }
			if (options.formData) {
                formData = [];
                $.each(options.formData, function (name, value) {
                    formData.push({name: name, value: value});
                });
                return formData;
            }
            return [];
        },

        _getTotal: function (files) {
            var total = 0;
            $.each(files, function (index, file) {
                total += file.size || 1;
            });
            return total;
        },

        _onProgress: function (e, data) {
            if (e.lengthComputable) {
                var now = +(new Date()),
                    total,
                    loaded;
                if (data._time && data.progressInterval &&
                        (now - data._time < data.progressInterval) &&
                        e.loaded !== e.total) {
                    return;
                }
                data._time = now;
                total = data.total || this._getTotal(data.files);
                loaded = parseInt(
                    e.loaded / e.total * (data.chunkSize || total),
                    10
                ) + (data.uploadedBytes || 0);
                this._loaded += loaded - (data.loaded || data.uploadedBytes || 0);
                data.lengthComputable = true;
                data.loaded = loaded;
                data.total = total;
                data.bitrate = data._bitrateTimer.getBitrate(
                    now,
                    loaded,
                    data.bitrateInterval
                );
                // Trigger a custom progress event with a total data property set
                // to the file size(s) of the current upload and a loaded data
                // property calculated accordingly:
                this._trigger('progress', e, data);
                // Trigger a global progress event for all current file uploads,
                // including ajax calls queued for sequential file uploads:
                this._trigger('progressall', e, {
                    lengthComputable: true,
                    loaded: this._loaded,
                    total: this._total,
                    bitrate: this._bitrateTimer.getBitrate(
                        now,
                        this._loaded,
                        data.bitrateInterval
                    )
                });
            }
        },

        _initProgressListener: function (options) {
            var that = this,
                xhr = options.xhr ? options.xhr() : $.ajaxSettings.xhr();
            // Accesss to the native XHR object is required to add event listeners
            // for the upload progress event:
            if (xhr.upload) {
                $(xhr.upload).bind('progress', function (e) {
                    var oe = e.originalEvent;
                    // Make sure the progress event properties get copied over:
                    e.lengthComputable = oe.lengthComputable;
                    e.loaded = oe.loaded;
                    e.total = oe.total;
                    that._onProgress(e, options);
                });
                options.xhr = function () {
                    return xhr;
                };
            }
        },

        _initXHRData: function (options) {
            var formData,
                file = options.files[0],
                // Ignore non-multipart setting if not supported:
                multipart = options.multipart || !$.support.xhrFileUpload,
                paramName = options.paramName[0];
            options.headers = options.headers || {};
            if (options.contentRange) {
                options.headers['Content-Range'] = options.contentRange;
            }
            if (!multipart) {
                options.headers['Content-Disposition'] = 'attachment; filename="' +
                    encodeURI(file.name) + '"';
                options.contentType = file.type;
                options.data = options.blob || file;
            } else if ($.support.xhrFormDataFileUpload) {
                if (options.postMessage) {
                    // window.postMessage does not allow sending FormData
                    // objects, so we just add the File/Blob objects to
                    // the formData array and let the postMessage window
                    // create the FormData object out of this array:
                    formData = this._getFormData(options);
                    if (options.blob) {
                        formData.push({
                            name: paramName,
                            value: options.blob
                        });
                    } else {
                        $.each(options.files, function (index, file) {
                            formData.push({
                                name: options.paramName[index] || paramName,
                                value: file
                            });
                        });
                    }
                } else {
                    if (options.formData instanceof FormData) {
                        formData = options.formData;
                    } else {
                        formData = new FormData();
                        $.each(this._getFormData(options), function (index, field) {
                            formData.append(field.name, field.value);
                        });
                    }
                    if (options.blob) {
                        options.headers['Content-Disposition'] = 'attachment; filename="' +
                            encodeURI(file.name) + '"';
                        options.headers['Content-Description'] = encodeURI(file.type);
                        formData.append(paramName, options.blob, file.name);
                    } else {
                        $.each(options.files, function (index, file) {
                            // File objects are also Blob instances.
                            // This check allows the tests to run with
                            // dummy objects:
                            if (file instanceof Blob) {
                                formData.append(
                                    options.paramName[index] || paramName,
                                    file,
                                    file.name
                                );
                            }
                        });
                    }
                }
                options.data = formData;
            }
            // Blob reference is not needed anymore, free memory:
            options.blob = null;
        },

        _initIframeSettings: function (options) {
            // Setting the dataType to iframe enables the iframe transport:
            options.dataType = 'iframe ' + (options.dataType || '');
            // The iframe transport accepts a serialized array as form data:
            options.formData = this._getFormData(options);
            // Add redirect url to form data on cross-domain uploads:
            if (options.redirect && $('<a></a>').prop('href', options.url)
                    .prop('host') !== location.host) {
                options.formData.push({
                    name: options.redirectParamName || 'redirect',
                    value: options.redirect
                });
            }
        },

        _initDataSettings: function (options) {
            if (this._isXHRUpload(options)) {
                if (!this._chunkedUpload(options, true)) {
                    if (!options.data) {
                        this._initXHRData(options);
                    }
                    this._initProgressListener(options);
                }
                if (options.postMessage) {
                    // Setting the dataType to postmessage enables the
                    // postMessage transport:
                    options.dataType = 'postmessage ' + (options.dataType || '');
                }
            } else {
                this._initIframeSettings(options, 'iframe');
            }
        },

        _getParamName: function (options) {
            var fileInput = $(options.fileInput),
                paramName = options.paramName;
            if (!paramName) {
                paramName = [];
                fileInput.each(function () {
                    var input = $(this),
                        name = input.prop('name') || 'files[]',
                        i = (input.prop('files') || [1]).length;
                    while (i) {
                        paramName.push(name);
                        i -= 1;
                    }
                });
                if (!paramName.length) {
                    paramName = [fileInput.prop('name') || 'files[]'];
                }
            } else if (!$.isArray(paramName)) {
                paramName = [paramName];
            }
            return paramName;
        },

        _initFormSettings: function (options) {
            // Retrieve missing options from the input field and the
            // associated form, if available:
            if (!options.form || !options.form.length) {
                options.form = $(options.fileInput.prop('form'));
                // If the given file input doesn't have an associated form,
                // use the default widget file input's form:
                if (!options.form.length) {
                    options.form = $(this.options.fileInput.prop('form'));
                }
            }
            options.paramName = this._getParamName(options);
            if (!options.url) {
                options.url = options.form.prop('action') || location.href;
            }
            // The HTTP request method must be "POST" or "PUT":
            options.type = (options.type || options.form.prop('method') || '')
                .toUpperCase();
            if (options.type !== 'POST' && options.type !== 'PUT') {
                options.type = 'POST';
            }
            if (!options.formAcceptCharset) {
                options.formAcceptCharset = options.form.attr('accept-charset');
            }
        },

        _getAJAXSettings: function (data) {
            var options = $.extend({}, this.options, data);
            this._initFormSettings(options);
            this._initDataSettings(options);
            return options;
        },

        // Maps jqXHR callbacks to the equivalent
        // methods of the given Promise object:
        _enhancePromise: function (promise) {
            promise.success = promise.done;
            promise.error = promise.fail;
            promise.complete = promise.always;
            return promise;
        },

        // Creates and returns a Promise object enhanced with
        // the jqXHR methods abort, success, error and complete:
        _getXHRPromise: function (resolveOrReject, context, args) {
            var dfd = $.Deferred(),
                promise = dfd.promise();
            context = context || this.options.context || promise;
            if (resolveOrReject === true) {
                dfd.resolveWith(context, args);
            } else if (resolveOrReject === false) {
                dfd.rejectWith(context, args);
            }
            promise.abort = dfd.promise;
            return this._enhancePromise(promise);
        },

        // Parses the Range header from the server response
        // and returns the uploaded bytes:
        _getUploadedBytes: function (jqXHR) {
            var range = jqXHR.getResponseHeader('Range'),
                parts = range && range.split('-'),
                upperBytesPos = parts && parts.length > 1 &&
                    parseInt(parts[1], 10);
            return upperBytesPos && upperBytesPos + 1;
        },

        // Uploads a file in multiple, sequential requests
        // by splitting the file up in multiple blob chunks.
        // If the second parameter is true, only tests if the file
        // should be uploaded in chunks, but does not invoke any
        // upload requests:
        _chunkedUpload: function (options, testOnly) {
            var that = this,
                file = options.files[0],
                fs = file.size,
                ub = options.uploadedBytes = options.uploadedBytes || 0,
                mcs = options.maxChunkSize || fs,
                slice = file.slice || file.webkitSlice || file.mozSlice,
                dfd = $.Deferred(),
                promise = dfd.promise(),
                jqXHR,
                upload;
            if (!(this._isXHRUpload(options) && slice && (ub || mcs < fs)) ||
                    options.data) {
                return false;
            }
            if (testOnly) {
                return true;
            }
            if (ub >= fs) {
                file.error = 'Uploaded bytes exceed file size';
                return this._getXHRPromise(
                    false,
                    options.context,
                    [null, 'error', file.error]
                );
            }
            // The chunk upload method:
            upload = function (i) {
                // Clone the options object for each chunk upload:
                var o = $.extend({}, options);
                o.blob = slice.call(
                    file,
                    ub,
                    ub + mcs
                );
                // Store the current chunk size, as the blob itself
                // will be dereferenced after data processing:
                o.chunkSize = o.blob.size;
                // Expose the chunk bytes position range:
                o.contentRange = 'bytes ' + ub + '-' +
                    (ub + o.chunkSize - 1) + '/' + fs;
                // Process the upload data (the blob and potential form data):
                that._initXHRData(o);
                // Add progress listeners for this chunk upload:
                that._initProgressListener(o);
                jqXHR = ($.ajax(o) || that._getXHRPromise(false, o.context))
                    .done(function (result, textStatus, jqXHR) {
                        ub = that._getUploadedBytes(jqXHR) ||
                            (ub + o.chunkSize);
                        // Create a progress event if upload is done and
                        // no progress event has been invoked for this chunk:
                        if (!o.loaded) {
                            that._onProgress($.Event('progress', {
                                lengthComputable: true,
                                loaded: ub - o.uploadedBytes,
                                total: ub - o.uploadedBytes
                            }), o);
                        }
                        options.uploadedBytes = o.uploadedBytes = ub;
                        if (ub < fs) {
                            // File upload not yet complete,
                            // continue with the next chunk:
                            upload();
                        } else {
                            dfd.resolveWith(
                                o.context,
                                [result, textStatus, jqXHR]
                            );
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        dfd.rejectWith(
                            o.context,
                            [jqXHR, textStatus, errorThrown]
                        );
                    });
            };
            this._enhancePromise(promise);
            promise.abort = function () {
                return jqXHR.abort();
            };
            upload();
            return promise;
        },

        _beforeSend: function (e, data) {
            if (this._active === 0) {
                // the start callback is triggered when an upload starts
                // and no other uploads are currently running,
                // equivalent to the global ajaxStart event:
                this._trigger('start');
                // Set timer for global bitrate progress calculation:
                this._bitrateTimer = new this._BitrateTimer();
            }
            this._active += 1;
            // Initialize the global progress values:
            this._loaded += data.uploadedBytes || 0;
            this._total += this._getTotal(data.files);
        },

        _onDone: function (result, textStatus, jqXHR, options) {
            if (!this._isXHRUpload(options)) {
                // Create a progress event for each iframe load:
                this._onProgress($.Event('progress', {
                    lengthComputable: true,
                    loaded: 1,
                    total: 1
                }), options);
            }
            options.result = result;
            options.textStatus = textStatus;
            options.jqXHR = jqXHR;
            this._trigger('done', null, options);
        },

        _onFail: function (jqXHR, textStatus, errorThrown, options) {
            options.jqXHR = jqXHR;
            options.textStatus = textStatus;
            options.errorThrown = errorThrown;
            this._trigger('fail', null, options);
            if (options.recalculateProgress) {
                // Remove the failed (error or abort) file upload from
                // the global progress calculation:
                this._loaded -= options.loaded || options.uploadedBytes || 0;
                this._total -= options.total || this._getTotal(options.files);
            }
        },

        _onAlways: function (jqXHRorResult, textStatus, jqXHRorError, options) {
            this._active -= 1;
            options.textStatus = textStatus;
            if (jqXHRorError && jqXHRorError.always) {
                options.jqXHR = jqXHRorError;
                options.result = jqXHRorResult;
            } else {
                options.jqXHR = jqXHRorResult;
                options.errorThrown = jqXHRorError;
            }
            this._trigger('always', null, options);
            if (this._active === 0) {
                // The stop callback is triggered when all uploads have
                // been completed, equivalent to the global ajaxStop event:
                this._trigger('stop');
                // Reset the global progress values:
                this._loaded = this._total = 0;
                this._bitrateTimer = null;
            }
        },

        _onSend: function (e, data) {
            var that = this,
                jqXHR,
                aborted,
                slot,
                pipe,
                options = that._getAJAXSettings(data),
                send = function () {
                    that._sending += 1;
                    // Set timer for bitrate progress calculation:
                    options._bitrateTimer = new that._BitrateTimer();
                    jqXHR = jqXHR || (
                        ((aborted || that._trigger('send', e, options) === false) &&
                        that._getXHRPromise(false, options.context, aborted)) ||
                        that._chunkedUpload(options) || $.ajax(options)
                    ).done(function (result, textStatus, jqXHR) {
                        that._onDone(result, textStatus, jqXHR, options);
                    }).fail(function (jqXHR, textStatus, errorThrown) {
                        that._onFail(jqXHR, textStatus, errorThrown, options);
                    }).always(function (jqXHRorResult, textStatus, jqXHRorError) {
                        that._sending -= 1;
                        that._onAlways(
                            jqXHRorResult,
                            textStatus,
                            jqXHRorError,
                            options
                        );
                        if (options.limitConcurrentUploads &&
                                options.limitConcurrentUploads > that._sending) {
                            // Start the next queued upload,
                            // that has not been aborted:
                            var nextSlot = that._slots.shift(),
                                isPending;
                            while (nextSlot) {
                                // jQuery 1.6 doesn't provide .state(),
                                // while jQuery 1.8+ removed .isRejected():
                                isPending = nextSlot.state ?
                                        nextSlot.state() === 'pending' :
                                        !nextSlot.isRejected();
                                if (isPending) {
                                    nextSlot.resolve();
                                    break;
                                }
                                nextSlot = that._slots.shift();
                            }
                        }
                    });
                    return jqXHR;
                };
            this._beforeSend(e, options);
            if (this.options.sequentialUploads ||
                    (this.options.limitConcurrentUploads &&
                    this.options.limitConcurrentUploads <= this._sending)) {
                if (this.options.limitConcurrentUploads > 1) {
                    slot = $.Deferred();
                    this._slots.push(slot);
                    pipe = slot.pipe(send);
                } else {
                    pipe = (this._sequence = this._sequence.pipe(send, send));
                }
                // Return the piped Promise object, enhanced with an abort method,
                // which is delegated to the jqXHR object of the current upload,
                // and jqXHR callbacks mapped to the equivalent Promise methods:
                pipe.abort = function () {
                    aborted = [undefined, 'abort', 'abort'];
                    if (!jqXHR) {
                        if (slot) {
                            slot.rejectWith(options.context, aborted);
                        }
                        return send();
                    }
                    return jqXHR.abort();
                };
                return this._enhancePromise(pipe);
            }
            return send();
        },

        _onAdd: function (e, data) {
            var that = this,
                result = true,
                options = $.extend({}, this.options, data),
                limit = options.limitMultiFileUploads,
                paramName = this._getParamName(options),
                paramNameSet,
                paramNameSlice,
                fileSet,
                i;
            if (!(options.singleFileUploads || limit) ||
                    !this._isXHRUpload(options)) {
                fileSet = [data.files];
                paramNameSet = [paramName];
            } else if (!options.singleFileUploads && limit) {
                fileSet = [];
                paramNameSet = [];
                for (i = 0; i < data.files.length; i += limit) {
                    fileSet.push(data.files.slice(i, i + limit));
                    paramNameSlice = paramName.slice(i, i + limit);
                    if (!paramNameSlice.length) {
                        paramNameSlice = paramName;
                    }
                    paramNameSet.push(paramNameSlice);
                }
            } else {
                paramNameSet = paramName;
            }
            data.originalFiles = data.files;
            $.each(fileSet || data.files, function (index, element) {
                var newData = $.extend({}, data);
                newData.files = fileSet ? element : [element];
                newData.paramName = paramNameSet[index];
                newData.submit = function () {
                    newData.jqXHR = this.jqXHR =
                        (that._trigger('submit', e, this) !== false) &&
                        that._onSend(e, this);
                    return this.jqXHR;
                };
                return (result = that._trigger('add', e, newData));
            });
            return result;
        },

        _replaceFileInput: function (input) {
            var inputClone = input.clone(true);
            $('<form></form>').append(inputClone)[0].reset();
            // Detaching allows to insert the fileInput on another form
            // without loosing the file input value:
            input.after(inputClone).detach();
            // Avoid memory leaks with the detached file input:
            $.cleanData(input.unbind('remove'));
            // Replace the original file input element in the fileInput
            // elements set with the clone, which has been copied including
            // event handlers:
            this.options.fileInput = this.options.fileInput.map(function (i, el) {
                if (el === input[0]) {
                    return inputClone[0];
                }
                return el;
            });
            // If the widget has been initialized on the file input itself,
            // override this.element with the file input clone:
            if (input[0] === this.element[0]) {
                this.element = inputClone;
            }
        },

        _handleFileTreeEntry: function (entry, path) {
            var that = this,
                dfd = $.Deferred(),
                errorHandler = function (e) {
                    if (e && !e.entry) {
                        e.entry = entry;
                    }
                    // Since $.when returns immediately if one
                    // Deferred is rejected, we use resolve instead.
                    // This allows valid files and invalid items
                    // to be returned together in one set:
                    dfd.resolve([e]);
                },
                dirReader;
            path = path || '';
            if (entry.isFile) {
                if (entry._file) {
                    // Workaround for Chrome bug #149735
                    entry._file.relativePath = path;
                    dfd.resolve(entry._file);
                } else {
                    entry.file(function (file) {
                        file.relativePath = path;
                        dfd.resolve(file);
                    }, errorHandler);
                }
            } else if (entry.isDirectory) {
                dirReader = entry.createReader();
                dirReader.readEntries(function (entries) {
                    that._handleFileTreeEntries(
                        entries,
                        path + entry.name + '/'
                    ).done(function (files) {
                        dfd.resolve(files);
                    }).fail(errorHandler);
                }, errorHandler);
            } else {
                // Return an empy list for file system items
                // other than files or directories:
                dfd.resolve([]);
            }
            return dfd.promise();
        },

        _handleFileTreeEntries: function (entries, path) {
            var that = this;
            return $.when.apply(
                $,
                $.map(entries, function (entry) {
                    return that._handleFileTreeEntry(entry, path);
                })
            ).pipe(function () {
                return Array.prototype.concat.apply(
                    [],
                    arguments
                );
            });
        },

        _getDroppedFiles: function (dataTransfer) {
            dataTransfer = dataTransfer || {};
            var items = dataTransfer.items;
            if (items && items.length && (items[0].webkitGetAsEntry ||
                    items[0].getAsEntry)) {
                return this._handleFileTreeEntries(
                    $.map(items, function (item) {
                        var entry;
                        if (item.webkitGetAsEntry) {
                            entry = item.webkitGetAsEntry();
                            if (entry) {
                                // Workaround for Chrome bug #149735:
                                entry._file = item.getAsFile();
                            }
                            return entry;
                        }
                        return item.getAsEntry();
                    })
                );
            }
            return $.Deferred().resolve(
                $.makeArray(dataTransfer.files)
            ).promise();
        },

        _getSingleFileInputFiles: function (fileInput) {
            fileInput = $(fileInput);
            var entries = fileInput.prop('webkitEntries') ||
                    fileInput.prop('entries'),
                files,
                value;
            if (entries && entries.length) {
                return this._handleFileTreeEntries(entries);
            }
            files = $.makeArray(fileInput.prop('files'));
            if (!files.length) {
                value = fileInput.prop('value');
                if (!value) {
                    return $.Deferred().resolve([]).promise();
                }
                // If the files property is not available, the browser does not
                // support the File API and we add a pseudo File object with
                // the input value as name with path information removed:
                files = [{name: value.replace(/^.*\\/, '')}];
            } else if (files[0].name === undefined && files[0].fileName) {
                // File normalization for Safari 4 and Firefox 3:
                $.each(files, function (index, file) {
                    file.name = file.fileName;
                    file.size = file.fileSize;
                });
            }
            return $.Deferred().resolve(files).promise();
        },

        _getFileInputFiles: function (fileInput) {
            if (!(fileInput instanceof $) || fileInput.length === 1) {
                return this._getSingleFileInputFiles(fileInput);
            }
            return $.when.apply(
                $,
                $.map(fileInput, this._getSingleFileInputFiles)
            ).pipe(function () {
                return Array.prototype.concat.apply(
                    [],
                    arguments
                );
            });
        },

        _onChange: function (e) {
            var that = this,
                data = {
                    fileInput: $(e.target),
                    form: $(e.target.form)
                };
            this._getFileInputFiles(data.fileInput).always(function (files) {
                data.files = files;
                if (that.options.replaceFileInput) {
                    that._replaceFileInput(data.fileInput);
                }
                if (that._trigger('change', e, data) !== false) {
                    that._onAdd(e, data);
                }
            });
        },

        _onPaste: function (e) {
            var cbd = e.originalEvent.clipboardData,
                items = (cbd && cbd.items) || [],
                data = {files: []};
            $.each(items, function (index, item) {
                var file = item.getAsFile && item.getAsFile();
                if (file) {
                    data.files.push(file);
                }
            });
            if (this._trigger('paste', e, data) === false ||
                    this._onAdd(e, data) === false) {
                return false;
            }
        },

        _onDrop: function (e) {
            e.preventDefault();
            var that = this,
                dataTransfer = e.dataTransfer = e.originalEvent.dataTransfer,
                data = {};
            this._getDroppedFiles(dataTransfer).always(function (files) {
                data.files = files;
                if (that._trigger('drop', e, data) !== false) {
                    that._onAdd(e, data);
                }
            });
        },

        _onDragOver: function (e) {
            var dataTransfer = e.dataTransfer = e.originalEvent.dataTransfer;
            if (this._trigger('dragover', e) === false) {
                return false;
            }
            if (dataTransfer) {
                dataTransfer.dropEffect = 'copy';
            }
            e.preventDefault();
        },

        _initEventHandlers: function () {
            if (this._isXHRUpload(this.options)) {
                this._on(this.options.dropZone, {
                    dragover: this._onDragOver,
                    drop: this._onDrop
                });
                this._on(this.options.pasteZone, {
                    paste: this._onPaste
                });
            }
            this._on(this.options.fileInput, {
                change: this._onChange
            });
        },

        _destroyEventHandlers: function () {
            this._off(this.options.dropZone, 'dragover drop');
            this._off(this.options.pasteZone, 'paste');
            this._off(this.options.fileInput, 'change');
        },

        _setOption: function (key, value) {
            var refresh = $.inArray(key, this._refreshOptionsList) !== -1;
            if (refresh) {
                this._destroyEventHandlers();
            }
            this._super(key, value);
            if (refresh) {
                this._initSpecialOptions();
                this._initEventHandlers();
            }
        },

        _initSpecialOptions: function () {
            var options = this.options;
            if (options.fileInput === undefined) {
                options.fileInput = this.element.is('input[type="file"]') ?
                        this.element : this.element.find('input[type="file"]');
            } else if (!(options.fileInput instanceof $)) {
                options.fileInput = $(options.fileInput);
            }
            if (!(options.dropZone instanceof $)) {
                options.dropZone = $(options.dropZone);
            }
            if (!(options.pasteZone instanceof $)) {
                options.pasteZone = $(options.pasteZone);
            }
        },

        _create: function () {
            var options = this.options;
            // Initialize options set via HTML5 data-attributes:
            $.extend(options, $(this.element[0].cloneNode(false)).data());
            this._initSpecialOptions();
            this._slots = [];
            this._sequence = this._getXHRPromise(true);
            this._sending = this._active = this._loaded = this._total = 0;
            this._initEventHandlers();
        },

        _destroy: function () {
            this._destroyEventHandlers();
        },

        // This method is exposed to the widget API and allows adding files
        // using the fileupload API. The data parameter accepts an object which
        // must have a files property and can contain additional options:
        // .fileupload('add', {files: filesList});
        add: function (data) {
            var that = this;
            if (!data || this.options.disabled) {
                return;
            }
            if (data.fileInput && !data.files) {
                this._getFileInputFiles(data.fileInput).always(function (files) {
                    data.files = files;
                    that._onAdd(null, data);
                });
            } else {
                data.files = $.makeArray(data.files);
                this._onAdd(null, data);
            }
        },

        // This method is exposed to the widget API and allows sending files
        // using the fileupload API. The data parameter accepts an object which
        // must have a files or fileInput property and can contain additional options:
        // .fileupload('send', {files: filesList});
        // The method returns a Promise object for the file upload call.
        send: function (data) {
            if (data && !this.options.disabled) {
                if (data.fileInput && !data.files) {
                    var that = this,
                        dfd = $.Deferred(),
                        promise = dfd.promise(),
                        jqXHR,
                        aborted;
                    promise.abort = function () {
                        aborted = true;
                        if (jqXHR) {
                            return jqXHR.abort();
                        }
                        dfd.reject(null, 'abort', 'abort');
                        return promise;
                    };
                    this._getFileInputFiles(data.fileInput).always(
                        function (files) {
                            if (aborted) {
                                return;
                            }
                            data.files = files;
                            jqXHR = that._onSend(null, data).then(
                                function (result, textStatus, jqXHR) {
                                    dfd.resolve(result, textStatus, jqXHR);
                                },
                                function (jqXHR, textStatus, errorThrown) {
                                    dfd.reject(jqXHR, textStatus, errorThrown);
                                }
                            );
                        }
                    );
                    return this._enhancePromise(promise);
                }
                data.files = $.makeArray(data.files);
                if (data.files.length) {
                    return this._onSend(null, data);
                }
            }
            return this._getXHRPromise(false, data && data.context);
        }

    });

}));

$.contentReady(function() {
    $('.photo_block').each(function() {

        var $photoBlock = $(this);
        var $form = $(this).closest('form');
        
        /**
        * Визуальная сортировка фотографий
        */
        $('.photo-list', this).sortable({
            handle: '.handle',
            tolerance: 'pointer',
            cancel: '.disable',
            placeholder: "sortable-placeholder",
            update: function(e, ui) {
                var pos = ui.item.index();
                var url = ui.item.closest('.photo-list').data('sortUrl');
                
                var first_bad_index = $('.fail:first', ui.item.closest('.photo-list')).index();
                if (first_bad_index > -1 && pos >= first_bad_index) {
                    $(this).sortable('cancel');
                } else {
                    $.ajaxQuery({
                        url: url,
                        data: {
                            pos: pos,
                            photoid: ui.item.data('id')
                        }
                    });
                    $form.trigger('changePhoto', $photoBlock);
                }
            }
        });
        
        /**
        * Удалить выбранные фотографии в списке
        */ 
        $('.delete-list', this).off('click.photoBlock').on('click.photoBlock', function() {
            if (!confirm(lang.t('Вы действительно хотите удалить выбранные фото?'))) return false;
            var data = [];
            var photo_ids = []; //массив c id фото
            $('input[name="photos[]"]:checked', $photoBlock).each(function() {
                data.push({
                    name:"photos[]",
                    value: $(this).val()
                }); 
                photo_ids.push($(this).val());
            });
            
            var url = $(this).attr('formaction');
            var selected = $('.photo-one', $photoBlock).has(".chk input:checkbox:checked");
            selected.css('opacity', '0.5');
            
            $.ajaxQuery({
                url: url,
                data:data,
                success: function() {
                    selected.remove();
                    $('.upload-block', $photoBlock).removeClass('can-delete');
                    $form.trigger('changePhoto', $photoBlock);
                }
            });
            return false;
        });
        
        /**
        * Выделение всех фото
        */
        $('.check-all', this).off('click.photoBlock').on('click.photoBlock', function() {
            if ($('input[name="photos[]"]', $photoBlock).length == $('input[name="photos[]"]:checked', $photoBlock).length) {
                $('input[name="photos[]"]', $photoBlock).prop('checked', false).change();
            } else {
                $('input[name="photos[]"]', $photoBlock).prop('checked', true).change(); 
            }
        });
        
        /**
        * Назначение действий на фото
        */
        $('.photo-one', this).each(function() {

            if ($(this).data('photoOne')) return;
            $(this).data('photoOne', {});
            
            $('.title .short', this).click(function() {
                var $short_title = $(this);
                $('.edit_title', $(this).parent())
                    .show()
                    .focus()
                    .rsCheckOutClick(function() {
                        //Сохраняем описание
                        if ($short_title.text() != $(this).val()) {
                            $short_title.text($(this).val());
                            $.ajaxQuery({
                                url: $short_title.closest('.photo-list').data('editUrl'),
                                type: 'post',
                                data: {
                                    photoid: $short_title.closest('.photo-one').data('id'),
                                    title: $(this).val()
                                },
                            })
                        }
                        $(this).hide();
                    }, this);
            });
            
            /**
            * Удаление одной фотографии
            * 
            */
            $('.delete', this).click(function() {
                if (confirm(lang.t('Вы действительно хотите удалить фото?'))) {
                    var photo_wrap = $(this).closest('.photo-one');
                    var photo_id   = photo_wrap.data('id');
                    var block      = photo_wrap.css('opacity', '0.5');
                    $.ajaxQuery({
                        url: $(this).attr('href'),
                        success: function() {
                            block.remove();
                            $form.trigger('changePhoto', $photoBlock);
                        }
                    });
                }
                return false;
            });
            
            /**
            * Поворот, отображение фотографии
            * 
            */
            $('.rotate, .flip', this).click(function() {
                var $photoOne = $(this).closest('.photo-one');
                var img = $photoOne.find('.image img').css('opacity', 0.5);
                var a = $photoOne.find('.bigview');

                $.ajaxQuery({
                    url: $(this).attr('href'),
                    success: function() {
                        img.css('opacity', 1);

                        var new_img_src, new_a_href;

                        if (img.data('originalSrc')) {
                            new_img_src = img.data('originalSrc')+'?r='+Math.random();
                        } else {
                            img.data('originalSrc', img.attr('src'));
                            new_img_src = img.attr('src')+'?r='+Math.random();
                        }

                        if (a.data('originalHref')) {
                            new_a_href = a.data('originalHref')+'?r='+Math.random();
                        } else {
                            a.data('originalHref', a.attr('href'));
                            new_a_href = a.attr('href')+'?r='+Math.random();
                        }

                        img.attr('src', new_img_src);
                        a.attr('href', new_a_href);
                    }
                });

                return false;
            });
            
            /**
            * Выделение фотографий
            * 
            */
            $('.chk input:checkbox').change(function() {
                var selected = $('.chk input:checkbox:checked', $photoBlock);
                if (selected.length) {
                    $('.upload-block', $photoBlock).addClass('can-delete');
                } else {
                    $('.upload-block', $photoBlock).removeClass('can-delete');
                }
            });
                
        });
        
        $photoBlock.fileupload({
            dataType: 'json',
            dropZone: $('.dragzone', this),
            pasteZone:null,
            filesContainer: $('.photo-list', this),
            uploadTemplateId:null,            
            downloadTemplateId:null,
            sequentialUploads:true,
            url: $photoBlock.attr('action'),
            type: 'POST',
            formData: function() {
                return {}
            },
            add: function(e, data) {
                var $photo = $(
                '<li class="photo-one inqueue">\
                  <div class="chk"></div>\
                    <div class="image">\
                        <a class="cancel"></a>\
                        <div class="wait"></div>\
                        <div class="action"></div>\
                        <div class="filename"></div>\
                    </div>\
                    <div class="title">\
                        <div class="progress">\
                            <div class="bar"></div>\
                            <div class="percent">0%</div>\
                        </div>\
                    </div>\
                    <div class="move disable"></div>\
                </li>');
                $photo.find('.delete').attr('title', lang.t('отменить загрузку'));
                $photo.find('.action').text(lang.t('В очереди'));
                $photo.find('.filename').text(lang.t(data.files[0].name));
                
                $('.photo-list', this).append($photo);
                data.li = $photo;
                                
                var jqXHR = data.submit();
                $photo.find('.cancel').one('click', function() {
                    jqXHR.abort();
                });
            },
            send: function(e, data) {
                data.li.removeClass('inqueue').addClass('uploading');
                data.li.find('.action').text(lang.t('Идет загрузка'));
            },
            progress: function(e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                data.li.find('.bar').css('width', progress+'%');
                data.li.find('.percent').text(progress+'%');
            },
            done: function(e, data) {
                data.li.find('.bar').css('width', '100%');
                data.li.find('.percent').text('100%');
                if (data.result.items[0].success) {
                    data.li.replaceWith(data.result.items[0].html);
                    data.form.trigger('new-content');  
                    $form.trigger('changePhoto', $photoBlock);
                } else {
                    data.textStatus = data.result.items[0].errors.join(', ');
                    data.fail(e, data);
                }
            },
            fail: function(e, data) {
                data.li.removeClass('uploading').addClass('fail');
                data.li.find('.wait').remove();
                data.li.find('.action').text(data.textStatus);
                data.li.find('.cancel').click(function() {
                    data.li.remove();
                });
            },
            
            start: function(e) {
                $('.photo-list', $photoBlock).addClass('disable-sort');
            },
            
            stop: function(e) {
                $('.photo-list', $photoBlock).removeClass('disable-sort');
            }
        });
        
        // скрываем кнопку "выбрать все" если нет фото
        $form.on('changePhoto', function(e, context) {
            if ($('.photo-one', context).length) {
                $('.upload-block', context).removeClass('no-photos');
            } else {
                $('.upload-block', context).addClass('no-photos');
            }
        });
    });
});
/**
* Plugin, активирующий универсальный блок файлов
*/
(function($){
    
$.fn.filesBlock = function(method) {

    var defaults = {
        filesList:      '.files-list',
        fileLine:       '.files-list .item',
        fileEditLine:   '.edit-form',
        fileDelete:     '.file-delete',
        fileEdit:       '.file-edit, .files-list td.clickable',
        deleteSelected: '.group-toolbar .delete',
        accessFileInput:'.access_file',
        virtualForm:    '.virtual-form',         //Класс виртуальной формы
        virtualSubmit:  '.virtual-form .virtual-submit, .virtual-form button.ok',
        tableInlineEditCancel: '.table-inline-edit .cancel',
        newFileLineHtml: function() {
            return $('\
                    <tr data-id="{$linked_file.id}" class="item nodrop inqueue">\
                        <td class="chk"></td>\
                        <td class="drag drag-handle"></td>\
                        <td class="title"></td>\
                        <td class="description">' + lang.t('нет') + '</td>\
                        <td class="size"></td>\
                        <td class="progress"> \
                            <span class="progress-bar"><i class="bar"></i><span class="percent">' + lang.t('в очереди') + '</span></span>\
                            <span class="error"></span>\
                        </td>\
                        <td class="actions">\
                            <span class="loader"></span>\
                            <a class="cancel delete">' + lang.t('удалить') + '</a>\
                        </td>\
                    </tr>');
        },
        fileFormHtml: function() {
            return $('<tr class="edit-form no-over">'+
                        '<td colspan="7">'+
                            '<div class="bordered"></div>'+
                        '</td>'+
                     '</tr>');
        }
    }, 
    args = arguments;
    
    return this.each(function() {
        var $this = $(this), 
            xhr,
            data = $this.data('filesBlock');
        var methods = {
            init: function(initoptions) {
                if (data) return;
                data         = {}; $this.data('filesBlock', data);
                data.options = $.extend({}, defaults, initoptions);
                data.urls = $this.data('urls');
                initFileUpload();
                
                $this
                    .on('click', data.options.virtualSubmit, submitVirtualForm)
                    .on('click', data.options.fileDelete, function() {
                        var id = $(this).closest('[data-id]').data('id');
                        methods.deleteFile([id]);
                    })
                    .on('click', data.options.deleteSelected, function() {
                        var ids = [];
                        $('input[name="files[]"]:checked', $this).each(function() {
                            ids.push($(this).val());
                        });
                        methods.deleteFile(ids);
                    })
                    .on('change', data.options.accessFileInput, function() {
                        var id = $(this).closest('[data-id]').data('id');
                        var access = $(this).val();
                        methods.changeAccessFile(id, access);
                    })
                    .on('click', data.options.fileEdit, function() {
                        var id = $(this).closest('[data-id]').data('id');
                        methods.editFile(id);
                    })
                    .on('change', data.options.filesList + ' input[type="checkbox"]', function() {
                        checkActiveCheckbox();                        
                    })
                    .on('click', data.options.tableInlineEditCancel, function() {
                        var id = $(this).closest(data.options.fileEditLine).data('id');
                        methods.editFile(id);
                    });
                    
                $this.closest('.dialog-window').on('dialogBeforeDestroy', function() {
                    $this
                        .trigger('enableBottomToolbar', 'file-checked')
                        .trigger('enableBottomToolbar', 'file-edit');
                });                    
            },
            
            deleteFile: function(ids) {
                if (ids.length && confirm(lang.t('Вы действительно хотите удалить выбранные файлы?'))) {
                    $.each(ids, function(i, val) {
                        $('.files-list .item[data-id="'+val+'"]', $this).css('opacity', 0.5);
                    });
                    $.ajaxQuery({
                        type: 'POST',
                        url: data.urls.fileDelete,
                        data: {
                            files: ids
                        },
                        success: function(response) {
                            if (response.success) {
                                $.each(ids, function(i, val) {
                                    $('.files-list .item[data-id="'+val+'"]', $this).remove();
                                });
                                $(data.options.filesList + ' input[type="checkbox"]', $this).prop({'disabled': false, 'checked': false}).change();
                                checkEmpty();
                            }
                        }
                    });
                }
            },
            
            editFile:function(id) {
                var edit_line = $(data.options.fileLine + '[data-id="'+id+'"]', $this);
                var is_opened = edit_line.is('.now-edit');
                
                $(data.options.fileLine, $this).removeClass('now-edit load');
                $(data.options.fileEditLine, $this).remove();
                $(data.options.filesList + ' > tbody > tr', $this).removeClass('nodrag nodrop');
                
                if (is_opened) {
                    $(data.options.filesList + ' input[type="checkbox"]', $this).prop({'disabled': false, 'checked': false}).change();
                } 
                
                if (xhr) xhr.abort();
                
                if (is_opened) { //Закрытие режима редактирования
                    $this.trigger('enableBottomToolbar', 'file-edit');
                    return;
                }
                
                $(data.options.filesList + ' input[type="checkbox"]', $this).prop('disabled', true);
                $this.trigger('disableBottomToolbar', 'file-edit');
                edit_line.addClass('now-edit load');
                
                xhr = $.ajaxQuery({
                    url: data.urls.fileEdit,
                    data: {
                        file: id
                    },
                    success: function(response) {
                        edit_line.removeClass('load');
                        var edit_wrap = data.options.fileFormHtml();
                             
                        edit_wrap.data('id', id).data('editLine', edit_line).find('.bordered').html(response.html);
                        edit_wrap.insertAfter(edit_line).trigger('new-content');
                        
                        //Отключаем сортировку
                        $(data.options.filesList + ' > tbody > tr').addClass('nodrag nodrop');
                    }
                });
            },
            
            changeAccessFile: function(id, access) {
                $.ajaxQuery({
                    type: 'POST',
                    url: data.urls.fileChangeAccess,
                    data: {
                        file: id,
                        access: access
                    }
                });
            }
        }
        
        //private     
        var initFileUpload = function()
        {
            $this.fileupload({
                dataType: 'json',
                dropZone: $('.dragzone', $this),
                pasteZone:null,
                filesContainer: $('.files-list', $this),
                uploadTemplateId:null,
                downloadTemplateId:null,
                sequentialUploads:true,
                url: data.urls.fileUpload,
                type: 'POST',
                formData: function() {
                    return {}
                },
                add: function(e, datafile) {
                    var $file = data.options.newFileLineHtml();
                    $file.find('.title').text(datafile.files[0].name);
                    $file.find('.size').text(bytesToSize(datafile.files[0].size));
                    datafile.li = $file.appendTo( $(this).find(data.options.filesList) );
                                    
                    var jqXHR = datafile.submit();
                    $file.find('.cancel').one('click', function() {
                        jqXHR.abort();
                    });
                    
                    checkEmpty();
                },
                send: function(e, data) {
                    data.li.removeClass('inqueue').addClass('uploading');
                },
                progress: function(e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    data.li.find('.bar').css('width', progress+'%');
                    data.li.find('.percent').text(progress+'%');
                },
                done: function(e, data) {
                    data.li.find('.bar').css('width', '100%');
                    data.li.find('.percent').text('100%');              
                    if (data.result.items[0].success) {
                        data.li.replaceWith(data.result.items[0].html);
                        data.form.trigger('new-content');  
                    } else {
                        data.textStatus = data.result.items[0].error;
                        data.fail(e, data);
                    }
                },
                fail: function(e, data) {
                    data.li.removeClass('uploading').addClass('fail');
                    data.li.find('.progress-bar').remove();
                    data.li.find('.error').text(data.textStatus);
                    data.li.find('.cancel').click(function() {
                        data.li.remove();
                        checkEmpty();
                    });                  
                },
                
                start: function(e) {
                    $('.files-list', $this).addClass('disable-sort');
                },
                
                stop: function(e) {
                    $('.files-list', $this).removeClass('disable-sort');
                }
            });
        },
        
        checkEmpty = function() {
            $('.files-container').toggleClass('hidden', !$(data.options.fileLine).length);
        },
        
        checkActiveCheckbox = function() {
            //Если есть отмеченные элементы, то посылаем событие - Запретить действия над товаром, иначе - разрешить
            if ($(data.options.filesList + ' input[type="checkbox"]:checked', $this).length) {
                $this.trigger('disableBottomToolbar', 'file-checked');
            } else {
                $this.trigger('enableBottomToolbar', 'file-checked');
            }
        },
        
        bytesToSize = function (bytes) {
           var sizes = [lang.t('Байт'), lang.t('Кб'), lang.t('Мб'), lang.t('Гб'), lang.t('Тб')];
           if (bytes == 0) return '0 ' + sizes[0];
           var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
           return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
        },
        
        submitVirtualForm = function(e) 
        {
            var edit_form = $(this).closest(data.options.fileEditLine);
            var edit_line = edit_form.data('editLine');
            var form = $(this).closest(data.options.virtualForm);
            var real_form = $('<form />');
            form.find('input, select, textarea').each(function() {
                var element = $(this).clone();
                if (element.is('select, textarea')) {
                    element.val( $(this).val() ); //bugfix select clone
                }
                element.appendTo(real_form);
            });
            
            var params = real_form.serializeArray();
            $.ajaxQuery({
                url: form.data('action'),
                data: params,
                method: 'post',
                success: function(response) {
                    //Если это пост виртуальной формы, то отображаем ошибки формы, если они есть
                    if (response.success) {                        
                        //Закрываем форму редактирования
                        methods.editFile(edit_line.data('id'));
                        edit_line.replaceWith(response.html);
                        $this.trigger('new-content');
                    } else {
                        $('.crud-form-error', form).fillError(response.formdata.errors, form);
                    }
                }
            });     
            
            e.preventDefault();
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
    $('.files-block', this).filesBlock();
});
