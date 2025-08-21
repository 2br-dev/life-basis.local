<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:51
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\form\menu\type_form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318c3e8fa43_43459949',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '81a7c2c5530a1e8d3a4124dbc5897cb5a2e32eba' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\form\\menu\\type_form.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318c3e8fa43_43459949 (Smarty_Internal_Template $_smarty_tpl) {
?><tr>
    <td><?php if ($_smarty_tpl->tpl_vars['changeType']->value) {
echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxBefore();
}?></td>
    <td><?php echo $_smarty_tpl->tpl_vars['type_object']->value->getDescription();?>
</td>
</tr>
<?php echo $_smarty_tpl->tpl_vars['type_object']->value->getFormHtml();?>

<tr>
    <td colspan="2"><?php if ($_smarty_tpl->tpl_vars['changeType']->value) {
echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxAfter();
}?></td>
</tr><?php }
}
