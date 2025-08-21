<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:24:16
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\helpers\footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b70096ae4_77120967',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2cc54a44243d1783f4831e4fd3986656991f46f6' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\helpers\\footer.tpl',
      1 => 1755083342,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b70096ae4_77120967 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
?>
<footer>
	<div class="container">
		<div class="row">
			<div class="col"><h2>Горное величие</h2></div>
			<div class="col">
				<div class="nav-wrapper">
					<div class="menu">
						<?php echo smarty_function_moduleinsert(array('name'=>"Menu\Controller\Block\Menu"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\helpers\footer.tpl');?>

					</div>
					<div class="social-media">
						<ul>
							<li><a href="https://ok.ru"><img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/ok.svg" alt="Одноклассники"></a></li>
							<li><a href="https://vk.com"><img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/vk.svg" alt="ВКонтакте"></a></li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col">
				<a href="/" class="footer-logo">
					<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/logo-img.svg" alt="Основа жизни" height="50">
					<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/logo-text.svg" alt="Основа жизни" height="40">
				</a>
			</div>
		</div>
	</div>
</footer><?php }
}
