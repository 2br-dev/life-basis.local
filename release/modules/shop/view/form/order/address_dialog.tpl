<div class="formbox">
    <form id="addressAddForm" method="POST" action="{urlmake}" data-city-autocomplete-url="{$router->getAdminUrl('searchCity')}" data-order-block="#addressBlockWrapper" enctype="multipart/form-data" class="crud-form" data-dialog-options='{ "width":800, "height":700 }'>
        {hook name="shop-form-order-address_dialog:form" title="{t}Редактирование заказа - диалог адреса:форма{/t}"}
            <table class="otable">
                {if $user_id}
                    <tbody class="new-address">
                        <tr>
                            <td class="otitle">{t}Изменяемый адрес{/t}:</td>
                            <td>
                                <div class="d-flex" style="gap:6px">
                                    <select name="use_addr" id="change_addr" data-url="{adminUrl do=getAddressRecord}" style="width:90%">
                                        {foreach from=$address_list item=item}
                                            <option value="{$item.id}" {if $current_address.id==$item.id}selected{/if}>{$item->getLineView()}</option>
                                        {/foreach}
                                        <option value="0">{t}Новый адрес для заказа{/t}</option>
                                    </select>
                                    <a id="addressDelete" data-url="{adminUrl do="deleteAddress"}" data-confirm-text="{t}Адрес будет удален из всех заказов, где он использовался. Вы действительно желаете удалить выбранный адрес?{/t}" class="btn btn-link {if !$address_list}hidden{/if}"><i class="zmdi zmdi-close f-22 c-red"></i></a>
                                </div>
                                <div class="fieldhelp" style="width:90%">{t}Внимание! если этот адрес используется в других заказах, то он также будет изменен.{/t}</div>
                            </td>
                        </tr>
                    </tbody>
                {else}
                    <input type="hidden" name="use_addr" value="{$order.use_addr}">
                {/if}

                <tbody class="address_part">
                    {$address_part}
                </tbody>
            </table>
        {/hook}
    </form>
    <script>
        /**
        * Получает адрес для получения подсказок для города
        */
        function getCityAutocompleteUrl()
        {
            var form   = $( "#addressCityInput" ).closest('form'); //Объект формы
            var url    = form.data('city-autocomplete-url'); //Адрес для запросов
            
            var country_id = $( "#addressCountryIdSelect" ).val();
            var region_id  = $( "#addressRegionIdSelect" ).val();
       
            url += "&country_id=" + country_id + "&region_id=" + region_id;
            return url;
        }
    
        $(function() {

            /**
             * Обновляет информацию в блоке Адрес у заказа
             *
             * @param response
             */
            var processNewAddressResponse = function(response) {
                if (response.success && response.insertBlockHTML){ //Если всё удачно и вернулся HTML для вставки в блок
                    var insertBlock = $('#addressAddForm').data('order-block');

                    $(insertBlock).html(response.insertBlockHTML).trigger('new-content');
                    $('#orderForm').data('hasChanges', 1);

                    if (typeof(response.use_addr)!='undefined'){ //Если выбран адрес доставки
                        $('input[name="use_addr"]').val(response.use_addr);
                    }
                    if (typeof(response.address)!='undefined'){ //Если выбран адрес
                        for(var m in response.address){
                            $('input[name="address[' + m + ']"]').val(response.address[m]);
                        }
                    }
                }
            }

            /**
            * Назначаем действия, если всё успешно вернулось 
            */
            $('#addressAddForm').on('crudSaveSuccess', function(event, response) {
                processNewAddressResponse(response);
            });
            
            /**
            * Смена выпадающего списка с адресами
            */
            $('#change_addr').on('change', function() {
                var addressId = $(this).val();
                $('#addressDelete').toggleClass('hidden', addressId == '0');
                $('[data-address-save]').toggleClass('hidden', addressId == '0');

                $.ajaxQuery({
                    url: $(this).data('url'),
                    data: {
                        'address_id': addressId
                    },
                    success: function(response) {
                        $('.address_part').html(response.html);
                    }
                });
            });

            /**
             * Удаляет выбранный адрес
             */
            $('#addressDelete').on('click', function() {
                if (confirm($(this).data('confirmText'))) {
                    var selectAddress = $('#change_addr');
                    var addressId = selectAddress.val();
                    var form = $(this).closest('form');
                    if (addressId > 0) {
                        $.ajaxQuery({
                            url: $(this).data('url'),
                            data: {
                                'address_id': addressId
                            },
                            success: function (response) {
                                if (response.success) {
                                    var option = $('option[value="' + addressId + '"]', selectAddress);
                                    option.next().prop('selected', true).change();
                                    option.remove();

                                    //Если удаляем выбранный в текущий момент адрес
                                    if ($('input[name="use_addr"]').val() === addressId) {
                                        $.ajaxQuery({
                                            url: form.attr('action'),
                                            method: 'post',
                                            data: {
                                                'use_addr': 0
                                            },
                                            success: function(response) {
                                                processNewAddressResponse(response);
                                            }
                                        });
                                    }
                                }
                            }
                        });
                    }
                }
            });
            
            /**
            * Автозаполнение в строке с вводом города
            */
            $( "#addressCityInput" ).each(function() {
                var url = getCityAutocompleteUrl(); //Установка адреса
                
                $(this).autocomplete({
                    source: url,
                    minLength: 3,
                    select: function( event, ui ) {
                        var region_id  = ui.item.region_id;  //Выбранный регион
                        var country_id = ui.item.country_id; //Выбранная страна
                        var zipcode    = ui.item.zipcode;    //Индекс
                        
                        //Установка индекса
                        if (!$("#addressZipcodeInput").val()){
                            $("#addressZipcodeInput").val(zipcode);
                        }
                    },
                    messages: {
                        noResults: '',
                        results: function() {}
                    }
                }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
                    ul.addClass('searchCityItems');
                    
                    return $( "<li />" )
                        .append( '<a>' + item.label + '</a>' )
                        .appendTo( ul );
                };
            });
            
            /**
            * Если меняется регион или страна в выпадающем списке
            */
            $("#addressRegionIdSelect, #addressCountryIdSelect").on('change', function(){
                var url = getCityAutocompleteUrl(); //Установка адреса
                $( "#addressCityInput" ).autocomplete('option', 'source', url);
            });
        });                                
    </script>
</div>