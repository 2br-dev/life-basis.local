<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:31
  from 'D:\Projects\Hosts\life-basis.local\release\modules\externalapi\view\sort_info.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e30fd741a5_87162797',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7a1fe6d43430c02afa9a8a638064cb479c17ad0b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\externalapi\\view\\sort_info.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e30fd741a5_87162797 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sort_fields']->value, 'field');
$_smarty_tpl->tpl_vars['field']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['field']->value) {
$_smarty_tpl->tpl_vars['field']->do_else = false;
?>
    <div>
        <b><?php echo $_smarty_tpl->tpl_vars['field']->value;?>
</b>
    </div>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
