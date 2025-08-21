<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:25
  from 'D:\Projects\Hosts\life-basis.local\release\modules\users\view\form\user\groups.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e309562b14_53380039',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '62d369e18d9bb84745165450d0cf9198cafce24b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\users\\view\\form\\user\\groups.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e309562b14_53380039 (Smarty_Internal_Template $_smarty_tpl) {
?><table class="otable">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value['groups'], 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
        <tr>
            <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
 (<?php echo $_smarty_tpl->tpl_vars['item']->value['alias'];?>
)</td>
            <td><input type="checkbox" name="groups[]" value="<?php echo $_smarty_tpl->tpl_vars['item']->value['alias'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['alias'] == $_smarty_tpl->tpl_vars['elem']->value->getDefaultGroup() || $_smarty_tpl->tpl_vars['item']->value['alias'] == $_smarty_tpl->tpl_vars['elem']->value->getAuthorizedGroup()) {?>disabled<?php }?> <?php if (in_array($_smarty_tpl->tpl_vars['item']->value['alias'],$_smarty_tpl->tpl_vars['elem']->value['usergroup'])) {?>checked<?php }?>/></td>
        </tr>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table><?php }
}
