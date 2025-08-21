<?php
/* Smarty version 4.3.1, created on 2025-08-19 14:56:06
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\stm.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a46656efccc0_01320568',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '51e52fd61e1613eac0123cbb0f7a8a4f3f5bc6fc' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\stm.tpl',
      1 => 1755604559,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/stm/stm.tpl' => 1,
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a46656efccc0_01320568 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php echo smarty_function_addcss(array('file'=>"pages/stm.css"),$_smarty_tpl);?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_154129792868a46656ef1e06_65708486', "content");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_154129792868a46656ef1e06_65708486 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_154129792868a46656ef1e06_65708486',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/stm/stm.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php
}
}
/* {/block "content"} */
}
