<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:52
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\tr_form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318c40752c0_98860865',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '07a52490b7b22601ac8e21684e51b3856a42f906' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\tr_form.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318c40752c0_98860865 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('groups', $_smarty_tpl->tpl_vars['prop']->value->getGroups(false,$_smarty_tpl->tpl_vars['switch']->value));
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'data', false, 'i');
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['items'], 'item', false, 'name');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
        <?php if (is_a($_smarty_tpl->tpl_vars['item']->value,'RS\Orm\Type\UserTemplate')) {?>
            {include file=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getRenderTemplate() field=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}
            <?php $_smarty_tpl->_assignInScope('issetUserTemplate', true);?>
        <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['item']->value->isHidden()) {?>
            {include file=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getRenderTemplate() field=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}
        <?php }?>                        
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php if (!$_smarty_tpl->tpl_vars['issetUserTemplate']->value) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'data', false, 'i');
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['items'], 'item', false, 'name');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
            <?php if (!$_smarty_tpl->tpl_vars['item']->value->isHidden()) {?>
            
            <tr>
                <td class="otitle">{$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getTitle()}&nbsp;&nbsp;{if $elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getHint() != ''}<a class="help-icon" title="{$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getHint()|escape}">?</a>{/if}
                </td>
                <td>{include file=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getRenderTemplate() field=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}</td>
            </tr>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
}
