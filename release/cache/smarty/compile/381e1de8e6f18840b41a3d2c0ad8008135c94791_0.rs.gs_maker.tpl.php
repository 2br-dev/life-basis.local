<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:51:09
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\gs_maker.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a321bd533b56_35774030',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '381e1de8e6f18840b41a3d2c0ad8008135c94791' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\gs_maker.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/gs/container.tpl' => 1,
  ),
),false)) {
function content_68a321bd533b56_35774030 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if ($_smarty_tpl->tpl_vars['this_controller']->value->getDebugGroup()) {
if ($_smarty_tpl->tpl_vars['layouts']->value['grid_system'] != 'gs960') {
echo smarty_function_addcss(array('file'=>"%templates%/manager.css"),$_smarty_tpl);
}?><div id="all-containers-wrapper" class="all-containers-wrapper" data-page-id="<?php echo $_smarty_tpl->tpl_vars['layouts']->value['page_id'];?>
"data-clone-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('copyContainer',array('context'=>$_smarty_tpl->tpl_vars['layouts']->value['theme_data']['blocks_context'],'ajax'=>1),'templates-blockctrl');?>
"data-sort-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('ajaxMoveContainer',array(),'templates-blockctrl');?>
"><?php }
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layouts']->value['containers'], 'container');
$_smarty_tpl->tpl_vars['container']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['container']->value) {
$_smarty_tpl->tpl_vars['container']->do_else = false;
ob_start();
$_smarty_tpl->_subTemplateRender("rs:%system%/gs/container.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('container'=>$_smarty_tpl->tpl_vars['container']->value,'page_id'=>$_smarty_tpl->tpl_vars['layouts']->value['page_id'],'theme_context'=>$_smarty_tpl->tpl_vars['layouts']->value['theme_data']['blocks_context']), 0, true);
$_smarty_tpl->assign('wrapped_content', ob_get_clean());
if ($_smarty_tpl->tpl_vars['container']->value['outside_template']) {
$_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['container']->value['outside_template'], $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('wrapped_content'=>$_smarty_tpl->tpl_vars['wrapped_content']->value), 0, true);
} else {
echo $_smarty_tpl->tpl_vars['wrapped_content']->value;
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
if ($_smarty_tpl->tpl_vars['this_controller']->value->getDebugGroup()) {?></div><div class="container-add-block"><a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('addModule',array('type'=>$_smarty_tpl->tpl_vars['layouts']->value['max_container_type']+1,'page_id'=>$_smarty_tpl->tpl_vars['layouts']->value['page_id'],'context'=>$_smarty_tpl->tpl_vars['layouts']->value['theme_data']['blocks_context']),'templates-blockctrl');?>
"data-crud-options='{ "dialogId": "blockListDialog", "beforeCallback": "addConstructorModuleSectionId", "type": "<?php echo $_smarty_tpl->tpl_vars['layouts']->value['max_container_type']+1;?>
", "pageId": "<?php echo $_smarty_tpl->tpl_vars['layouts']->value['page_id'];?>
", "context": "<?php echo $_smarty_tpl->tpl_vars['layouts']->value['theme_data']['blocks_context'];?>
" }'class="crud-add btn btn-success" target="_blank"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>добавить блок<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></div><div class="container-add-wrapper"><a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('addContainer',array('type'=>$_smarty_tpl->tpl_vars['layouts']->value['max_container_type']+1,'page_id'=>$_smarty_tpl->tpl_vars['layouts']->value['page_id'],'context'=>$_smarty_tpl->tpl_vars['layouts']->value['theme_data']['blocks_context']),'templates-blockctrl');?>
" class="crud-add btn btn-success"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>добавить контейнер<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a><a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('copyContainer',array('type'=>$_smarty_tpl->tpl_vars['layouts']->value['max_container_type']+1,'page_id'=>$_smarty_tpl->tpl_vars['layouts']->value['page_id'],'context'=>$_smarty_tpl->tpl_vars['layouts']->value['theme_data']['blocks_context']),'templates-blockctrl');?>
" class="crud-add btn btn-default"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>добавить контейнер клонированием<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></div><?php }
}
}
