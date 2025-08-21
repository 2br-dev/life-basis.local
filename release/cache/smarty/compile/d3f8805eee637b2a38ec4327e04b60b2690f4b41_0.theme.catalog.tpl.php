<?php
/* Smarty version 4.3.1, created on 2025-08-19 14:57:33
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\catalog.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a466add93182_77984765',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd3f8805eee637b2a38ec4327e04b60b2690f4b41' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\catalog.tpl',
      1 => 1755604650,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a466add93182_77984765 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>



<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_196392046168a466add84d40_16141716', "content");
?>


<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_196392046168a466add84d40_16141716 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_196392046168a466add84d40_16141716',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
?>


		<?php echo smarty_function_moduleinsert(array('name'=>"\CatalogExt\Controller\Block\ProductList"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\catalog.tpl');?>


		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php
}
}
/* {/block "content"} */
}
