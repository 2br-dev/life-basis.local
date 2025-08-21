<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:10:10
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\default.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31822407147_68858240',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd770eba797648ed782a139df3665670da0eedfb4' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\default.tpl',
      1 => 1755083817,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/helpers/header.tpl' => 1,
    'rs:%THEME%/helpers/footer.tpl' => 1,
  ),
),false)) {
function content_68a31822407147_68858240 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
$_smarty_tpl->_subTemplateRender("rs:%THEME%/helpers/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<main>
<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_106805594968a318223ff900_85148460', "content");
?>

</main>

<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/helpers/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
/* {block "content"} */
class Block_106805594968a318223ff900_85148460 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_106805594968a318223ff900_85148460',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<?php echo $_smarty_tpl->tpl_vars['app']->value->blocks->getMainContent();?>

<?php
}
}
/* {/block "content"} */
}
