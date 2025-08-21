<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:25
  from 'D:\Projects\Hosts\life-basis.local\release\modules\users\view\form\user\userfields.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3097277b1_21444161',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3c3b6ec3a6cbff981e7532512243cdb8efa39bb2' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\users\\view\\form\\user\\userfields.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e3097277b1_21444161 (Smarty_Internal_Template $_smarty_tpl) {
?></td></tr>
<?php if ($_smarty_tpl->tpl_vars['elem']->value['conf_userfields']->notEmpty()) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value['conf_userfields']->getStructure(), 'fld');
$_smarty_tpl->tpl_vars['fld']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['fld']->value) {
$_smarty_tpl->tpl_vars['fld']->do_else = false;
?>
    <tr>
        <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['fld']->value['title'];?>
</td>
        <td>
            <?php echo $_smarty_tpl->tpl_vars['elem']->value['conf_userfields']->getForm($_smarty_tpl->tpl_vars['fld']->value['alias']);?>

            <?php $_smarty_tpl->_assignInScope('errname', $_smarty_tpl->tpl_vars['elem']->value['conf_userfields']->getErrorForm($_smarty_tpl->tpl_vars['fld']->value['alias']));?>
            <?php $_smarty_tpl->_assignInScope('error', $_smarty_tpl->tpl_vars['elem']->value->getErrorsByForm($_smarty_tpl->tpl_vars['errname']->value,', '));?>
            <?php if (!empty($_smarty_tpl->tpl_vars['error']->value)) {?>
                <span class="form-error"><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</span>
            <?php }?>
        </td>
    </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
}
