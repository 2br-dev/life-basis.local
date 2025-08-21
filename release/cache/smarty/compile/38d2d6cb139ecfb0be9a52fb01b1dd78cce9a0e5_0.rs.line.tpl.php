<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:34
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\filter\line.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0aea9e58_36662461',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '38d2d6cb139ecfb0be9a52fb01b1dd78cce9a0e5' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\filter\\line.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0aea9e58_36662461 (Smarty_Internal_Template $_smarty_tpl) {
?><fieldset>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['fline']->value->getItems(), 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
        <div class="form-group">
            <?php echo $_smarty_tpl->tpl_vars['item']->value->getView();?>

        </div>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</fieldset><?php }
}
