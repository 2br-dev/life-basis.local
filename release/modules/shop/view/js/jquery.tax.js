    /**
    * Plugin, активирующий вкладку "характеристики" у товаров
    */
(function($){
    $.fn.ratesBlock = function(method) {
        var defaults = {
            addButton: '.actions .add',
            collapseButton: '.close',
            saveButton: '.addRate',
            successText: '.successText',
            form: '.form',
            onlyCountry: '#onlyCountries',
            fields: {
                regions: '[name="region_id_list[]"]',
                rate: '.p-rate'
            },
            rateContainer: '#rateItems',
            rateItem: '.rateItem',
            remove: '.p-del',
        },
        args = arguments;

        return this.each(function() {
            var $this = $(this),
                data = $this.data('ratesBlock');

            var methods = {
                init: function(initoptions) {
                    if (data) return;
                    data = {}; $this.data('ratesBlock', data);
                    data.opt = $.extend({}, defaults, initoptions);

                    $this
                        .on('click', data.opt.addButton, toggleForm)
                        .on('click', data.opt.collapseButton, toggleForm)
                        .on('click', data.opt.remove, removeItem)
                        .on('click', data.opt.saveButton, append);

                    $(data.opt.onlyCountry, $this).change(checkOnlyCountry);
                    checkOnlyCountry();
                }
            }

            //private
            var toggleForm = function() {
                $(data.opt.form, $this).toggle();
                $this.trigger('contentSizeChanged');
            },

            checkOnlyCountry = function() {
                var onlyCountries = $(data.opt.onlyCountry, $this).is(':checked');
                $('option.region', $(data.opt.fields.regions, $this)).toggle(!onlyCountries);
            },

            append = function() {
                var region_fields = $(data.opt.fields.regions, $this);
                if (!region_fields.val()) {
                    alert(lang.t('Отметьте хотя бы одно местоположение'));
                    return false;
                }
                var rate = $(data.opt.fields.rate).val();
                var container = $(data.opt.rateContainer, $this);

                region_fields.each(function() {
                    var region_name = '';
                    var regionElement = $(this).parents().first();
                    var pathParts = regionElement.find('.tree-select_selected-value-item_title-path-part').map(function() {
                        return $(this).text();
                    }).get();
                    var endPart = regionElement.find('.tree-select_selected-value-item_title-end-part').text();
                    if (endPart) {
                        pathParts.push(endPart);
                    }
                    region_name = pathParts.join(' > ');

                    var region_id = $(this).attr('value');
                    var data = {
                        'region_id': region_id,
                        'region': region_name,
                        'rate': rate
                    };
                    $('.tree-select_selected-value-item[data-id="'+region_id+'"]').remove();
                    $(tmpl('tmpl-rate-line', data)).appendTo(container);
                });
                $('.tree-select_selected-values').html('<li class="tree-select_selected-value-stub">- Ничего не выбрано -</li>');

                $(data.opt.successText, $this).fadeIn(function() {
                    var _this = this;
                    setTimeout(function() {
                        $(_this).fadeOut();
                    }, 5000);
                });
                toggleForm();
                
            },
            removeItem = function() {
               $(this).closest(data.opt.rateItem).remove();
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
    $('#ratesBlock').ratesBlock();
});