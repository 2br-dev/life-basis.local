<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:17:36
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\checkbox.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a319e0c81320_90790988',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c1a935ac91f771e2029379e882142600a47caaa0' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\checkbox.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/coreobject/type/form/block_error.tpl' => 1,
  ),
),false)) {
function content_68a319e0c81320_90790988 (Smarty_Internal_Template $_smarty_tpl) {
?><input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value->getCheckboxParam('on');?>
" <?php if ($_smarty_tpl->tpl_vars['field']->value->get() == $_smarty_tpl->tpl_vars['field']->value->getCheckboxParam('on')) {?>checked<?php }?> <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
>
<?php $_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/block_error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
