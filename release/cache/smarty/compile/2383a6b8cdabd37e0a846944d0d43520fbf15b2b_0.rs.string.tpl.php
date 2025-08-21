<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:24
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\string.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e30866d590_93745191',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2383a6b8cdabd37e0a846944d0d43520fbf15b2b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\string.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/coreobject/type/form/block_error.tpl' => 1,
  ),
),false)) {
function content_68a5e30866d590_93745191 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('attr', $_smarty_tpl->tpl_vars['field']->value->getAttrArray());?>
<input name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value->get();?>
" <?php if ($_smarty_tpl->tpl_vars['field']->value->getMaxLength() > 0) {?>maxlength="<?php echo $_smarty_tpl->tpl_vars['field']->value->getMaxLength();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
 <?php if (!$_smarty_tpl->tpl_vars['attr']->value['type']) {?>type="text"<?php }?>/>
<?php $_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/block_error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
