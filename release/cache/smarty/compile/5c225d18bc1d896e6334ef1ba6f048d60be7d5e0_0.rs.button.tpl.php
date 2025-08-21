<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:40
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\toolbar\button\button.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318048bed47_81719952',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5c225d18bc1d896e6334ef1ba6f048d60be7d5e0' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\toolbar\\button\\button.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318048bed47_81719952 (Smarty_Internal_Template $_smarty_tpl) {
?><a <?php if ($_smarty_tpl->tpl_vars['button']->value->getHref() != '') {?>href="<?php echo $_smarty_tpl->tpl_vars['button']->value->getHref();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['button']->value->getAttrLine();?>
><?php echo $_smarty_tpl->tpl_vars['button']->value->getTitle();?>
</a><?php }
}
