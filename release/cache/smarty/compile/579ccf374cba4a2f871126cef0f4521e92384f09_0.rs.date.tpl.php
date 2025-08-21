<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:35
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\filter\type\date.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0b3a9774_29054048',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '579ccf374cba4a2f871126cef0f4521e92384f09' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\filter\\type\\date.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0b3a9774_29054048 (Smarty_Internal_Template $_smarty_tpl) {
?><span class="form-inline">
    <div class="input-group">
        <input type="text" name="<?php echo $_smarty_tpl->tpl_vars['fitem']->value->getName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['fitem']->value->getValue();?>
" <?php echo $_smarty_tpl->tpl_vars['fitem']->value->getAttrString();?>
 date="date" size="12">
        <span class="input-group-addon"><i class="zmdi zmdi-calendar-alt"></i></span>
    </div>
</span><?php }
}
