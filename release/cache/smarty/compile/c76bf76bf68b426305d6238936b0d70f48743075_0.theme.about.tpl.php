<?php
/* Smarty version 4.3.1, created on 2025-08-19 17:32:47
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\about.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a48b0fd933e2_21850795',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c76bf76bf68b426305d6238936b0d70f48743075' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\about.tpl',
      1 => 1755613966,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/common/products.tpl' => 1,
    'rs:%THEME%/sections/about/about.tpl' => 1,
    'rs:%THEME%/sections/about/process.tpl' => 1,
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a48b0fd933e2_21850795 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php echo smarty_function_addcss(array('file'=>"pages/about.css"),$_smarty_tpl);?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_160402390368a48b0fd7f772_71796804', "content");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_160402390368a48b0fd7f772_71796804 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_160402390368a48b0fd7f772_71796804',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/products.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('header'=>"Дистрибьюторам",'tag'=>"h1",'subheader'=>"Гибко управляйте ассортиментом, расширяя продуктовую матрицу известными брендами с идеальной репутацией"), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/about/about.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/about/process.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php
}
}
/* {/block "content"} */
}
