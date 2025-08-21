<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:13:51
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\catalog.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318ff86de66_15588466',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'db8f3439d177879c493e1a5594c84d1112f205ac' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\catalog.tpl',
      1 => 1755261191,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318ff86de66_15588466 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>



<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_197854036268a318ff85df51_86850185', "content");
?>


<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_197854036268a318ff85df51_86850185 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_197854036268a318ff85df51_86850185',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
?>


	<?php echo smarty_function_moduleinsert(array('name'=>"\CatalogExt\Controller\Block\ProductList"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\catalog.tpl');?>


<?php
}
}
/* {/block "content"} */
}
