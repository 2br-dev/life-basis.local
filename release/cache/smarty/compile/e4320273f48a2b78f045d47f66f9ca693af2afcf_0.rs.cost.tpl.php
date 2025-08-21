<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:40
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\cost.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b70b39621_15258468',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e4320273f48a2b78f045d47f66f9ca693af2afcf' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\cost.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b70b39621_15258468 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<table class="otable">
<?php echo $_smarty_tpl->tpl_vars['elem']->value->calculateUserCost();?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value->getCostList(), 'onecost');
$_smarty_tpl->tpl_vars['onecost']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['onecost']->value) {
$_smarty_tpl->tpl_vars['onecost']->do_else = false;
?>
    <tr>
        <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['onecost']->value['title'];?>
</td>
        <td>
            <input type="text" name="excost[<?php echo $_smarty_tpl->tpl_vars['onecost']->value['id'];?>
][cost_original_val]" value="<?php if ($_smarty_tpl->tpl_vars['onecost']->value['type'] != 'auto') {
echo $_smarty_tpl->tpl_vars['elem']->value['excost'][$_smarty_tpl->tpl_vars['onecost']->value['id']]['cost_original_val'];
}?>" <?php if ($_smarty_tpl->tpl_vars['onecost']->value['type'] == 'auto') {?>disabled<?php }?>>

            <?php if ($_smarty_tpl->tpl_vars['onecost']->value['type'] == 'manual') {?>
                <select name="excost[<?php echo $_smarty_tpl->tpl_vars['onecost']->value['id'];?>
][cost_original_currency]">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value->getCurrencies(), 'curr', false, 'id');
$_smarty_tpl->tpl_vars['curr']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['id']->value => $_smarty_tpl->tpl_vars['curr']->value) {
$_smarty_tpl->tpl_vars['curr']->do_else = false;
?>
                        <option value="<?php echo $_smarty_tpl->tpl_vars['id']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['elem']->value['excost'][$_smarty_tpl->tpl_vars['onecost']->value['id']]['cost_original_currency'] == $_smarty_tpl->tpl_vars['id']->value) {?>selected<?php }?>><?php echo $_smarty_tpl->tpl_vars['curr']->value;?>
</option>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </select>
            <?php } else { ?>
                <span class="m-l-10 help-icon" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Автовычесляемое поле, будет расчитано после сохранения<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">?</span>
            <?php }?>
        </td>
    </tr>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
