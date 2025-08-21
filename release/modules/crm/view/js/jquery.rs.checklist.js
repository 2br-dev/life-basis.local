/**
 * Плагин инициализирует работу чеклистов с возможностью редактирования и сортировки
 *
 * @author ReadyScript lab.
 */
(function($) {

    $.fn.checklists = function(method) {
        let defaults = {
            groupSelector: '.checklist-group',
            itemsSelector: '.checklist-items',
            itemSelector: '.checklist-item',
            selectButton: '.select-product-btn',
            addGroupBtnSelector: '#addGroupBtn'
        }, args = arguments;

        return this.each(function() {
            let $this = $(this),
                data = $this.data('checklists');

            let methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('checklists', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    data.checklistCanEdit = $this.data('checklist-can-edit') === true || $this.data('checklist-can-edit') === 'true';
                    data.lastClickedElement = null;

                    init();
                }
            };

            let init = function() {
                $(document).on('mousedown', function(e) {
                    data.lastClickedElement = e.target;
                });

                bindAllItemLabels();
                bindAllGroupTitles();
                initSortable();

                $(data.opt.addGroupBtnSelector).off('click').on('click', function(e) {
                    e.preventDefault();
                    resetEditing();
                    let newGroup = createChecklistGroup('');
                    $this.append(newGroup);
                    bindGroupTitle(newGroup);
                    initSortable();
                    updateChecklistFields();

                    setTimeout(() => {
                        enableEditGroup(newGroup.get(0));
                        newGroup.find('input.group-title-input').focus();
                    }, 0);
                });

                $(document).off('click.checklists').on('click.checklists', function(e) {
                    if ($(e.target).closest('.select-product-btn, #checklistProductDialog, .ui-dialog-titlebar, .ui-dialog-buttonpane, .ui-widget-overlay, .bottom-toolbar, .tab-nav').length) {
                        return;
                    }

                    if ($(e.target).closest('input.item-label-input, input.group-title-input, .item-label, .group-title').length === 0) {
                        resetEditing();
                    }
                });

                updateChecklistFields();
                bindSelectProducts();
            };

            /**
             * Вызывает диалоговое окно с выбором товара и при выборе сохраняет значение в скрытом поле
             */
            let bindSelectProducts = function() {
                $this.selectProduct({
                    dialog: 'checklistProductDialog',
                    startButton: data.opt.selectButton,

                    onSelectProduct: function(params) {
                        let element = params.openDialogEvent.target;
                        let $button = $(element).closest('.select-product-btn');
                        let $item = $button.closest(data.opt.itemSelector);

                        if ($item.length) {
                            $item.data('entity-id', params.productId);
                            $item.data('entity-type', 'product');
                            $item.attr('data-has-product', 'true');

                            const baseUrl = $('#checklistContainer').data('product-url');
                            let productUrl = baseUrl ? `${baseUrl}&id=${params.productId}` : null;

                            const productLink = `
                                <a 
                                    href="${productUrl}" 
                                    target="_blank"
                                    class="crud-edit"
                                    data-crud-dialog-width="90%"
                                    data-crud-dialog-height="90%"
                                >${params.productTitle} (${params.productBarcode})</a>
                            `;
                            const newHtml = `
                                ${productLink} 
                                <button type="button" class="remove-btn remove-item-btn" title="Удалить товар">
                                    <i class="zmdi zmdi-close"></i>
                                </button>`;

                            let $input = $item.find('input.item-label-input');
                            if ($input.length) {
                                $item.attr('data-editing', 'false');
                                $input.replaceWith(`<span class="item-label">${newHtml}</span>`);
                            } else {
                                $item.find('.item-label').html(newHtml);
                            }

                            bindItemLabel($item);
                            updateChecklistFields();
                        }

                        params.dialog.dialog('close');
                    }

                });
            };

            /**
             * Сбрасывает блок редактирования
             */
            let resetEditingBlock = function($container, selector, inputClass, spanClass, defaultText = null) {
                $container.find(selector).each(function () {
                    if (this.dataset.editing === "true") {
                        this.dataset.editing = "false";
                        const $input = $(this).find(`input.${inputClass}`);
                        const val = $input.val().trim() || defaultText;

                        if (!val) {
                            $(this).remove();
                        } else {
                            $input.replaceWith(`<span class="${spanClass}">${val} <i class="zmdi zmdi-edit edit-icon"></i></span>`);
                        }
                    }
                });
            }

            /**
             * Сбрасывает редактирование всех пунктов и групп, возвращая их в режим отображения
             */
            let resetEditing = function() {
                resetEditingBlock($this, data.opt.itemSelector, 'item-label-input', 'item-label');
                resetEditingBlock($this, data.opt.groupSelector, 'group-title-input', 'group-title', 'Новая группа');
                bindAllItemLabels();
                bindAllGroupTitles();
            };

            /**
             * Переводит пункт в режим редактирования
             */
            let enableEditItem = function(item) {
                resetEditing();
                item.dataset.editing = "true";
                let $label = $(item).find('.item-label');
                let text = $label.text().trim();
                $label.replaceWith(`<input type="text" class="item-label-input" value="${text}" autofocus>`);
                let $input = $(item).find('input.item-label-input');

                $input.on('blur', () => finishEditItem(item, $input));
                $input.on('keydown', e => {
                    if (e.key === 'Enter') {
                        let value = $input.val().trim();
                        $input.blur();

                        if (!value) return;

                        setTimeout(() => {
                            let $group = $(item).closest(data.opt.groupSelector);
                            let $items = $group.find(data.opt.itemsSelector);
                            let newItem = createChecklistItem('');
                            $items.append(newItem);
                            bindItemLabel(newItem);
                            initSortable();

                            enableEditItem(newItem.get(0));
                            newItem.find('input.item-label-input').focus();
                        }, 0);
                    }
                    if (e.key === 'Escape') {
                        $input.val($input.attr('value'));
                        $input.blur();
                    }
                });
            };

            /**
             * Завершает редактирование пункта
             */
            let finishEditItem = function(item, $input) {
                const $last = $(data.lastClickedElement);
                const clickedSelectBtn = $last.closest('.select-product-btn').length > 0;

                if (clickedSelectBtn) {
                    return;
                }

                let val = $input.val().trim();
                if (!val) {
                    $(item).remove();
                    updateChecklistFields();
                    return;
                }

                item.dataset.editing = "false";
                $input.replaceWith(`<span class="item-label">${val} <i class="zmdi zmdi-edit edit-icon"></i></span>`);
                bindItemLabel($(item));
                updateChecklistFields();
            };

            /**
             * Переводит заголовок группы в режим редактирования
             */
            let enableEditGroup = function(group) {
                resetEditing();
                group.dataset.editing = "true";
                let $title = $(group).find('.group-title');
                let text = $title.text().trim();
                $title.replaceWith(`<input type="text" class="group-title-input" size="70" value="${text}" placeholder="` + lang.t('Наименование группы') + `" autofocus>`);
                let $input = $(group).find('input.group-title-input');

                $input.on('blur', () => finishEditGroup(group, $input));
                $input.on('keydown', e => {
                    if (e.key === 'Enter') {
                        $input.blur();

                        setTimeout(() => {
                            let $items = $(group).find(data.opt.itemsSelector);
                            let newItem = createChecklistItem('');
                            $items.append(newItem);
                            bindItemLabel(newItem);
                            initSortable();

                            enableEditItem(newItem.get(0));
                            newItem.find('input.item-label-input').focus();
                        }, 0);
                    }
                    if (e.key === 'Escape') {
                        $input.val($input.attr('value'));
                        $input.blur();
                    }
                });
            };

            /**
             * Завершает редактирование заголовка группы
             */
            let finishEditGroup = function(group, $input) {
                let val = $input.val().trim() || "Новая группа";
                group.dataset.editing = "false";
                $input.replaceWith(`<span class="group-title">${val} <i class="zmdi zmdi-edit edit-icon"></i></span>`);
                bindGroupTitle($(group));
                updateChecklistFields();
            };

            /**
             * Назначает обработчики клика, удаления и чекбокса для пункта задачи
             */
            let bindItemLabel = function($item) {
                $item.find('.item-label').off('click');

                if (data.checklistCanEdit) {
                    $item.find('.item-label').on('click', function(e) {
                        if ($(e.target).closest('a').length > 0) return;
                        enableEditItem($item.get(0));
                    });
                }


                $item.find('.remove-item-btn').off('click').on('click', () => {
                    $item.remove();
                    updateChecklistFields();
                });

                $item.find('input[type="checkbox"]').off('change').on('change', () => {
                    updateChecklistFields();
                });
            };


            /**
             * Назначает обработчики на все пункты задачи в текущем контейнере
             */
            let bindAllItemLabels = function() {
                $this.find(data.opt.itemSelector).each((_, item) => bindItemLabel($(item)));
            };

            /**
             * Назначает обработчики на заголовок, кнопку удаления и кнопку добавления пункта в группе
             */
            let bindGroupTitle = function($group) {
                $group.find('.group-title').off('click');

                if (data.checklistCanEdit) {
                    $group.find('.group-title').on('click', () => {
                        enableEditGroup($group.get(0))
                    });
                }

                $group.find('.remove-group-btn').off('click').on('click', () => {
                    $group.remove();
                    updateChecklistFields();
                });
                $group.find('.add-item-btn').off('click').on('click', function(e) {
                    e.preventDefault();
                    resetEditing();
                    let $items = $group.find(data.opt.itemsSelector);
                    let newItem = createChecklistItem('');
                    $items.append(newItem);
                    bindItemLabel(newItem);
                    initSortable();
                    updateChecklistFields();

                    setTimeout(() => {
                        enableEditItem(newItem.get(0));
                        newItem.find('input.item-label-input').focus();
                    }, 0);
                });
            };

            /**
             * Назначает обработчики на все группы в текущем контейнере
             */
            let bindAllGroupTitles = function() {
                $this.find(data.opt.groupSelector).each((_, group) => bindGroupTitle($(group)));
            };

            /**
             * Создает DOM-элемент нового пункта задачи с базовой разметкой
             */
            let createChecklistItem = function(text = "Новая задача") {
                return $(`
                    <div class="checklist-item" data-editing="false">
                        <i class="zmdi zmdi-unfold-more sort-handle"></i>
                        <input type="checkbox">
                        <span class="item-label">${text} <i class="zmdi zmdi-edit edit-icon"></i></span>
                        <a class="btn btn-success select-product-btn">
                            <i class="zmdi zmdi-plus"></i> 
                            <span>Указать товар</span>
                        </a>
                        <button type="button" class="remove-btn remove-item-btn" title="Удалить пункт">
                            <i class="zmdi zmdi-close"></i>
                        </button>
                    </div>
                `);
            };

            /**
             * Создает DOM-элемент новой группы с базовой разметкой
             */
            let createChecklistGroup = function(title = "Новая группа") {
                return $(`
                    <div class="checklist-group" data-editing="false">
                        <div class="checklist-header">
                            <span class="group-title">${title} <i class="zmdi zmdi-edit edit-icon"></i></span>
                            <button type="button" class="remove-btn remove-group-btn" title="Удалить группу">
                                <i class="zmdi zmdi-close"></i>
                            </button>
                        </div>
                        <div class="checklist-items ui-sortable"></div>
                        <a href="#" class="add-item-btn">+ добавить пункт</a>
                    </div>
                `);
            };

            /**
             * Инициализирует сортировку пунктов в рамках группы через jQuery UI Sortable
             */
            let initSortable = function() {
                $this.find(data.opt.itemsSelector).sortable({
                    tolerance: 'pointer',
                    handle: '.sort-handle',
                    items: data.opt.itemSelector,
                    start: function() {
                        resetEditing();
                    },
                    stop: function() {
                        updateChecklistFields();
                    }
                });
            };

            /**
             * Получает или создает уникальный идентификатор для элемента
             */
            let getOrCreateUniq = function($el) {
                let uniq = $el.data('uniq');
                if (!uniq) {
                    uniq = Date.now().toString(36) + Math.random().toString(36).substring(2, 10);
                    $el.data('uniq', uniq);
                }
                return uniq;
            }

            /**
             * Обновляет прогрессбар на основе всех отмеченных задач
             */
            let updateProgressBar = function() {
                const items = $this.find(`${data.opt.itemSelector} input[type="checkbox"]`);
                const total = items.length;

                const label = $('.checklist-progress .progress-label');
                const bar = $('.checklist-progress .progress-bar-fill');

                if (total === 0) {
                    label.text('0%');
                    bar.css('width', '0%');
                    return;
                }

                const checked = items.filter(':checked').length;
                const percent = Math.round((checked / total) * 100);

                label.text(`${percent}%`);
                bar.css('width', `${percent}%`);
            };

            /**
             * Обновляет скрытые поля формы, синхронизируя данные чеклиста перед отправкой
             */
            let updateChecklistFields = function() {
                $this.find('input[type="hidden"][name^="checklist["]').remove();

                $this.find(data.opt.groupSelector).each(function(_, group) {
                    let $group = $(group);
                    let groupUniq = getOrCreateUniq($group);

                    let groupTitle = $group.find('.group-title').text().trim();
                    if (!groupTitle) {
                        let $input = $group.find('input.group-title-input');
                        if ($input.length) groupTitle = $input.val().trim();
                    }
                    groupTitle = groupTitle || 'Новая группа';

                    let groupPrefix = `checklist[${groupUniq}]`;

                    $('<input>', { type: 'hidden', name: `${groupPrefix}[title]`, value: groupTitle }).appendTo($this);

                    $group.find(data.opt.itemSelector).each(function(_, item) {
                        let $item = $(item);
                        let itemUniq = getOrCreateUniq($item);

                        let label = $item.find('.item-label').text().trim();
                        if (!label) {
                            let $input = $item.find('input.item-label-input');
                            if ($input.length) label = $input.val().trim();
                        }
                        label = label || 'Новая задача';

                        let type = $item.data('type') || '';
                        let entityType = $item.data('entity-type') || '';
                        let entityId = $item.data('entity-id') || '';

                        let itemPrefix = `${groupPrefix}[items][${itemUniq}]`;

                        let $checkbox = $item.find('input[type="checkbox"]');
                        $checkbox.attr('name', `${itemPrefix}[is_done]`);
                        $checkbox.val('1');

                        $('<input>', {
                            type: 'hidden',
                            name: `${itemPrefix}[is_done]`,
                            value: '0'
                        }).insertBefore($checkbox);

                        $('<input>', { type: 'hidden', name: `${itemPrefix}[title]`, value: label }).appendTo($this);
                        $('<input>', { type: 'hidden', name: `${itemPrefix}[entity_type]`, value: entityType }).appendTo($this);
                        $('<input>', { type: 'hidden', name: `${itemPrefix}[entity_id]`, value: entityId }).appendTo($this);
                    });
                });

                updateProgressBar();
            };

            if (methods[method]) {
                methods[method].apply(this, Array.prototype.slice.call(args, 1));
            } else if (typeof method === 'object' || !method) {
                return methods.init.apply(this, args);
            }
        });
    };

})(jQuery);
