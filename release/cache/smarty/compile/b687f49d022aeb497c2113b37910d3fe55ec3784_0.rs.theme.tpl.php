<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:48:41
  from 'D:\Projects\Hosts\life-basis.local\release\modules\site\view\form\options\theme.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a32f3998f847_87708036',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b687f49d022aeb497c2113b37910d3fe55ec3784' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\site\\view\\form\\options\\theme.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a32f3998f847_87708036 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
echo smarty_function_addjs(array('file'=>((string)$_smarty_tpl->tpl_vars['elem']->value['tpl_module_folders']['mod_js'])."selecttheme.js",'basepath'=>"root"),$_smarty_tpl);?>

<?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__theme']->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__theme']), 0, true);
?>
<a id="selectTheme" class="btn btn-default va-middle"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>выбрать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>

<?php echo '<script'; ?>
>
$.allReady(function() {
    $('input[name="theme"]').selectTheme({
        dialogUrl: '<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttheme",'do'=>false),$_smarty_tpl);?>
',
        setThemeUrl: '<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttheme",'do'=>'installTheme'),$_smarty_tpl);?>
'
    })
});
<?php echo '</script'; ?>
><?php }
}
