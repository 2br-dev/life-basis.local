<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:10:10
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\moduleview\catalogext\next-product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318229490a2_72362538',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '364b1acdfd87d11d7ea11ea299ac2d88fbde01f4' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\moduleview\\catalogext\\next-product.tpl',
      1 => 1755514889,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318229490a2_72362538 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="col t4 xs4">
	<a href="<?php echo $_smarty_tpl->tpl_vars['nextProduct']->value->getUrl();?>
">
		<img data-src="<?php echo $_smarty_tpl->tpl_vars['nextProduct']->value->getMainImage()->getOriginalUrl();?>
" alt="<?php echo $_smarty_tpl->tpl_vars['nextProduct']->value->title;?>
" class="lazy responsive-img next" title="<?php echo $_smarty_tpl->tpl_vars['nextProduct']->value->title;?>
">
	</a>
</div>
<div class="col s1 t2 xs2">
	<a href="<?php echo $_smarty_tpl->tpl_vars['nextProduct']->value->getUrl();?>
" title="<?php echo $_smarty_tpl->tpl_vars['nextProduct']->value->title;?>
" class="next-link">
		<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-right.svg" alt="<?php echo $_smarty_tpl->tpl_vars['nextImage']->value->title;?>
">
	</a>
</div><?php }
}
