<?php
/* Smarty version 4.3.1, created on 2025-08-20 14:26:14
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\sections\main\meet.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5b0d6045a31_80069921',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c6bc4368983772898c1b714048e415a3fa7a5605' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\sections\\main\\meet.tpl',
      1 => 1755689168,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5b0d6045a31_80069921 (Smarty_Internal_Template $_smarty_tpl) {
?><section id="production">
	<div class="container">
		<div class="row flex vcenter">
			<div class="col xs8">
				<hgroup>
					<h2>Знакомьтесь с нашим производством</h2>
				</hgroup>
			</div>
			<div class="col xs4">
				<div class="slider-navi">
					<a href="#!" class="meet-prev slider-nav-button"><img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-left.svg" alt="Назад"></a>
					<a href="#!" class="meet-next slider-nav-button"><img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-right.svg" alt="Вперёд"></a>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="swiper" id="production-slider">
					<div class="swiper-wrapper">
						<div class="swiper-slide">
							<div class="production-card">
								<div class="image-wrapper"><img class="lazy responsive-img" data-src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/control.webp" alt="Постоянный мониторинг параметров производства"></div>
								<div class="product-title">Постоянный мониторинг параметров производства</div>
							</div>
						</div>
						<div class="swiper-slide">
							<div class="production-card">
								<div class="image-wrapper"><img class="lazy responsive-img" data-src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/bottles.webp" alt="Тара от проверенных производителей"></div>
								<div class="product-title">Тара от проверенных производителей</div>
							</div>
						</div>
						<div class="swiper-slide">
							<div class="production-card">
								<div class="image-wrapper"><img class="lazy responsive-img" data-src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/pack.webp" alt="Максимальное внимание ко всем уровням упаковки"></div>
								<div class="product-title">Максимальное внимание ко всем уровням упаковки</div>
							</div>
						</div>
						<div class="swiper-slide">
							<div class="production-card">
								<div class="image-wrapper"><img class="lazy responsive-img" data-src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/safe.webp" alt="Особое внимание к качеству и безопасности"></div>
								<div class="product-title">Особое внимание к качеству и безопасности</div>
							</div>
						</div>
						<div class="swiper-slide">
							<div class="production-card">
								<div class="image-wrapper"><img class="lazy responsive-img" data-src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/equipment.webp" alt="Современное оборудование"></div>
								<div class="product-title">Современное оборудование</div>
							</div>
						</div>
					</div>
					<div class="swiper-pagination" id="production-pagination"></div>
				</div>
			</div>
		</div>
	</div>
</section><?php }
}
