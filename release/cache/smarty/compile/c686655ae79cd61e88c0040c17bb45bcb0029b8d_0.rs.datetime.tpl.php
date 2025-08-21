<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:48
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\datetime.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b18c75631_09019008',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c686655ae79cd61e88c0040c17bb45bcb0029b8d' => 
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
function content_68a31b18c75631_09019008 (Smarty_Internal_Template $_smarty_tpl) {
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
