<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:36
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\checkbox_head.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0c1552d0_37638627',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2468ea8fd054c283e8c7dd651078a051cd6e51e5' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\checkbox_head.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0c1552d0_37638627 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<div class="chkhead-block">
    <input type="checkbox" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выделить элементы на этой странице<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" class="chk_head select-page" data-name="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getName();?>
">
    <?php if ($_smarty_tpl->tpl_vars['cell']->value->property['showSelectAll']) {?>
    <div class="onover">
        <input type="checkbox" name="selectAll" value="on" class="select-all" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выделить элементы на всех страницах<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
    </div>
    <?php }?>
</div><?php }
}
