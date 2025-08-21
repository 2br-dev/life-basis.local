<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:34
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\paginator\control.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0a143517_52935739',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '81128a96083caa39b9f595e957e3517619489a64' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\paginator\\control.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0a143517_52935739 (Smarty_Internal_Template $_smarty_tpl) {
?><form method="GET" action="<?php echo $_smarty_tpl->tpl_vars['pcontrol']->value->action;?>
" class="paginator <?php if (!$_smarty_tpl->tpl_vars['local_options']->value['no_ajax']) {?>form-call-update<?php }?> <?php if ($_smarty_tpl->tpl_vars['pcontrol']->value->element->isNoUpdateUrl()) {?>no-update-hash<?php }?>">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pcontrol']->value->getHiddenFields(), 'val', false, 'key');
$_smarty_tpl->tpl_vars['val']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->do_else = false;
?>
        <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
">
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    <?php echo $_smarty_tpl->tpl_vars['pcontrol']->value->element->getView($_smarty_tpl->tpl_vars['local_options']->value);?>

</form><?php }
}
