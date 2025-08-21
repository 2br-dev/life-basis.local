<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:10:13
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\one_product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318259d7a18_65534047',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '08007f9801ee919f5370381d6bedfa27dda6e0dd' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\one_product.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/product_cart_button.tpl' => 1,
  ),
),false)) {
function content_68a318259d7a18_65534047 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.hook.php','function'=>'smarty_block_hook',),));
$_smarty_tpl->_assignInScope('shop_config', \RS\Config\Loader::byModule('shop'));
$_smarty_tpl->_assignInScope('catalog_config', \RS\Config\Loader::byModule('catalog'));
$_smarty_tpl->_assignInScope('offers_data', $_smarty_tpl->tpl_vars['product']->value->getOffersJson(array('noVirtual'=>true),true));
$_smarty_tpl->_assignInScope('only_main_offer', $_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['show_offers_in_list']);?>

<div class="item-card rs-product-item
                <?php if (!$_smarty_tpl->tpl_vars['product']->value->isAvailable($_smarty_tpl->tpl_vars['only_main_offer']->value)) {?> rs-not-avaliable<?php }?>
                <?php if ($_smarty_tpl->tpl_vars['product']->value->canBeReserved()) {?> rs-can-be-reserved<?php }?>
                <?php if ($_smarty_tpl->tpl_vars['product']->value->isReservationForced()) {?> rs-forced-reserve<?php }?>"
                <?php echo $_smarty_tpl->tpl_vars['product']->value->getDebugAttributes();?>
 data-sale-status="<?php echo $_smarty_tpl->tpl_vars['product']->value->getSaleStatus();?>
" data-id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
">
    <div class="item-card__inner">
        <div class="position-relative mb-2">
            <?php $_smarty_tpl->_assignInScope('spec_dirs', $_smarty_tpl->tpl_vars['product']->value->getMySpecDir());?>
            <?php if ($_smarty_tpl->tpl_vars['spec_dirs']->value) {?>
                <div class="item-product-labels js-product-labels">
                    <ul>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['spec_dirs']->value, 'spec');
$_smarty_tpl->tpl_vars['spec']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['spec']->value) {
$_smarty_tpl->tpl_vars['spec']->do_else = false;
?>
                            <?php if ($_smarty_tpl->tpl_vars['spec']->value['is_label']) {?>
                                <li class="item-product-label item-product-label_<?php echo $_smarty_tpl->tpl_vars['spec']->value['alias'];?>
" style="color:<?php echo $_smarty_tpl->tpl_vars['spec']->value['label_text_color'];?>
; background-color: <?php echo $_smarty_tpl->tpl_vars['spec']->value['label_bg_color'];?>
; border-color: <?php echo $_smarty_tpl->tpl_vars['spec']->value['label_border_color'];?>
"><?php echo $_smarty_tpl->tpl_vars['spec']->value['name'];?>
</li>
                            <?php }?>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </ul>
                    <button class="item-product-labels-btn d-none" type="button">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.86423 4.60983C3.01653 4.46339 3.26347 4.46339 3.41577 4.60983L6 7.09467L8.58423 4.60983C8.73653 4.46339 8.98347 4.46339 9.13577 4.60983C9.28808 4.75628 9.28808 4.99372 9.13577 5.14017L6.27577 7.89017C6.12347 8.03661 5.87653 8.03661 5.72423 7.89017L2.86423 5.14017C2.71192 4.99372 2.71192 4.75628 2.86423 4.60983Z" fill="#1B1B1F"/>
                        </svg>
                    </button>
                </div>
            <?php }?>
            <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Просмотр категории продукции:изображение товара, блочный вид";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable7=ob_get_clean();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('hook', array('name'=>"catalog-list_products:blockview-image",'title'=>$_prefixVariable7,'product'=>$_smarty_tpl->tpl_vars['product']->value));
