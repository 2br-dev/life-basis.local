<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\property_group_product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b7268d619_31684074',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '09053d451998d2f9b75af49094f3e7f18dc707c7' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\property_group_product.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/form/product/property_product.tpl' => 1,
  ),
),false)) {
function content_68a58b7268d619_31684074 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<tbody class="group-body" data-gid="<?php echo (($tmp = $_smarty_tpl->tpl_vars['group']->value['group']['id'] ?? null)===null||$tmp==='' ? "0" ?? null : $tmp);?>
">
    <tr class="property-group noover">
        <td colspan="6"><div class="back"><?php if ($_smarty_tpl->tpl_vars['group']->value['group']['id'] > 0) {
echo $_smarty_tpl->tpl_vars['group']->value['group']['title'];
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Без группы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?></div></td>
    </tr>
</tbody>
<tbody data-group-id="<?php echo (($tmp = $_smarty_tpl->tpl_vars['group']->value['group']['id'] ?? null)===null||$tmp==='' ? "0" ?? null : $tmp);?>
">
    <?php if (!empty($_smarty_tpl->tpl_vars['group']->value['properties'])) {?>
    <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/form/product/property_product.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('properties'=>$_smarty_tpl->tpl_vars['group']->value['properties']), 0, false);
?>
    <?php }?>
</tbody><?php }
}
