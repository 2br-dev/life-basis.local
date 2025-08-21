<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:49
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\object_select.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b19370374_56735455',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fefdda941f968a60cb690563841953cc91cbbd64' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\object_select.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b19370374_56735455 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),));
echo smarty_function_addjs(array('file'=>"jquery.rs.objectselect.js?v=1",'basepath'=>"common"),$_smarty_tpl);?>


<span class="form-inline">
    <div class="input-group">
        <input type="text" data-name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" class="object-select" <?php if (!empty($_smarty_tpl->tpl_vars['field']->value->get())) {?> value="<?php echo $_smarty_tpl->tpl_vars['field']->value->getPublicTitle();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
 data-request-url="<?php echo $_smarty_tpl->tpl_vars['field']->value->getRequestUrl();?>
">
        <?php if ($_smarty_tpl->tpl_vars['field']->value->get() > 0) {?><input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value->get();?>
"><?php }?>
        <span class="input-group-addon"><i class="zmdi zmdi-<?php echo $_smarty_tpl->tpl_vars['field']->value->getIconClass();?>
"></i></span>
    </div>
</span><?php }
}
