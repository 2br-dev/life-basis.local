<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:43
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\meta.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318075079c6_29572694',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8910fe2919f14aa4adc4915cf8fa7a833403c2cb' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\meta.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318075079c6_29572694 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['meta_vars']->value, 'tagparam');
$_smarty_tpl->tpl_vars['tagparam']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tagparam']->value) {
$_smarty_tpl->tpl_vars['tagparam']->do_else = false;
?>
<meta <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tagparam']->value, 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['value']->value,'"','&quot;');?>
" <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
