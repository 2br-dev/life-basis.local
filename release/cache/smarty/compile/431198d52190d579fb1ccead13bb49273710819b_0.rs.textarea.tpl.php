<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:34
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\filter\textarea.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b6aab2a63_64034140',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '431198d52190d579fb1ccead13bb49273710819b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\filter\\textarea.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b6aab2a63_64034140 (Smarty_Internal_Template $_smarty_tpl) {
?><textarea name="<?php echo $_smarty_tpl->tpl_vars['fitem']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['fitem']->value->getValue();?>
" <?php echo $_smarty_tpl->tpl_vars['fitem']->value->getAttrString();?>
><?php echo $_smarty_tpl->tpl_vars['fitem']->value->getValue();?>
</textarea><?php }
}
