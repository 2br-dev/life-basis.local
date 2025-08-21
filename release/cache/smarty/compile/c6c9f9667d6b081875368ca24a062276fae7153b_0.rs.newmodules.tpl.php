<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:55
  from 'D:\Projects\Hosts\life-basis.local\release\modules\marketplace\view\widget\newmodules\newmodules.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34207043589_18606423',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c6c9f9667d6b081875368ca24a062276fae7153b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\marketplace\\view\\widget\\newmodules\\newmodules.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:widget/newmodules/newmodules_item.tpl' => 1,
  ),
),false)) {
function content_68a34207043589_18606423 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"%marketplace%/newmodules.js"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"%marketplace%/newmodules.css"),$_smarty_tpl);?>


<div id="mp-modules" <?php if (!$_smarty_tpl->tpl_vars['items']->value) {?>class="need-refresh"<?php }?> data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"marketplace-widget-newmodules",'mpdo'=>"getItems"),$_smarty_tpl);?>
">
    <div class="mp-container">
        <?php if ($_smarty_tpl->tpl_vars['items']->value) {?>
                        <?php $_smarty_tpl->_subTemplateRender("rs:widget/newmodules/newmodules_item.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
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
