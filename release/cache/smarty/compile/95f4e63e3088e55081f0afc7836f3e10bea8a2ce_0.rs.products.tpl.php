<?php
/* Smarty version 4.3.1, created on 2025-08-19 09:35:55
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\sections\common\products.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a41b4be53f90_60191279',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '95f4e63e3088e55081f0afc7836f3e10bea8a2ce' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\sections\\common\\products.tpl',
      1 => 1755585031,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a41b4be53f90_60191279 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
?>
<section>
	<div class="container">
		<div class="row flex vcenter">
			<div class="col xs8">
				<hgroup>
					<<?php echo $_smarty_tpl->tpl_vars['tag']->value;?>
><?php echo $_smarty_tpl->tpl_vars['header']->value;?>
</<?php echo $_smarty_tpl->tpl_vars['tag']->value;?>
>
					<?php if ($_smarty_tpl->tpl_vars['subheader']->value != null) {?>
						<p class="fogged"><?php echo $_smarty_tpl->tpl_vars['subheader']->value;?>
</p>
					<?php }?>
				</hgroup>
			</div>
			<div class="col xs4">
				<div class="slider-navi">
					<a href="#!" class="production-prev slider-nav-button"><img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-left.svg" alt="Назад"></a>
					<a href="#!" class="production-next slider-nav-button"><img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-right.svg" alt="Вперёд"></a>
				</div>
			</div>
		</div>
		<div class="row">
			<?php echo smarty_function_moduleinsert(array('name'=>"\CatalogExt\Controller\Block\ProductSlider"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\sections\common\products.tpl');?>

		</div>
	</div>
</section><?php }
}
