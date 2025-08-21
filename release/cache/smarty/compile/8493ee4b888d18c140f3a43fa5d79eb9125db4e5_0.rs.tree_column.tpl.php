<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:21
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\admin\tree_column.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a5503ff8_85546502',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8493ee4b888d18c140f3a43fa5d79eb9125db4e5' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\admin\\tree_column.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318a5503ff8_85546502 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
$_smarty_tpl->_assignInScope('type', $_smarty_tpl->tpl_vars['cell']->value->getRow()->getTypeObject());?>
<a class="type zmdi <?php echo $_smarty_tpl->tpl_vars['type']->value->getIconClass();?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('title'=>$_smarty_tpl->tpl_vars['type']->value->getTitle()));
$_block_repeat=true;
echo smarty_block_t(array('title'=>$_smarty_tpl->tpl_vars['type']->value->getTitle()), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Тип:%title<?php $_block_repeat=false;
echo smarty_block_t(array('title'=>$_smarty_tpl->tpl_vars['type']->value->getTitle()), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">&nbsp;</a>
<a href="<?php echo smarty_function_adminUrl(array('do'=>"edit",'id'=>$_smarty_tpl->tpl_vars['cell']->value->getRow('id')),$_smarty_tpl);?>
" class="edit crud-edit<?php if (!$_smarty_tpl->tpl_vars['cell']->value->getRow('public')) {?> c-gray<?php }?>" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нажмите, чтобы отредактировать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><?php echo $_smarty_tpl->tpl_vars['cell']->value->getValue();?>
</a>
<?php }
}
