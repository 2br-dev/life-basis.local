<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:43:31
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\checkboxlist.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31ff314a417_06314109',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c809d9341d7ee01254464abd6b7354f47e84ddbf' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\checkboxlist.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/coreobject/type/form/block_error.tpl' => 1,
  ),
),false)) {
function content_68a31ff314a417_06314109 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['field']->value->getList(), 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
    <div>
        <label><input name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['key']->value,(array)$_smarty_tpl->tpl_vars['field']->value->get())) {?>checked="checked"<?php }?>> <?php echo $_smarty_tpl->tpl_vars['item']->value;?>
</label>
    </div>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/block_error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
