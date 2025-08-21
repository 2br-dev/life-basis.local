<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:21
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\action\abstract.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a5773f70_81478868',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bc5cb0a0eab1b6513fbfd792ab94d54eab922bd8' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\action\\abstract.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318a5773f70_81478868 (Smarty_Internal_Template $_smarty_tpl) {
?><a href="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getHref($_smarty_tpl->tpl_vars['tool']->value->getHrefPattern());?>
" title="<?php echo $_smarty_tpl->tpl_vars['tool']->value->getTitle();?>
" class="tool <?php echo $_smarty_tpl->tpl_vars['tool']->value->getClass();?>
" <?php echo $_smarty_tpl->tpl_vars['cell']->value->getLineAttr($_smarty_tpl->tpl_vars['tool']->value);?>
>
<?php if ($_smarty_tpl->tpl_vars['tool']->value->getIconClass()) {?>
    <i class="zmdi zmdi-<?php echo $_smarty_tpl->tpl_vars['tool']->value->getIconClass();?>
"></i>
<?php }?>
</a><?php }
}
