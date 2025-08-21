<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:27
  from 'D:\Projects\Hosts\life-basis.local\release\modules\mobilesiteapp\view\form\menu\mobile_image.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318ab44c700_82080241',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd310a19841edcf295ce4296afc2342c8736d4b78' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\mobilesiteapp\\view\\form\\menu\\mobile_image.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318ab44c700_82080241 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['field']->value->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>&nbsp; <a href="https://ionic.io/ionicons" target="_blank"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Перейти к списку картинок<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a><?php }
}
