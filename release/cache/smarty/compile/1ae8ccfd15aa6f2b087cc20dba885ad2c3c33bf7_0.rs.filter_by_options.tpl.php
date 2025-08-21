<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:22:59
  from 'D:\Projects\Hosts\life-basis.local\release\modules\modcontrol\view\admin\filter_by_options.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a32933a11de1_68396611',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1ae8ccfd15aa6f2b087cc20dba885ad2c3c33bf7' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\modcontrol\\view\\admin\\filter_by_options.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a32933a11de1_68396611 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<a data-url="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('searchOptions');?>
" data-crud-dialog-width="900" class="find-button m-l-20 crud-add"><i class="zmdi zmdi-fullscreen"></i><span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Поиск по настройкам<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span> </a><?php }
}
