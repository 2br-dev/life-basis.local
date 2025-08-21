<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:54
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\widget\bestsellers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34206ce4df7_73313309',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '03f1595df9bbf7b6ceb67f10c410487fa667214c' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\widget\\bestsellers.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%main%/widget/bestsellers_items.tpl' => 1,
  ),
),false)) {
function content_68a34206ce4df7_73313309 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addcss(array('file'=>"common/owlcarousel/owl.carousel.min.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/owlcarousel/owl.theme.default.min.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"owlcarousel/owl.carousel.min.js"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"%main%/bestsellers.css"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%main%/bestsellers.js"),$_smarty_tpl);?>


<div id="bestsellers" class="<?php if (!$_smarty_tpl->tpl_vars['error']->value && !$_smarty_tpl->tpl_vars['items']->value) {?>need-refresh<?php }?>"
        data-need-show-dialog="<?php echo $_smarty_tpl->tpl_vars['need_show_dialog']->value;?>
"
        data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"main-widget-bestsellers",'bsdo'=>"getItems"),$_smarty_tpl);?>
"
        data-url-dialog="<?php echo smarty_function_adminUrl(array('mod_controller'=>"main-widget-bestsellers",'bsdo'=>"getDialog"),$_smarty_tpl);?>
">
    <div class="bestsellers-container">
        <?php if ($_smarty_tpl->tpl_vars['items']->value) {?>
                        <?php $_smarty_tpl->_subTemplateRender("rs:%main%/widget/bestsellers_items.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        <?php }?>
    </div>

    <div class="empty-widget loading <?php if ($_smarty_tpl->tpl_vars['items']->value) {?>hidden<?php }?>">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Загрузка...<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
</div><?php }
}
