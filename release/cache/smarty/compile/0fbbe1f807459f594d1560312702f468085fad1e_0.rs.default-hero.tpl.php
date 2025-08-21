<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:47
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\default-hero.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3180bd1c6a4_03504829',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0fbbe1f807459f594d1560312702f468085fad1e' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\default-hero.tpl',
      1 => 1755073444,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/helpers/header.tpl' => 1,
    'rs:%THEME%/helpers/footer.tpl' => 1,
  ),
),false)) {
function content_68a3180bd1c6a4_03504829 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
$_smarty_tpl->_subTemplateRender("rs:%THEME%/helpers/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('class'=>"hero"), 0, false);
?>

<main class="hero">
<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_168599558868a3180bd14d33_84818563', "content");
?>

</main>

<?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/helpers/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
/* {block "content"} */
class Block_168599558868a3180bd14d33_84818563 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_168599558868a3180bd14d33_84818563',
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
