<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:18
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\user\column_personal_price.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e302660bf0_44023788',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '699d1b3fd24185c96c8e90c6bc7537763c301ead' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\user\\column_personal_price.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e302660bf0_44023788 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_assignInScope('user', $_smarty_tpl->tpl_vars['cell']->value->getRow());
$_smarty_tpl->_assignInScope('user_cost', unserialize(strval($_smarty_tpl->tpl_vars['user']->value['cost_id'])));?>

<table>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cell']->value->property['site_list'], 'site', false, 'site_id');
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site_id']->value => $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
?>
        <tr>
            <td><?php echo $_smarty_tpl->tpl_vars['site']->value;?>
: </td> 
            <td>
                <?php if ($_smarty_tpl->tpl_vars['user_cost']->value[$_smarty_tpl->tpl_vars['site_id']->value]) {?>
                    <?php echo $_smarty_tpl->tpl_vars['cell']->value->property['cost_list'][$_smarty_tpl->tpl_vars['user_cost']->value[$_smarty_tpl->tpl_vars['site_id']->value]];?>

                <?php } else { ?>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>- По умолчанию -<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                <?php }?>
            </td>
        </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
