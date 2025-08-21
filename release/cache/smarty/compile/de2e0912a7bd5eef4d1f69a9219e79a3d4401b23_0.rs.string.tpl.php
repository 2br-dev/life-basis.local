<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:25
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\string.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a9c62816_49454980',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'de2e0912a7bd5eef4d1f69a9219e79a3d4401b23' => 
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
function content_68a318a9c62816_49454980 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('attr', $_smarty_tpl->tpl_vars['field']->value->getAttrArray());?>
<input name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value->get();?>
" <?php if ($_smarty_tpl->tpl_vars['field']->value->getMaxLength() > 0) {?>maxlength="<?php echo $_smarty_tpl->tpl_vars['field']->value->getMaxLength();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
 <?php if (!$_smarty_tpl->tpl_vars['attr']->value['type']) {?>type="text"<?php }?>/>
<?php $_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/block_error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
