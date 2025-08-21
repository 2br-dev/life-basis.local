<?php
/* Smarty version 4.3.1, created on 2025-08-20 13:58:48
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\sections\catalog\product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5aa680c62e8_00312028',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1c793d275b9ee2cc334f03035784f07479083a5a' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\sections\\catalog\\product.tpl',
      1 => 1755687525,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5aa680c62e8_00312028 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
echo smarty_function_addcss(array('file'=>"pages/product.css"),$_smarty_tpl);?>


<section id="product-page">
	<div class="container">
		<div class="row">
			<div class="col">
				<div class="back-link-wrapper">
					<a href="<?php echo $_smarty_tpl->tpl_vars['product']->value->getMainDir()->getUrl();?>
" class="back-link">
						<span class="image">
							<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-left.svg" alt="Назад">
						</span>
						<span class="title">
							Назад
						</span>
					</a>
				</div>
			</div>
			<div class="col">
				<h1><?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
</h1>
			</div>
			<div class="col l6 m12">
				<?php if ($_smarty_tpl->tpl_vars['product']->value->description != null) {?>
				<div class="fogged"><?php echo $_smarty_tpl->tpl_vars['product']->value->description;?>
</div>
				<?php }?>
				<div class="image-wrapper hide-l-up">
					<div class="row flex vcenter">
						<div class="col s7 xs6 t6">
														<img data-src="<?php echo $_smarty_tpl->tpl_vars['product']->value->getMainImage()->getOriginalUrl();?>
" alt="<?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
" title="<?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
" class="lazy responsive-img current">
						</div>
												<?php echo smarty_function_moduleinsert(array('name'=>"\CatalogExt\Controller\Block\NextProduct",'currentId'=>((string)$_smarty_tpl->tpl_vars['product']->value->id)),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\sections\catalog\product.tpl');?>

					</div>
				</div>
				<h2>Характеристики воды</h2>
				<div class="row flex">
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value->properties[1]['properties'], 'property');
$_smarty_tpl->tpl_vars['property']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['property']->value) {
$_smarty_tpl->tpl_vars['property']->do_else = false;
?>
					<div class="col s6 xs6 t12">
						<div class="prop-card">
							<div class="prop-name">
								<?php echo $_smarty_tpl->tpl_vars['property']->value->title;?>

							</div>
							<div class="prop-data">
								<?php echo $_smarty_tpl->tpl_vars['property']->value->val_str;?>
 <?php echo $_smarty_tpl->tpl_vars['property']->value->unit;?>

							</div>
						</div>
					</div>
					<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</div>
			</div>
			<div class="col l6 hide-l-down">
				<div class="image-wrapper">
					<div class="row flex vcenter">
						<div class="col t7 xs7">
														<img data-src="<?php echo $_smarty_tpl->tpl_vars['product']->value->getMainImage()->getOriginalUrl();?>
" alt="<?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
" title="<?php echo $_smarty_tpl->tpl_vars['product']->value->title;?>
" class="lazy responsive-img current">
						</div>
												<?php echo smarty_function_moduleinsert(array('name'=>"\CatalogExt\Controller\Block\NextProduct",'currentId'=>((string)$_smarty_tpl->tpl_vars['product']->value->id)),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\sections\catalog\product.tpl');?>

					</div>
				</div>
			</div>
		</div>
	</div>
</section><?php }
}
