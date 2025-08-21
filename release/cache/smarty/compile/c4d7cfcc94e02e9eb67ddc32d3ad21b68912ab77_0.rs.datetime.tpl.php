<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:24
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\datetime.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e308f0bec4_22276333',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c4d7cfcc94e02e9eb67ddc32d3ad21b68912ab77' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\datetime.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e308f0bec4_22276333 (Smarty_Internal_Template $_smarty_tpl) {
?><span class="form-inline">
    <div class="input-group">
        <input type="text" name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value->get();?>
" <?php if ($_smarty_tpl->tpl_vars['field']->value->getMaxLength() > 0) {?>maxlength="<?php echo $_smarty_tpl->tpl_vars['field']->value->getMaxLength();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
 datetime="datetime"/>
        <span class="input-group-addon"><i class="zmdi zmdi-calendar-note"></i></span>
    </div>
</span><?php }
}
