<?php
/* Smarty version 4.3.1, created on 2025-08-19 09:25:21
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\moduleview\catalogext\productlist.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a418d17c7d03_20115652',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'edb5240e3354cbf9c99782d70356aa12eb3f6372' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\moduleview\\catalogext\\productlist.tpl',
      1 => 1755584293,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a418d17c7d03_20115652 (Smarty_Internal_Template $_smarty_tpl) {
?><section id="products">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $_smarty_tpl->tpl_vars['category']->value;?>
</h1>
			</div>
		</div>
		<div class="row flex">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
				<div class="col xl3 l4 m6 s6 xs12 margin-bottom">
					<div class="product-card">
						<div class="image-wrapper">
							<img src="<?php echo $_smarty_tpl->tpl_vars['product']->value->getMainImage()->getOriginalUrl();?>
" alt="<?php echo $_smarty_tpl->tpl_vars['product']->value->name;?>
">
						</div>
						<div class="product-info">
							<div class="title-wrapper">
								<strong><?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
</strong>
								<small><?php echo $_smarty_tpl->tpl_vars['product']->value->longtitle;?>
</small>
							</div>
							<div class="arrow-wrapper">
								<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-right.svg" alt="Подробнее">
							</div>
						</div>
						<a href="<?php echo $_smarty_tpl->tpl_vars['product']->value->getUrl();?>
" class="card-link">Подробнее</a>
					</div>
				</div>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</div>
	</div>
</section><?php }
}
