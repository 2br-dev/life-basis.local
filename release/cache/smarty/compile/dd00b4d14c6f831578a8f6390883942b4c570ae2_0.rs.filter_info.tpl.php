<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:31
  from 'D:\Projects\Hosts\life-basis.local\release\modules\externalapi\view\filter_info.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e30fc271a3_11382816',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dd00b4d14c6f831578a8f6390883942b4c570ae2' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\externalapi\\view\\filter_info.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e30fc271a3_11382816 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['filters']->value, 'filter', false, 'key');
$_smarty_tpl->tpl_vars['filter']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['filter']->value) {
$_smarty_tpl->tpl_vars['filter']->do_else = false;
?>
    <div>
        <b><?php echo $_smarty_tpl->tpl_vars['key']->value;?>
</b>, <i><?php echo $_smarty_tpl->tpl_vars['filter']->value['type'];?>
</i> - <?php echo (($tmp = $_smarty_tpl->tpl_vars['filter']->value['title'] ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['orm_object']->value["__".((string)$_smarty_tpl->tpl_vars['key']->value)]->getDescription() ?? null : $tmp);?>

        <?php if ($_smarty_tpl->tpl_vars['filter']->value['values']) {?>
            <br><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Возможные значения:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <i><?php echo implode(',',$_smarty_tpl->tpl_vars['filter']->value['values']);?>
</i>
        <?php }?>
    </div>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
