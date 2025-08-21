/**
 * Скрипт отвечает за работу секции настройки прав к модулям и пунктам меню
 */
$(function() {
    $('.access-item input').on('change', function() {
        //Устанавливаем флажки вниз по дереву
        $(this).closest('.access-scope')
            .find('.item-right:not(.hidden), .item-head:not(.hidden)')
            .find('input[value="'+$(this).val()+'"]')
            .prop('checked', true);

        //Сбрасываем родительские флажки, при изменении дочернего
        $(this).closest('.access-scope')
            .parents('.access-scope')
            .children('.access-group')
            .find('input')
            .prop('checked', false);
    });

    let listItems = $('.modrights .list-item:not(.all)');

    //Фильтр
    $('#module-name, #module-right-name').on('input', function() {
        let moduleName = $('#module-name').val();
        let rightName = $('#module-right-name').val();

        listItems.each(function() {
            let _this = this;

            $('.access-right', this).each(function() {
                let visible = (rightName === '') || ($('.title', this)
                    .text().toUpperCase().indexOf(rightName.toUpperCase()) > -1);

                $(this).toggleClass('hidden', !visible);
            });

            $('.item-head', this).each(function() {
                let visible = (moduleName === '') || ($('.title', this).text()
                    .toUpperCase().indexOf(moduleName.toUpperCase()) > -1)
                || ($('.class', this).text().toUpperCase().indexOf(moduleName.toUpperCase()) > -1);

                $(_this).toggleClass('hidden', !visible);
            })
        });
    }).on('keydown', function(e) {
        if (e.keyCode === 13) {
            e.preventDefault();
        }
    });

    //Раскрыть дерево
    $('.modrights .expand-all').on('click', function() {
        listItems.each(function() {
            $('.handler', this).removeClass('collapsed');
            $('> .item-rights', this).addClass('in').css('height', 'auto');
        });
        $('.modrights .collapse-all').show();
        $(this).hide();
    });

    //Закрыть дерево
    $('.modrights .collapse-all').on('click', function() {
        listItems.each(function() {
            $('.handler', this).addClass('collapsed');
            $('> .item-rights', this).removeClass('in');
        });
        $('.modrights .expand-all').show();
        $(this).hide();
    });

    $('.modrights .handler').on('click', function() {
        $('.modrights .expand-all').show();
        $('.modrights .collapse-all').hide();
    });


    // Настройка прав доступа к пунктам меню
    putOverlay = function(options)
    {
        var _this = this;
        this.options = options;
        this.overdiv = $(this.options.overlay);
        this.checkbox = $(this.options.checkbox);

        this.change = function()
        {
            var checked = (_this.options.checkshow) ? this.checked : !this.checked;
            if (checked) _this.showOverlay();
            else {
                _this.overdiv.hide();
            }
        }

        this.showOverlay = function()
        {
            var parentHeight = this.overdiv.parent().height();
            if (parentHeight>0) this.overdiv.height(parentHeight);
            this.overdiv.show();
        }

        this.defaultDraw = function()
        {
            //Включаем оверлей по умолчанию, если нужно
            var checked = (this.options.checkshow) ? this.checkbox.get(0).checked : !this.checkbox.get(0).checked;
            if (checked) this.showOverlay();
        }

        this.defaultDraw();
        this.checkbox.change(this.change);
    }

    new putOverlay({checkbox: '#full_user', overlay:'#user_overlay', checkshow:true});
    new putOverlay({checkbox: '#full_admin', overlay:'#admin_overlay', checkshow:true});
});