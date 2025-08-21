<?php
/* Smarty version 4.3.1, created on 2025-08-19 16:24:02
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\moduleview\article\view_article.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a47af2e71321_56350034',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5a258aed31d702a1ad7ac09e31cfac290cccd1d9' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\moduleview\\article\\view_article.tpl',
      1 => 1755609838,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a47af2e71321_56350034 (Smarty_Internal_Template $_smarty_tpl) {
?><section id="news">
	<div class="container">
		<div class="row flex">
			<div class="col">
				<hgroup>
					<h1><?php echo $_smarty_tpl->tpl_vars['article']->value->title;?>
</h1>
					<div class="fogged">
						<?php echo $_smarty_tpl->tpl_vars['article']->value->short_content;?>

					</div>
				</hgroup>
			</div>
			<div class="col l6 m12">
				<?php echo $_smarty_tpl->tpl_vars['article']->value->content;?>

			</div>
			<div class="col l6 m12">
				<div class="pin">
				<?php if ($_smarty_tpl->tpl_vars['article']->value['image']) {?>
					<img data-src="<?php echo $_smarty_tpl->tpl_vars['article']->value->__image->getLink();?>
" alt="<?php echo $_smarty_tpl->tpl_vars['article']->value->title;?>
" class="news-image lazy responsive-img">
				<?php } else { ?>
					<img data-src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/no-photo.svg" alt="<?php echo $_smarty_tpl->tpl_vars['article']->value->title;?>
" class="lazy responsive-img news-image">
				<?php }?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
