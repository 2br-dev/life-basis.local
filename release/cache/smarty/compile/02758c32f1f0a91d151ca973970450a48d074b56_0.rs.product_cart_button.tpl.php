<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:10:14
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\product_cart_button.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31826377a34_94529904',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '02758c32f1f0a91d151ca973970450a48d074b56' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\product_cart_button.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/helper/svg/reserve.tpl' => 2,
    'rs:%THEME%/helper/svg/minus.tpl' => 1,
    'rs:%THEME%/helper/svg/plus.tpl' => 1,
    'rs:%THEME%/helper/svg/hand.tpl' => 1,
  ),
),false)) {
function content_68a31826377a34_94529904 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.hook.php','function'=>'smarty_block_hook',),));
ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Кнопка в корзину:кнопка";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable9=ob_get_clean();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('hook', array('name'=>"catalog-product_cart_button:button",'title'=>$_prefixVariable9,'product'=>$_smarty_tpl->tpl_vars['product']->value,'shop_config'=>$_smarty_tpl->tpl_vars['shop_config']->value,'offers_data'=>$_smarty_tpl->tpl_vars['offers_data']->value));
$_block_repeat=true;
echo smarty_block_hook(array('name'=>"catalog-product_cart_button:button",'title'=>$_prefixVariable9,'product'=>$_smarty_tpl->tpl_vars['product']->value,'shop_config'=>$_smarty_tpl->tpl_vars['shop_config']->value,'offers_data'=>$_smarty_tpl->tpl_vars['offers_data']->value), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
if ($_smarty_tpl->tpl_vars['shop_config']->value) {?>
    <?php if (!$_smarty_tpl->tpl_vars['product']->value['disallow_manually_add_to_cart']) {?>
        <?php $_smarty_tpl->_assignInScope('has_any_offers', ($_smarty_tpl->tpl_vars['offers_data']->value['offers'] && count($_smarty_tpl->tpl_vars['offers_data']->value['offers']) > 1) || ($_smarty_tpl->tpl_vars['offers_data']->value['levels'] && !$_smarty_tpl->tpl_vars['offers_data']->value['virtual']));?>
        <?php if ($_smarty_tpl->tpl_vars['has_any_offers']->value || !$_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['button_as_amount']) {?>
                        <div class="item-product-cart-action" <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['show_offers_in_list']) {?>data-sol<?php }?>>
                <button type="button" class="btn btn-primary primary-svg w-100 rs-buy rs-to-cart" data-add-text="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавлено<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"
                        data-href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('shop-front-cartpage',array("add"=>$_smarty_tpl->tpl_vars['product']->value['id']));?>
"
                        <?php if (!$_smarty_tpl->tpl_vars['disable_multioffer_dialog']->value && $_smarty_tpl->tpl_vars['has_any_offers']->value) {?>data-select-multioffer-href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('shop-front-multioffers',array("product_id"=>$_smarty_tpl->tpl_vars['product']->value['id']));?>
"<?php }?>>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/icons/to-cart-white.svg" alt="">
                    <span class="ms-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В корзину<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                </button>

                <a data-href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('shop-front-reservation',array("product_id"=>$_smarty_tpl->tpl_vars['product']->value['id']));?>
"
                   <?php if (!$_smarty_tpl->tpl_vars['disable_multioffer_dialog']->value && $_smarty_tpl->tpl_vars['has_any_offers']->value) {?>data-select-multioffer-href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('shop-front-multioffers',array("product_id"=>$_smarty_tpl->tpl_vars['product']->value['id']));?>
"<?php }?>
                   class="w-100 btn btn-outline-primary outline-primary-svg rs-reserve">
                    <?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/helper/svg/reserve.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                    <span class="ms-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Заказать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                </a>
                <div class="item-card__not-available btn btn-outline-danger rs-unobtainable"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет в наличии<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
                <div class="item-card__not-available rs-bad-offer-error"></div>
            </div>
        <?php } else { ?>
                        <?php $_smarty_tpl->_assignInScope('sale_status', $_smarty_tpl->tpl_vars['product']->value->getSaleStatus());?>
            <?php if ($_smarty_tpl->tpl_vars['product']->value->shouldReserve() || ($_smarty_tpl->tpl_vars['product']->value->canBeReserved() && $_smarty_tpl->tpl_vars['sale_status']->value == 'no_cost') || $_smarty_tpl->tpl_vars['sale_status']->value == 'on_request') {?>
                <div class="item-product-cart-action">
                    <a data-href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('shop-front-reservation',array("product_id"=>$_smarty_tpl->tpl_vars['product']->value['id']));?>
"
                       class="w-100 btn btn-outline-primary outline-primary-svg rs-reserve">
                        <?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/helper/svg/reserve.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                        <span class="ms-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Заказать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                    </a>
                </div>
            <?php } else { ?>
                <?php if (($_smarty_tpl->tpl_vars['shop_config']->value->check_quantity && $_smarty_tpl->tpl_vars['product']->value->getNum() <= 0) || in_array($_smarty_tpl->tpl_vars['sale_status']->value,array('discontinued','no_cost'))) {?>
                    <div class="item-product-cart-action">
                        <div class="item-card__not-available btn btn-outline-danger rs-unobtainable"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет в наличии<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
                    </div>
                <?php } else { ?>
                    <div class="item-product-cart-action rs-sa <?php if ($_smarty_tpl->tpl_vars['product']->value->inCart()) {?>item-product-cart-action_amount<?php }?>"
                         data-amount-params='<?php echo $_smarty_tpl->tpl_vars['product']->value->getAmountParamsJson();?>
'
                         data-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('shop-front-cartpage',array('Act'=>'changeAmount'));?>
">
                        <div class="item-product-cart-action__to-cart">
                            <button type="button" class="btn btn-primary primary-svg w-100 rs-to-cart rs-no-modal-cart"
                                    data-href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('shop-front-cartpage',array("add"=>$_smarty_tpl->tpl_vars['product']->value['id']));?>
">
                                <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/icons/to-cart-white.svg" alt="">
                                <span class="ms-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В корзину<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                            </button>
                        </div>
                        <div class="item-product-cart-action__amount">
                            <div class="item-product-amount">
                                <button class="item-product-amount__prev rs-sa-dec" type="button">
                                    <?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/helper/svg/minus.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                                </button>
                                <div class="item-product-amount__input">
                                    <input type="number" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->getAmountInCart();?>
" class="rs-sa-input">
                                    <span class="fs-6 ms-1"><?php echo $_smarty_tpl->tpl_vars['product']->value->getUnit()->stitle;?>
</span>
                                </div>
                                <button class="item-product-amount__next rs-sa-inc" type="button">
                                    <?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/helper/svg/plus.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php }?>
            <?php }?>
        <?php }?>
    <?php }
} elseif ($_smarty_tpl->tpl_vars['catalog_config']->value['buyinoneclick']) {?>
    <div class="item-product-cart-action rs-script-base">
        <a data-href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('catalog-front-oneclick',array("product_id"=>$_smarty_tpl->tpl_vars['product']->value['id']));?>
"
           class="btn btn-primary primary-svg w-100 rs-buy-one-click rs-in-dialog">
            <?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/helper/svg/hand.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            <span class="ms-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Купить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
        </a>
        <div class="item-card__not-available btn btn-outline-danger rs-unobtainable"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет в наличии<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
    </div>
<?php }
$_block_repeat=false;
echo smarty_block_hook(array('name'=>"catalog-product_cart_button:button",'title'=>$_prefixVariable9,'product'=>$_smarty_tpl->tpl_vars['product']->value,'shop_config'=>$_smarty_tpl->tpl_vars['shop_config']->value,'offers_data'=>$_smarty_tpl->tpl_vars['offers_data']->value), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
}
