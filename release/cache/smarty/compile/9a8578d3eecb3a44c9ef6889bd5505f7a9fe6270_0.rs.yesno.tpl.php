<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:34
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\yesno.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b6aefd012_43991066',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9a8578d3eecb3a44c9ef6889bd5505f7a9fe6270' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\yesno.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b6aefd012_43991066 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['cell']->value->getValue()) {
$_smarty_tpl->_assignInScope('on', ' on');
}?>
<div <?php echo $_smarty_tpl->tpl_vars['cell']->value->getAttr(array('class'=>$_smarty_tpl->tpl_vars['on']->value));?>
>
    <label class="ts-helper"></label>
</div>
<?php }
}
