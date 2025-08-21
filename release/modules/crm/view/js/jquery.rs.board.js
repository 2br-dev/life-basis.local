/**
 * Плагин инициализирует в административной панели работу Kanban доски
 *
 * @author ReadyScript lab.
 */
(function( $ ){

    $.fn.board = function( method ) {
        let defaults = {
                columns:'.crm-status-columns',
                columnItems: '.crm-column-items',
                columnItem: '.crm-column-item',
                columnClassPrefix: '.crm-column-items.status-id-',
                ajaxPaginator: '.ajaxPaginator',

                searchLine: '.search-line',
                searchLineField: '.search-line__field',
                searchLineButtonSearch: '.search-line__button-search',
                searchLineButtonClear: '.search-line__button-clear'
            },
            args = arguments;

        return this.each(function() {
            let $this = $(this),
                data = $this.data('board');

            let methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('board', data);
                    data.opt = $.extend({}, defaults, initoptions);
                    data.opt.searchUrl = $(data.opt.searchLine).data('urlSearch');
                    data.opt.canClear = !!$(data.opt.searchLine).data('term');

                    init();
                    $(data.opt.searchLineField).on('keydown', function (e) {
                        if (e.keyCode === 13 && e.target.nodeName === 'INPUT') {
                            e.preventDefault();
                            search();
                        }

                    });
                    $(data.opt.searchLineField).on('input', function (e) {
                        if (e.target.value.length || data.opt.canClear) {
                            $(data.opt.searchLineButtonClear).removeClass('hidden');
                        }else {
                            $(data.opt.searchLineButtonClear).addClass('hidden');
                        }
                    });
                    $(data.opt.searchLineButtonSearch).on('click', function (e) {
                        search();
                    });
                    $(data.opt.searchLineButtonClear).on('click', function (e) {
                        $(data.opt.searchLineField).val('');
                        search(true);
                    });
                    $this.on('click', '.crm-column-item-title .crud-edit.u-link', function() {
                        $(this).closest('.crm-column-item').find('.view-element').removeClass('is-new');
                    });
                    $this.on('click', '.crm-status-head__button-read-all', event => {
                        markAllViewed(event);
                    });
                }
            };

            let init = function() {

                let allColumns = $(data.opt.columnItems);
                let columns = $(data.opt.columns);
                $(data.opt.columnItems).each(function(i) {

                    let columnClasses = [];
                    allColumns.not(this).each(function() {
                        columnClasses.push(data.opt.columnClassPrefix + $(this).data('statusId'));
                    });

                    $(this).sortable({
                        tolerance: 'pointer',
                        connectWith: columnClasses,
                        cursor: 'move',
                        placeholder: "sortable-placeholder",
                        items : data.opt.columnItem,
                        forcePlaceholderSize: true,
                        receive: function(e, ui) {
                            //Переносим пагинатор в конец списка
                            let paginator = ui.item.prev(data.opt.ajaxPaginator);
                            if (paginator.length) {
                                paginator.insertAfter(ui.item);
                            }
                        },
                        stop: function(e, ui) {
                            //Фиксируем новую позицию
                            let direction, toElement;
                            let fromElement = ui.item.data('id');
                            let status_id = ui.item.closest('[data-status-id]').data('statusId');
                            let prev = ui.item.prev(data.opt.columnItem);
                            let next = ui.item.next(data.opt.columnItem);
                            if (prev.length>0) {
                                direction = 'down';
                                toElement = prev.data('id');
                            }
                            else if (next.length>0) {
                                direction = 'up';
                                toElement = next.data('id');
                            }
                            $.ajaxQuery({
                                url:columns.data('sortUrl'),
                                data: {
                                    from: fromElement,
                                    to: toElement,
                                    direction: direction,
                                    status_id: status_id
                                },
                                success: function(response) {
                                    if (response.success) {
                                        ui.item.find('.view-element').removeClass('is-new')
                                    }
                                }
                            });
                        }
                    });

                });

                columns.disableSelection();
            };

            let search = function(clear = false) {
                if (clear || $(data.opt.searchLineField).val()) {
                    $.ajaxQuery({
                        url: data.opt.searchUrl,
                        data: {term: $(data.opt.searchLineField).val()},
                        success: function(response) {
                            $('.updatable').html(response.html).trigger('new-content');
                        }
                    });
                }
            };

            let markAllViewed = function (event) {
                if (confirm(lang.t('Прочитать все в данном статусе?'))) {
                    const url = $(event.target).closest('.crm-status-head__button-read-all').data('urlMarkAllViewed');
                    $.ajaxQuery({
                        url,
                        success: function(response) {
                            if (response.success) {
                                const column = $(event.target).closest('.crm-status-column');
                                const items = column.find('.crm-column-item');

                                if (items.length) {
                                    items.each(function() {
                                        $(this).find('.view-element').removeClass('is-new');
                                    });
                                }
                            }
                        }
                    });
                }
            }

            if ( methods[method] ) {
                methods[ method ].apply( this, Array.prototype.slice.call( args, 1 ));
            } else if ( typeof method === 'object' || ! method ) {
                return methods.init.apply( this, args );
            }
        });
    };

})( jQuery );