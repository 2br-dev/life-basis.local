<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:34
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\admin\tree_item_cell.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b6a85a539_82242389',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a4c20789c796cfc83cca6fef08607dd22dd19d07' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\admin\\tree_item_cell.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b6a85a539_82242389 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['cell']->value->getValue();?>
 <sup class="sup-text"><?php echo $_smarty_tpl->tpl_vars['cell']->value->getRow('itemcount');?>
</sup><?php }
}
