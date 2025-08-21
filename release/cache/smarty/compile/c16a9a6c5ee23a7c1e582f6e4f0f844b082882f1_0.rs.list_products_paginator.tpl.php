<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:10:54
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\list_products_paginator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3184e1e4598_45264972',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c16a9a6c5ee23a7c1e582f6e4f0f844b082882f1' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\list_products_paginator.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/paginator.tpl' => 1,
  ),
),false)) {
function content_68a3184e1e4598_45264972 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<div class="rs-pagination-block">
    <?php if ($_smarty_tpl->tpl_vars['paginator']->value->page < $_smarty_tpl->tpl_vars['paginator']->value->total_pages) {?>
        <div class="mt-5">
            <a class="btn btn-outline-primary col-12 rs-ajax-paginator"
               data-pagination-options='{ "appendElement":".rs-products-list", "loaderBlock":".rs-pagination-block", "replaceBrowserUrl": true}'
               data-url="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->getPageHref($_smarty_tpl->tpl_vars['paginator']->value->page+1);?>
"
            ><span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать еще<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
            </a>
        </div>
    <?php }?>
    <div class="mt-5">
        <div class="g-4 row row-cols-auto align-items-center justify-content-between">
            <?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/paginator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('class'=>" "), 0, false);
?>
            <div>
                <div class="catalog-select">
                    <div class="catalog-select__label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать по<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:</div>
                    <div class="catalog-select__options">
                        <select class="rs-list-pagesize-change">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items_on_page']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                <option value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value == $_smarty_tpl->tpl_vars['page_size']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</option>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </select>
                        <div class="catalog-select__value"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php }
}
