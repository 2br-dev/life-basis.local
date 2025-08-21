<?php
/* Smarty version 4.3.1, created on 2025-08-19 09:18:31
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\moduleview\catalogext\product-slider.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a41737bb7d74_26390952',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '397d832f1ba9d50510459d3f9f85e6ad301ffb73' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\moduleview\\catalogext\\product-slider.tpl',
      1 => 1755584303,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a41737bb7d74_26390952 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="row">
	<div class="col">
		<div class="swiper" id="product-slider">
			<div class="swiper-wrapper">
				<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
					<div class="swiper-slide">
						<div class="product-card">
							<div class="image-wrapper">
								<?php if ($_smarty_tpl->tpl_vars['product']->value->image != '') {?>
								<img data-src="<?php echo $_smarty_tpl->tpl_vars['product']->value->getMainImage()->getOriginalUrl();?>
" alt="<?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
" class="responsive-img lazy">
								<?php }?>
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
		<div class="swiper-pagination" id="product-pagination"></div>
	</div>
</div><?php }
}
