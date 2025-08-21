<?php
/* Smarty version 4.3.1, created on 2025-08-20 09:12:01
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\main.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a56731521742_30479999',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '99c9cbe922777fbeaadf4ce420280c1bfa5adc96' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\main.tpl',
      1 => 1755611376,
      2 => 'theme',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/sections/main/hero.tpl' => 1,
    'rs:%THEME%/sections/main/production.tpl' => 1,
    'rs:%THEME%/sections/main/crystal.tpl' => 1,
    'rs:%THEME%/sections/common/products.tpl' => 1,
    'rs:%THEME%/sections/main/meet.tpl' => 1,
    'rs:%THEME%/sections/common/coop.tpl' => 1,
  ),
),false)) {
function content_68a56731521742_30479999 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.devnull.php','function'=>'smarty_modifier_devnull',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
echo smarty_function_addcss(array('file'=>"/pages/main.css"),$_smarty_tpl);?>


<?php echo smarty_modifier_devnull($_smarty_tpl->tpl_vars['app']->value->title->addSection('Главная — Основа жизни | Оптовая продажа питьевой воды',0,'replace'));?>




<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_170288166368a567315050e5_58735848', "content");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "%THEME%/default.tpl");
}
/* {block "content"} */
class Block_170288166368a567315050e5_58735848 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_170288166368a567315050e5_58735848',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/main/hero.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/main/production.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/main/crystal.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/products.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('header'=>"Наши продукты",'tag'=>"h2"), 0, false);
?>

		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/main/meet.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	
		<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/sections/common/coop.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	

<?php
}
}
/* {/block "content"} */
}
