<?php
/* Smarty version 4.3.1, created on 2025-08-20 09:10:39
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\sections\contacts\contacts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a566dfe5ea97_23295683',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9ecc2b147a31a61a3088ef8e84edb841230186cf' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\sections\\contacts\\contacts.tpl',
      1 => 1755670233,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a566dfe5ea97_23295683 (Smarty_Internal_Template $_smarty_tpl) {
?><section id="contacts">
	<div class="container">
		<div class="row flex">
			<div class="col xl5 l6 m12">
				<h1>Давайте начнём дело</h1>
				<p class="fogged">
					Мы так же преданы вашему опыту, как и чистоте каждой бутылки. Чтобы поддерживать 
					наши высокие стандарты качества, мы опираемся на наше тесное сотрудничество с вами. 
					Свяжитесь с нами: мы будем рады помочь вам в любых вопросах.
				</p>
				<div class="row">
					<div class="col s6 xs12">
						<div class="contact-wrapper">
							<div class="title">Телефон</div>
							<div class="contact">
								<a href="tel:+79186030703">
									<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/whatsapp.svg" alt="WhatsApp">
									<span>+7 (918) 603-07-03</span>
								</a>
							</div>
						</div>
					</div>
					<div class="col s6 xs12">
						<div class="contact-wrapper">
							<div class="title">Почта</div>
							<div class="contact">
								<a href="mailto:osnovazhizni@mail.ru">
									<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/mail.svg" alt="Электронная почта">
									<span>osnovazhizni@mail.ru</span>
								</a>
							</div>
						</div>
					</div>
					<div class="col">
						<div class="contact-wrapper">
							<div class="title">Адрес</div>
							<div class="contact">
								<a href="https://yandex.ru/maps/-/CHtIBNlC">
									<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/map.svg" alt="Адрес">
									<span>КЧР, Урупский район, с. Курджиново, ул. Гагарина, 49a/1</span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col">
						<div class="qr-wrapper hide-l-down">
							<div class="img-wrapper">
								<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/contacts-qr.svg" alt="Открыть чат WhatsApp">
							</div>
							<div class="data-wrapper">
								<p>Сканируйте QR код, чтобы начать чат в WhatsApp</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col l6 m12 offset-xl1">
				<div id="map" 
					data-lat="43.959829" 
					data-lon="40.959700" 
					data-zoom="13" 
					data-linktext="улица Гагарина, 49a/1" 
					data-link="https://yandex.ru/maps/-/CHtIBNlC"
					>
				</div>
			</div>
		</div>
	</div>
</section>

<?php }
}
