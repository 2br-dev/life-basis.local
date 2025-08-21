<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:47
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\hidden.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b178ed1d5_61988189',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '914e89201ec77097e0bca5503f4bb4f2c900f8c5' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\hidden.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b178ed1d5_61988189 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('values', $_smarty_tpl->tpl_vars['field']->value->get());
if (is_array($_smarty_tpl->tpl_vars['values']->value)) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['values']->value, 'value');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?>
        <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
 />
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
} else { ?>
    <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['values']->value;?>
" <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
 />
<?php }
}
}
