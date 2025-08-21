<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\property_product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b72873416_61521328',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9a80fa3540cb3873af75f44f07edc5edfa21cc67' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\property_product.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b72873416_61521328 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"jquery.rs.propertytypebiglist.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['properties']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
    <?php if ($_smarty_tpl->tpl_vars['item']->value['is_my']) {?>
        <tr class="property-item" data-property-id="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" data-is-my="1">
            <td class="item-title">
                <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][id]" class="h-id">
                <input type="hidden" value="1" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][is_my]" class="h-product_id">
                <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['xml_id'];?>
" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][xml_id]" class="h-xml_id">
                <?php echo $_smarty_tpl->tpl_vars['item']->value['title'];
if (!empty($_smarty_tpl->tpl_vars['item']->value['unit'])) {?>, <?php echo $_smarty_tpl->tpl_vars['item']->value['unit'];
}?>
            </td>
            <td class="item-info">
                <span class="hint help-icon" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Тип:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo $_smarty_tpl->tpl_vars['item']->value['__type']->textView();?>
<br>№: <?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
">?</span>
            </td>
            <td class="item-useval"></td>
            <td class="item-val">
                <?php echo $_smarty_tpl->tpl_vars['item']->value->valView();?>

            </td>
            <td class="item-public">
                <?php if ($_smarty_tpl->tpl_vars['owner_type']->value == 'group' && $_smarty_tpl->tpl_vars['item']->value['type'] != 'text') {?>
                    <input type="checkbox" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][public]" value="1" class="h-public" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отображать в поиске на сайте<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" <?php if ($_smarty_tpl->tpl_vars['item']->value['public']) {?>checked<?php }?>>
                    <input type="checkbox" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][is_expanded]" value="1" class="h-is_expanded" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отображать всегда развернутой<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" <?php if ($_smarty_tpl->tpl_vars['item']->value['is_expanded']) {?>checked<?php }?>>
                <?php }?>
            </td>
            <td class="item-tools has-tools">
                <div class="inline-tools">
                    <a title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Редактировать параметры характеристики<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" class="tool p-edit"><i class="zmdi zmdi-edit"></i></a>
                    <a title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить характеристику<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" class="tool p-del"><i class="zmdi zmdi-delete c-red"></i></a>
                </div>
            </td>
        </tr>
    <?php } else { ?>
        <tr class="property-item" data-property-id="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" data-is-my="0">
            <td class="item-title">
                <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][id]" class="h-id">
                <input type="hidden" value="0" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][is_my]" class="h-group_id">
                <input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['xml_id'];?>
" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][xml_id]" class="h-xml_id">
                <?php echo $_smarty_tpl->tpl_vars['item']->value['title'];
if (!empty($_smarty_tpl->tpl_vars['item']->value['unit'])) {?>, <?php echo $_smarty_tpl->tpl_vars['item']->value['unit'];
}?>
            </td>
            <td class="item-info">
                <span class="hint help-icon" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Тип:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo $_smarty_tpl->tpl_vars['item']->value['__type']->textView();?>
<br>№: <?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
">?</span>
            </td>
            <td class="item-useval">
                <input type="checkbox" value="1" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][usevalue]" class="h-useval" <?php if ($_smarty_tpl->tpl_vars['item']->value['useval']) {?>checked<?php }?> title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отметьте, чтобы задать персональное значение, иначе будет использоваться значение категории товара<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
            </td>
            <td class="item-val">
                <?php echo $_smarty_tpl->tpl_vars['item']->value->valView();?>

            </td>
            <td class="item-public">
                <?php if ($_smarty_tpl->tpl_vars['owner_type']->value == 'group' && $_smarty_tpl->tpl_vars['item']->value['type'] != 'text') {?>
                    <input type="checkbox" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][public]" value="1" class="h-val-linked" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отображать в поиске на сайте<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" <?php if ($_smarty_tpl->tpl_vars['item']->value['public']) {?>checked<?php }?>>
                    <input type="checkbox" name="prop[<?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
][is_expanded]" value="1" class="h-is_expanded" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отображать всегда развернутой<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" <?php if ($_smarty_tpl->tpl_vars['item']->value['is_expanded']) {?>checked<?php }?>>
                <?php }?>
            </td>
            <td class="item-tools">
                <div class="inline-tools">
                    <a title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Редактировать параметры характеристики<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" class="tool p-edit"><i class="zmdi zmdi-edit"></i></a>
                </div>
            </td>
        </tr>
    <?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
