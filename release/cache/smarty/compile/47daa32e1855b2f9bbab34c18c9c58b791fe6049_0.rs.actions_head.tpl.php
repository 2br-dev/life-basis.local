<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:36
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\actions_head.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0c4f8462_68533865',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '47daa32e1855b2f9bbab34c18c9c58b791fe6049' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\actions_head.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0c4f8462_68533865 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if ($_smarty_tpl->tpl_vars['cell']->value->property['SettingsUrl']) {?>
<a data-url="<?php echo $_smarty_tpl->tpl_vars['cell']->value->property['SettingsUrl'];?>
" class="options crud-add crud-sm-dialog" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройка таблицы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
    <i class="zmdi zmdi-settings"></i>
</a>
<?php }
}
}
