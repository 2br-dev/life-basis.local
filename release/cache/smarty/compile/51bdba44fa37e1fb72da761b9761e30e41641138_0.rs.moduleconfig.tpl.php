<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:40
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\toolbar\button\moduleconfig.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31804a6b096_71746161',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '51bdba44fa37e1fb72da761b9761e30e41641138' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\toolbar\\button\\moduleconfig.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31804a6b096_71746161 (Smarty_Internal_Template $_smarty_tpl) {
?><a <?php if ($_smarty_tpl->tpl_vars['button']->value->getHref() != '') {?>href="<?php echo $_smarty_tpl->tpl_vars['button']->value->getHref();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['button']->value->getAttrLine();?>
>
    <img src="<?php echo $_smarty_tpl->tpl_vars['Setup']->value['IMG_PATH'];?>
/adminstyle/modoptions.png">
    <?php if ($_smarty_tpl->tpl_vars['button']->value->getTitle()) {?><span class="visible-xs-inline"><?php echo $_smarty_tpl->tpl_vars['button']->value->getTitle();?>
</span><?php }?></a><?php }
}
