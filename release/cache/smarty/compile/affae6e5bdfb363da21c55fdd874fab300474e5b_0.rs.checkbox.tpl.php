<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:23:07
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\checkbox.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b2b847a91_77653631',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'affae6e5bdfb363da21c55fdd874fab300474e5b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\checkbox.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b2b847a91_77653631 (Smarty_Internal_Template $_smarty_tpl) {
?><input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getValue();?>
" <?php echo $_smarty_tpl->tpl_vars['cell']->value->getCellAttr();?>
><?php }
}
