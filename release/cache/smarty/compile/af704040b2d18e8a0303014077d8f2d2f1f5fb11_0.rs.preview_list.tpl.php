<?php
/* Smarty version 4.3.1, created on 2025-08-19 16:23:07
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\moduleview\article\preview_list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a47abb30af97_19338344',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'af704040b2d18e8a0303014077d8f2d2f1f5fb11' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\moduleview\\article\\preview_list.tpl',
      1 => 1755609784,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/paginator.tpl' => 1,
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a47abb30af97_19338344 (Smarty_Internal_Template $_smarty_tpl) {
?><section id="news">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $_smarty_tpl->tpl_vars['dir']->value->title;?>
</h1>
			</div>
		</div>
		<div class="row flex">
			<?php if ($_smarty_tpl->tpl_vars['list']->value) {?>
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
					<div class="col xl3 l4 m6 s12 margin-bottom">
						<div class="news-card">
							<div class="image-wrapper">
							<?php if ($_smarty_tpl->tpl_vars['item']->value['image']) {?>
								<img data-src="<?php echo $_smarty_tpl->tpl_vars['item']->value->__image->getLink();?>
" alt="<?php echo $_smarty_tpl->tpl_vars['item']->value->title;?>
" class="lazy responsive-img">
							<?php } else { ?>
								<img data-src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/no-photo.svg" alt="<?php echo $_smarty_tpl->tpl_vars['item']->value->title;?>
" class="lazy responsive-img">
							<?php }?>
							</div>
							<div class="news-title">
								<span><?php echo $_smarty_tpl->tpl_vars['item']->value->title;?>
</span>
							</div>
							<a href="<?php echo $_smarty_tpl->tpl_vars['item']->value->getUrl();?>
">Подробнее</a>
						</div>
					</div>
				<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			<?php } else { ?>

			<?php }?>
		</div>
		<div class="row">
			<div class="col">
				<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/paginator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
			</div>
		</div>
	</div>
</section>

<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
