<div class="formbox" >
                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#rs-orm-formobject-tab0" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(0)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab1" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(1)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab2" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(2)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab3" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(3)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab4" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(4)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab5" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(5)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab6" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(6)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab7" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(7)}</a></li>
                    <li class=""><a data-target="#rs-orm-formobject-tab8" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(8)}</a></li>
                </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="rs-orm-formobject-tab0" role="tabpanel">
                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__grid_system->getTitle()}&nbsp;&nbsp;{if $elem.__grid_system->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__grid_system->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__grid_system->getRenderTemplate() field=$elem.__grid_system}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab1" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__catalog_view_type->getTitle()}&nbsp;&nbsp;{if $elem.__catalog_view_type->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__catalog_view_type->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__catalog_view_type->getRenderTemplate() field=$elem.__catalog_view_type}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__catalog_nesting_type->getTitle()}&nbsp;&nbsp;{if $elem.__catalog_nesting_type->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__catalog_nesting_type->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__catalog_nesting_type->getRenderTemplate() field=$elem.__catalog_nesting_type}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__catalog_with_images->getTitle()}&nbsp;&nbsp;{if $elem.__catalog_with_images->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__catalog_with_images->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__catalog_with_images->getRenderTemplate() field=$elem.__catalog_with_images}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__sticky_header->getTitle()}&nbsp;&nbsp;{if $elem.__sticky_header->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__sticky_header->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__sticky_header->getRenderTemplate() field=$elem.__sticky_header}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__catalog_enable_designer_column->getTitle()}&nbsp;&nbsp;{if $elem.__catalog_enable_designer_column->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__catalog_enable_designer_column->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__catalog_enable_designer_column->getRenderTemplate() field=$elem.__catalog_enable_designer_column}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__category_item_resolution_count->getTitle()}&nbsp;&nbsp;{if $elem.__category_item_resolution_count->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__category_item_resolution_count->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__category_item_resolution_count->getRenderTemplate() field=$elem.__category_item_resolution_count}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab2" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__product_card_view_type->getTitle()}&nbsp;&nbsp;{if $elem.__product_card_view_type->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__product_card_view_type->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__product_card_view_type->getRenderTemplate() field=$elem.__product_card_view_type}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__product_tabs_view_type->getTitle()}&nbsp;&nbsp;{if $elem.__product_tabs_view_type->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__product_tabs_view_type->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__product_tabs_view_type->getRenderTemplate() field=$elem.__product_tabs_view_type}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__filter_view_variant->getTitle()}&nbsp;&nbsp;{if $elem.__filter_view_variant->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__filter_view_variant->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__filter_view_variant->getRenderTemplate() field=$elem.__filter_view_variant}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__buyinoneclick->getTitle()}&nbsp;&nbsp;{if $elem.__buyinoneclick->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__buyinoneclick->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__buyinoneclick->getRenderTemplate() field=$elem.__buyinoneclick}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__tabs_on_new_page->getTitle()}&nbsp;&nbsp;{if $elem.__tabs_on_new_page->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__tabs_on_new_page->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__tabs_on_new_page->getRenderTemplate() field=$elem.__tabs_on_new_page}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__show_rating->getTitle()}&nbsp;&nbsp;{if $elem.__show_rating->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_rating->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__show_rating->getRenderTemplate() field=$elem.__show_rating}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__review_enabled->getTitle()}&nbsp;&nbsp;{if $elem.__review_enabled->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__review_enabled->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__review_enabled->getRenderTemplate() field=$elem.__review_enabled}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__price_like_slider->getTitle()}&nbsp;&nbsp;{if $elem.__price_like_slider->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__price_like_slider->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__price_like_slider->getRenderTemplate() field=$elem.__price_like_slider}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__button_as_amount->getTitle()}&nbsp;&nbsp;{if $elem.__button_as_amount->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__button_as_amount->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__button_as_amount->getRenderTemplate() field=$elem.__button_as_amount}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__show_offers_in_list->getTitle()}&nbsp;&nbsp;{if $elem.__show_offers_in_list->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_offers_in_list->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__show_offers_in_list->getRenderTemplate() field=$elem.__show_offers_in_list}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__enable_compare->getTitle()}&nbsp;&nbsp;{if $elem.__enable_compare->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enable_compare->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__enable_compare->getRenderTemplate() field=$elem.__enable_compare}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__enable_favorite->getTitle()}&nbsp;&nbsp;{if $elem.__enable_favorite->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enable_favorite->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__enable_favorite->getRenderTemplate() field=$elem.__enable_favorite}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__enable_short_description_in_product_card->getTitle()}&nbsp;&nbsp;{if $elem.__enable_short_description_in_product_card->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enable_short_description_in_product_card->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__enable_short_description_in_product_card->getRenderTemplate() field=$elem.__enable_short_description_in_product_card}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__product_zoom_variant->getTitle()}&nbsp;&nbsp;{if $elem.__product_zoom_variant->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__product_zoom_variant->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__product_zoom_variant->getRenderTemplate() field=$elem.__product_zoom_variant}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__show_product_stock->getTitle()}&nbsp;&nbsp;{if $elem.__show_product_stock->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_product_stock->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__show_product_stock->getRenderTemplate() field=$elem.__show_product_stock}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__category_description_place->getTitle()}&nbsp;&nbsp;{if $elem.__category_description_place->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__category_description_place->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__category_description_place->getRenderTemplate() field=$elem.__category_description_place}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab3" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                    <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__show_modal_cart->getTitle()}&nbsp;&nbsp;{if $elem.__show_modal_cart->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_modal_cart->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__show_modal_cart->getRenderTemplate() field=$elem.__show_modal_cart}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__buy_one_click_in_cart->getTitle()}&nbsp;&nbsp;{if $elem.__buy_one_click_in_cart->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__buy_one_click_in_cart->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__buy_one_click_in_cart->getRenderTemplate() field=$elem.__buy_one_click_in_cart}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__show_unselected_concomitant_in_cart->getTitle()}&nbsp;&nbsp;{if $elem.__show_unselected_concomitant_in_cart->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_unselected_concomitant_in_cart->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__show_unselected_concomitant_in_cart->getRenderTemplate() field=$elem.__show_unselected_concomitant_in_cart}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__enable_discount_coupons->getTitle()}&nbsp;&nbsp;{if $elem.__enable_discount_coupons->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enable_discount_coupons->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__enable_discount_coupons->getRenderTemplate() field=$elem.__enable_discount_coupons}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab4" role="tabpanel">
                                                                                                                                                                                                                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__top_category_with_images->getTitle()}&nbsp;&nbsp;{if $elem.__top_category_with_images->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__top_category_with_images->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__top_category_with_images->getRenderTemplate() field=$elem.__top_category_with_images}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__top_category_mobile_view->getTitle()}&nbsp;&nbsp;{if $elem.__top_category_mobile_view->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__top_category_mobile_view->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__top_category_mobile_view->getRenderTemplate() field=$elem.__top_category_mobile_view}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab5" role="tabpanel">
                                                                                                                                                                                                                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__use_personal_account->getTitle()}&nbsp;&nbsp;{if $elem.__use_personal_account->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__use_personal_account->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__use_personal_account->getRenderTemplate() field=$elem.__use_personal_account}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__recurring_show_methods_menu->getTitle()}&nbsp;&nbsp;{if $elem.__recurring_show_methods_menu->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__recurring_show_methods_menu->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__recurring_show_methods_menu->getRenderTemplate() field=$elem.__recurring_show_methods_menu}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab6" role="tabpanel">
                                                                                                                                                                                                                                                                                                                <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__default_phone->getTitle()}&nbsp;&nbsp;{if $elem.__default_phone->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_phone->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__default_phone->getRenderTemplate() field=$elem.__default_phone}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__default_email->getTitle()}&nbsp;&nbsp;{if $elem.__default_email->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_email->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__default_email->getRenderTemplate() field=$elem.__default_email}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__default_address->getTitle()}&nbsp;&nbsp;{if $elem.__default_address->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__default_address->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__default_address->getRenderTemplate() field=$elem.__default_address}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab7" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_primary->getTitle()}&nbsp;&nbsp;{if $elem.__color_primary->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_primary->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_primary->getRenderTemplate() field=$elem.__color_primary}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_dark->getTitle()}&nbsp;&nbsp;{if $elem.__color_dark->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_dark->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_dark->getRenderTemplate() field=$elem.__color_dark}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_light->getTitle()}&nbsp;&nbsp;{if $elem.__color_light->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_light->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_light->getRenderTemplate() field=$elem.__color_light}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_very_light->getTitle()}&nbsp;&nbsp;{if $elem.__color_very_light->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_very_light->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_very_light->getRenderTemplate() field=$elem.__color_very_light}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_border->getTitle()}&nbsp;&nbsp;{if $elem.__color_border->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_border->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_border->getRenderTemplate() field=$elem.__color_border}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_btn_text->getTitle()}&nbsp;&nbsp;{if $elem.__color_btn_text->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_btn_text->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_btn_text->getRenderTemplate() field=$elem.__color_btn_text}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_link->getTitle()}&nbsp;&nbsp;{if $elem.__color_link->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_link->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_link->getRenderTemplate() field=$elem.__color_link}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__color_link_hover->getTitle()}&nbsp;&nbsp;{if $elem.__color_link_hover->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__color_link_hover->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__color_link_hover->getRenderTemplate() field=$elem.__color_link_hover}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__menubar_bg->getTitle()}&nbsp;&nbsp;{if $elem.__menubar_bg->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__menubar_bg->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__menubar_bg->getRenderTemplate() field=$elem.__menubar_bg}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__menubar_link->getTitle()}&nbsp;&nbsp;{if $elem.__menubar_link->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__menubar_link->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__menubar_link->getRenderTemplate() field=$elem.__menubar_link}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__footer_bg->getTitle()}&nbsp;&nbsp;{if $elem.__footer_bg->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__footer_bg->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__footer_bg->getRenderTemplate() field=$elem.__footer_bg}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__subfooter_bg->getTitle()}&nbsp;&nbsp;{if $elem.__subfooter_bg->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__subfooter_bg->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__subfooter_bg->getRenderTemplate() field=$elem.__subfooter_bg}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__footer_link->getTitle()}&nbsp;&nbsp;{if $elem.__footer_link->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__footer_link->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__footer_link->getRenderTemplate() field=$elem.__footer_link}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__footer_text->getTitle()}&nbsp;&nbsp;{if $elem.__footer_text->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__footer_text->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__footer_text->getRenderTemplate() field=$elem.__footer_text}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="rs-orm-formobject-tab8" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__body_increased_width->getTitle()}&nbsp;&nbsp;{if $elem.__body_increased_width->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__body_increased_width->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__body_increased_width->getRenderTemplate() field=$elem.__body_increased_width}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__show_cookie_use_policy->getTitle()}&nbsp;&nbsp;{if $elem.__show_cookie_use_policy->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__show_cookie_use_policy->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__show_cookie_use_policy->getRenderTemplate() field=$elem.__show_cookie_use_policy}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__force_white_background_category->getTitle()}&nbsp;&nbsp;{if $elem.__force_white_background_category->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__force_white_background_category->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__force_white_background_category->getRenderTemplate() field=$elem.__force_white_background_category}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__force_white_background_brand->getTitle()}&nbsp;&nbsp;{if $elem.__force_white_background_brand->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__force_white_background_brand->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__force_white_background_brand->getRenderTemplate() field=$elem.__force_white_background_brand}</td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle">{$elem.__enable_jquery->getTitle()}&nbsp;&nbsp;{if $elem.__enable_jquery->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__enable_jquery->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__enable_jquery->getRenderTemplate() field=$elem.__enable_jquery}</td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                    </form>
    </div>
    </div>