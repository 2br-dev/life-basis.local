<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:24
  from 'D:\Projects\Hosts\life-basis.local\release\modules\users\view\form\user\chpass.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3088db170_04114283',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7733ffb420df2b8c87f4add6ce133b50c23aa7dc' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\users\\view\\form\\user\\chpass.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e3088db170_04114283 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<input type="checkbox" name="changepass" value="1" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>сменить пароль<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
<?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['field']->value->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
}
