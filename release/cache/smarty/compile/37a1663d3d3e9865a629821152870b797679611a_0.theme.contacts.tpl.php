<?php
/* Smarty version 4.3.1, created on 2025-08-18 17:58:42
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\contacts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a33fa2d4fc20_93584811',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '37a1663d3d3e9865a629821152870b797679611a' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\contacts.tpl',
      1 => 1755084937,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/contacts/contacts.tpl' => 1,
  ),
),false)) {
function content_68a33fa2d4fc20_93584811 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.devnull.php','function'=>'smarty_modifier_devnull',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
echo smarty_function_addcss(array('file'=>"/pages/contacts.css"),$_smarty_tpl);?>


<?php echo smarty_modifier_devnull($_smarty_tpl->tpl_vars['app']->value->title->addSection('Контакты — Основа жизни | Оптовая продажа питьевой воды',0,'replace'));?>




<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_167647358968a33fa2d46033_04247115', "content");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_167647358968a33fa2d46033_04247115 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_167647358968a33fa2d46033_04247115',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


	<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/contacts/contacts.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php
}
}
/* {/block "content"} */
}
