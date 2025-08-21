<?php
/* Smarty version 4.3.1, created on 2025-08-19 15:00:02
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a4674217ad86_22680712',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e4ab711bac8b56cd72755ac13590e8e5c1a6acff' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\product.tpl',
      1 => 1755604798,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/catalog/product.tpl' => 1,
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a4674217ad86_22680712 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_105038742968a4674216b429_93912696', "content");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_105038742968a4674216b429_93912696 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_105038742968a4674216b429_93912696',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/catalog/product.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	
<?php
}
}
/* {/block "content"} */
}
