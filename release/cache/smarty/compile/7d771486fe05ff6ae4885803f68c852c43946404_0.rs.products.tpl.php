<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:51:09
  from 'D:\Projects\Hosts\life-basis.local\release\modules\article\view\blocks\article\products.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a321bd2273b1_73405918',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7d771486fe05ff6ae4885803f68c852c43946404' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\article\\view\\blocks\\article\\products.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/one_product.tpl' => 1,
  ),
),false)) {
function content_68a321bd2273b1_73405918 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if (!empty($_smarty_tpl->tpl_vars['products']->value)) {?>
    
        <?php echo smarty_function_addjs(array('file'=>"%catalog%/rscomponent/productslider.js"),$_smarty_tpl);?>

    
    <div class="mt-5">
        <h2><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Прикреплённые товары<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></h2>
        <div class="product-slider">
            <div class="product-slider__container">
                <div class="swiper-container swiper-products swiper-products_sm">
                    <div class="swiper-wrapper" >
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                            <div class="swiper-slide">
                                <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/one_product.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                            </div>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </div>
        </div>
    </div>
<?php }
}
}
