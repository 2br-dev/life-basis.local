<?php
/* Smarty version 4.3.1, created on 2025-08-19 15:00:33
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\logistics.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a46761e4a889_75614810',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5160b2351f6edf4578393bb879fd3679f9c0eb26' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\logistics.tpl',
      1 => 1755604831,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/logistics/logistics.tpl' => 1,
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a46761e4a889_75614810 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php echo smarty_function_addcss(array('file'=>"pages/logistics.css"),$_smarty_tpl);?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_60501745068a46761e40ac6_72326657', "content");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_60501745068a46761e40ac6_72326657 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_60501745068a46761e40ac6_72326657',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/logistics/logistics.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php
}
}
/* {/block "content"} */
}