$_block_repeat=true;
echo smarty_block_hook(array('name'=>"catalog-list_products:blockview-image",'title'=>$_prefixVariable7,'product'=>$_smarty_tpl->tpl_vars['product']->value), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['product']->value->getUrl();?>
" class="item-product-img rs-to-product">
                <canvas width="268" height="268"></canvas>
                <img src="<?php echo $_smarty_tpl->tpl_vars['product']->value->getMainImage()->getUrl(268,268);?>
" srcset="<?php echo $_smarty_tpl->tpl_vars['product']->value->getMainImage()->getUrl(536,536);?>
 2x" loading="lazy" alt="<?php echo $_smarty_tpl->tpl_vars['product']->value['title'];?>
" class="rs-image">
            </a>
            <?php $_block_repeat=false;
echo smarty_block_hook(array('name'=>"catalog-list_products:blockview-image",'title'=>$_prefixVariable7,'product'=>$_smarty_tpl->tpl_vars['product']->value), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['show_rating']) {?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['product']->value->getUrl();?>
" class="item-product-reviews mb-2 rs-to-product">
                <div class="item-product-rating">
                    <img width="24" height="24" src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/icons/star<?php if ($_smarty_tpl->tpl_vars['product']->value->getRatingBall() > 0) {?>-active<?php }?>.svg" alt="">
                    <?php if ($_smarty_tpl->tpl_vars['product']->value->getRatingBall()) {?>
                        <div><?php echo $_smarty_tpl->tpl_vars['product']->value->getRatingBall();?>
</div>
                    <?php }?>
                </div>
                <div>
                    <?php if ($_smarty_tpl->tpl_vars['product']->value->getCommentsNum()) {?>
                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('n'=>$_smarty_tpl->tpl_vars['product']->value->getCommentsNum()));
$_block_repeat=true;
echo smarty_block_t(array('n'=>$_smarty_tpl->tpl_vars['product']->value->getCommentsNum()), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>%n отзывов<?php $_block_repeat=false;
echo smarty_block_t(array('n'=>$_smarty_tpl->tpl_vars['product']->value->getCommentsNum()), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                    <?php } else { ?>
                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>нет отзывов<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                    <?php }?>
                </div>
            </a>
        <?php }?>
        <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Просмотр категории продукции:название товара, блочный вид";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable8=ob_get_clean();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('hook', array('name'=>"catalog-list_products:blockview-title",'title'=>$_prefixVariable8,'product'=>$_smarty_tpl->tpl_vars['product']->value));
$_block_repeat=true;
echo smarty_block_hook(array('name'=>"catalog-list_products:blockview-title",'title'=>$_prefixVariable8,'product'=>$_smarty_tpl->tpl_vars['product']->value), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
            <a href="<?php echo $_smarty_tpl->tpl_vars['product']->value->getUrl();?>
" class="item-card__title rs-to-product"><?php echo $_smarty_tpl->tpl_vars['product']->value['title'];?>
</a>
        <?php $_block_repeat=false;
echo smarty_block_hook(array('name'=>"catalog-list_products:blockview-title",'title'=>$_prefixVariable8,'product'=>$_smarty_tpl->tpl_vars['product']->value), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
        <div class="item-product-sale-status rs-sale-status"><?php echo $_smarty_tpl->tpl_vars['product']->value->getSubstitutePriceText();?>
</div>
        <div class="item-product-price item-product-price_card rs-price-block">
            <?php $_smarty_tpl->_assignInScope('cur_cost', $_smarty_tpl->tpl_vars['product']->value->getCost());?>
            <?php $_smarty_tpl->_assignInScope('old_cost', $_smarty_tpl->tpl_vars['product']->value->getOldCost());?>

            <div class="item-product-price__new-price">
                <span class="rs-price-new"><?php echo $_smarty_tpl->tpl_vars['cur_cost']->value;?>
</span> <?php echo $_smarty_tpl->tpl_vars['product']->value->getCurrency();?>

                <?php if ($_smarty_tpl->tpl_vars['catalog_config']->value['use_offer_unit']) {?>
                    <?php $_smarty_tpl->_assignInScope('unit', $_smarty_tpl->tpl_vars['product']->value->getUnit());?>
                    <?php if (($_smarty_tpl->tpl_vars['offers_data']->value && $_smarty_tpl->tpl_vars['offers_data']->value['offers']) || $_smarty_tpl->tpl_vars['unit']->value) {?>
                        <span class="rs-unit-block">/ <span class="rs-unit"><?php echo (($tmp = $_smarty_tpl->tpl_vars['offers_data']->value['offers'][0]['unit'] ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['unit']->value->stitle ?? null : $tmp);?>
</span></span>
                    <?php }?>
                <?php }?>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['old_cost']->value && $_smarty_tpl->tpl_vars['old_cost']->value != $_smarty_tpl->tpl_vars['cur_cost']->value) {?>
                <div class="item-product-price__old-price"><span class="rs-price-old"><?php echo $_smarty_tpl->tpl_vars['old_cost']->value;?>
</span> <?php echo $_smarty_tpl->tpl_vars['product']->value->getCurrency();?>
</div>
            <?php }?>
        </div>
        <div class="row g-3 align-items-center item-card__actions">
            <div class="col-lg-auto">
                <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/product_cart_button.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
            <div class="col d-flex justify-content-center justify-content-lg-start">
                <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['enable_favorite']) {?>
                    <div class="col-6 col-lg-auto d-flex justify-content-center me-lg-3">
                        <a class="fav rs-favorite <?php if ($_smarty_tpl->tpl_vars['product']->value->inFavorite()) {?>rs-in-favorite<?php }?>" data-title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В избранное<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" data-already-title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В избранном<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.2131 5.5617L12 6.5651L12.7869 5.56171C13.5614 4.57411 14.711 4 15.9217 4C18.1262 4 20 5.89454 20 8.32023C20 10.2542 18.8839 12.6799 16.3617 15.5585C14.6574 17.5037 12.8132 19.0666 11.9999 19.7244C11.1866 19.0667 9.34251 17.5037 7.63817 15.5584C5.1161 12.6798 4 10.2542 4 8.32023C4 5.89454 5.87376 4 8.07829 4C9.28909 4 10.4386 4.57407 11.2131 5.5617ZM11.6434 20.7195L11.7113 20.6333L11.6434 20.7195Z" stroke-width="1"/>
                            </svg>
                        </a>
                    </div>
                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['enable_compare']) {?>
                    <div class="col-6 col-lg-auto d-flex justify-content-center">
                        <a class="comp rs-compare<?php if ($_smarty_tpl->tpl_vars['product']->value->inCompareList()) {?> rs-in-compare<?php }?>" data-title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>сравнить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" data-already-title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В сравнении<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
                            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.1279 18.0433V18.5433H19.6279H19.9688L19.9689 18.5433C19.9692 18.5433 19.9693 18.5433 19.97 18.5436C19.9713 18.5442 19.975 18.5462 19.9798 18.5513C19.9895 18.5616 20 18.581 20 18.6095C20 18.638 19.9895 18.6574 19.9798 18.6677C19.975 18.6728 19.9713 18.6748 19.97 18.6754C19.9693 18.6757 19.9692 18.6757 19.9689 18.6757L19.9688 18.6757H4.03125L4.03109 18.6757C4.03077 18.6757 4.03069 18.6757 4.02996 18.6754C4.02867 18.6748 4.02498 18.6728 4.02023 18.6677C4.01055 18.6574 4 18.638 4 18.6095C4 18.581 4.01055 18.5616 4.02023 18.5513C4.02498 18.5462 4.02867 18.5442 4.02996 18.5436C4.03069 18.5433 4.03077 18.5433 4.03109 18.5433L4.03125 18.5433H4.37236H4.87236V18.0433V10.7968C4.87236 10.7683 4.88291 10.7489 4.89259 10.7385C4.89734 10.7335 4.90103 10.7315 4.90232 10.7309C4.90315 10.7305 4.90314 10.7306 4.90361 10.7306H8.14403C8.14409 10.7306 8.14414 10.7306 8.14419 10.7306C8.14451 10.7306 8.14459 10.7306 8.14532 10.7309C8.14661 10.7315 8.1503 10.7335 8.15505 10.7385C8.16473 10.7489 8.17528 10.7683 8.17528 10.7968V18.0433V18.5433H8.67528H9.84867H10.3487V18.0433V8.15454C10.3487 8.12606 10.3592 8.10665 10.3689 8.09633C10.3737 8.09127 10.3773 8.08926 10.3786 8.08868C10.379 8.08852 10.3792 8.08844 10.3793 8.0884C10.3795 8.08835 10.3797 8.08836 10.3799 8.08836H13.6203C13.6208 8.08836 13.6208 8.08831 13.6216 8.08868C13.6229 8.08926 13.6266 8.09127 13.6314 8.09633C13.641 8.10665 13.6516 8.12606 13.6516 8.15454V18.0433V18.5433H14.1516H15.325H15.825V18.0433V5.51247C15.825 5.48398 15.8355 5.46457 15.8452 5.45425C15.85 5.44919 15.8537 5.44719 15.8549 5.44661C15.8553 5.44643 15.8555 5.44635 15.8557 5.44632C15.8559 5.44627 15.856 5.44629 15.8562 5.44629H19.0967L19.0968 5.44629C19.0971 5.44628 19.0972 5.44628 19.0979 5.44661C19.0992 5.44719 19.1029 5.44919 19.1077 5.45425C19.1173 5.46457 19.1279 5.48398 19.1279 5.51247V18.0433Z" />
                            </svg>
                        </a>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>

    <div class="item-card__wrapper rs-offers-preview"></div>
    <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['show_offers_in_list']) {?>
        <?php if ($_smarty_tpl->tpl_vars['offers_data']->value) {?>
            <?php echo '<script'; ?>
 rel="offers" type="application/json" data-check-quantity="<?php echo $_smarty_tpl->tpl_vars['shop_config']->value->check_quantity;?>
"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'json_encode' ][ 0 ], array( $_smarty_tpl->tpl_vars['offers_data']->value,320 ));
echo '</script'; ?>
>
        <?php }?>
    <?php }?>
</div><?php }
}
