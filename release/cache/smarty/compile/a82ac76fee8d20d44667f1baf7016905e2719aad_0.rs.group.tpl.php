<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:18
  from 'D:\Projects\Hosts\life-basis.local\release\modules\users\view\form\filter\group.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e302501656_47218130',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a82ac76fee8d20d44667f1baf7016905e2719aad' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\users\\view\\form\\filter\\group.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e302501656_47218130 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_assignInScope('groups', $_smarty_tpl->tpl_vars['cell']->value->getRow()->getUserGroups(false));
if (!empty($_smarty_tpl->tpl_vars['groups']->value)) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'group', true);
$_smarty_tpl->tpl_vars['group']->iteration = 0;
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
$_smarty_tpl->tpl_vars['group']->iteration++;
$_smarty_tpl->tpl_vars['group']->last = $_smarty_tpl->tpl_vars['group']->iteration === $_smarty_tpl->tpl_vars['group']->total;
$__foreach_group_1_saved = $_smarty_tpl->tpl_vars['group'];
?>
       <?php echo $_smarty_tpl->tpl_vars['group']->value['name'];
if (!$_smarty_tpl->tpl_vars['group']->last) {?>, <?php }?>
    <?php
$_smarty_tpl->tpl_vars['group'] = $__foreach_group_1_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
} else { ?>
    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Без группы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
}
}
