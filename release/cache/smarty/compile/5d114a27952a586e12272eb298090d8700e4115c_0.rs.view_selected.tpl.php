<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:50
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\dialog\view_selected.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b1a4924b8_54513112',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5d114a27952a586e12272eb298090d8700e4115c' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\dialog\\view_selected.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b1a4924b8_54513112 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addcss(array('file'=>"%catalog%/selectproduct.css",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%catalog%/selectproduct.js?v=1.0",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.tableimage.js",'basepath'=>"common"),$_smarty_tpl);?>


<?php ob_start();
echo rand(1000,9999);
$_prefixVariable6=ob_get_clean();
$_smarty_tpl->_assignInScope('self_uniq', "uniq".$_prefixVariable6);?>
<div class="product-group-container <?php echo $_smarty_tpl->tpl_vars['self_uniq']->value;?>
 <?php if ($_smarty_tpl->tpl_vars['hide_group_checkbox']->value) {?>hide-group-cb<?php }?> <?php if ($_smarty_tpl->tpl_vars['hide_product_checkbox']->value) {?>hide-product-cb<?php }?>" data-urls='{ "getChild": "<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-dialog",'do'=>"getChildCategory"),$_smarty_tpl);?>
", "getProducts": "<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-dialog",'do'=>"getProducts"),$_smarty_tpl);?>
", "getDialog": "<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-dialog",'do'=>false),$_smarty_tpl);?>
" }'>
    <a class="btn btn-success select-button"><i class="zmdi zmdi-plus"></i> <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выбрать товары<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></a><br>
    <div class="selected-container">
        <ul class="group-block">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['productArr']->value['group'], 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <li class="group" val="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
"><a class="remove" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить из списка<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">&#215;</a>
                    <span class="group_icon" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>категория товаров<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></span>
                    <span class="value"><?php echo $_smarty_tpl->tpl_vars['extdata']->value['group'][$_smarty_tpl->tpl_vars['item']->value]['obj']['name'];?>
</span>
                    <?php if ((isset($_smarty_tpl->tpl_vars['plugin_options']->value['additionalItemHtml']))) {?>
                        <?php echo str_replace(array('%field_name%','%item_id%','%type%'),array($_smarty_tpl->tpl_vars['fieldName']->value,$_smarty_tpl->tpl_vars['item']->value,'groups'),$_smarty_tpl->tpl_vars['plugin_options']->value['additionalItemHtml']);?>

                    <?php }?>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
        <ul class="product-block">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['productArr']->value['product'], 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <li class="product" val="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
">
                    <a class="remove" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить из списка<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">&#215;</a>
                    <span class="product_icon" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>товар<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></span>
                    <?php if ((isset($_smarty_tpl->tpl_vars['extdata']->value['product'][$_smarty_tpl->tpl_vars['item']->value]))) {?>
                        <span class="product_image cell-image" data-preview-url="<?php echo $_smarty_tpl->tpl_vars['extdata']->value['product'][$_smarty_tpl->tpl_vars['item']->value]['obj']->getMainImage()->getUrl(200,200);?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['extdata']->value['product'][$_smarty_tpl->tpl_vars['item']->value]['obj']->getMainImage()->getUrl(30,30);?>
" alt=""/></span>
                    <?php } else { ?>
                        <span class="product_image cell-image"></span>
                    <?php }?>
                    <span class="barcode"><?php echo $_smarty_tpl->tpl_vars['extdata']->value['product'][$_smarty_tpl->tpl_vars['item']->value]['obj']['barcode'];?>
</span>
                    <span class="value"><?php if (!(isset($_smarty_tpl->tpl_vars['extdata']->value['product'][$_smarty_tpl->tpl_vars['item']->value]))) {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Товар был удален<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
} else {
echo $_smarty_tpl->tpl_vars['extdata']->value['product'][$_smarty_tpl->tpl_vars['item']->value]['obj']['title'];
}?></span>
                    <?php if ((isset($_smarty_tpl->tpl_vars['plugin_options']->value['additionalItemHtml']))) {?>
                        <?php echo str_replace(array('%field_name%','%item_id%','%type%'),array($_smarty_tpl->tpl_vars['fieldName']->value,$_smarty_tpl->tpl_vars['item']->value,'products'),$_smarty_tpl->tpl_vars['plugin_options']->value['additionalItemHtml']);?>

                    <?php }?>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
    </div>

    <div class="input-container" data-field-name="<?php echo $_smarty_tpl->tpl_vars['fieldName']->value;?>
">
        <div class="dirs">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['productArr']->value['group'], 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <input type="hidden" data-catids="<?php echo $_smarty_tpl->tpl_vars['extdata']->value['group'][$_smarty_tpl->tpl_vars['item']->value]['parents'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
" class="hiddenCategory" name="<?php echo $_smarty_tpl->tpl_vars['fieldName']->value;?>
[group][]">
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
        <div class="products">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['productArr']->value['product'], 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <input type="hidden" data-catids="<?php echo $_smarty_tpl->tpl_vars['extdata']->value['product'][$_smarty_tpl->tpl_vars['item']->value]['dirs'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
" class="hiddenProduct" name="<?php echo $_smarty_tpl->tpl_vars['fieldName']->value;?>
[product][]">
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
>
    $.allReady(function() {
        $('.product-group-container.<?php echo $_smarty_tpl->tpl_vars['self_uniq']->value;?>
').selectProduct( <?php echo json_encode($_smarty_tpl->tpl_vars['plugin_options']->value);?>
 );

        <?php if ((isset($_smarty_tpl->tpl_vars['plugin_options']->value['additionalItemHtml_values']))) {?>
        var additionalItemHtml_values = <?php echo json_encode($_smarty_tpl->tpl_vars['plugin_options']->value['additionalItemHtml_values']);?>
;
        $.each(additionalItemHtml_values, function(index, value) {
            var elem = $(index);
            if (elem.attr('type') == 'checkbox') {
                elem.prop('checked', value);
            } else {
                elem.val(value);
            }
        });
        <?php }?>
    });
<?php echo '</script'; ?>
><?php }
}
