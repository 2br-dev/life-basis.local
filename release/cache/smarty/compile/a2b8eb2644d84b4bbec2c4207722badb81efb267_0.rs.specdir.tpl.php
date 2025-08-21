<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:41
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\specdir.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b719b9c81_13739108',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a2b8eb2644d84b4bbec2c4207722badb81efb267' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\specdir.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b719b9c81_13739108 (Smarty_Internal_Template $_smarty_tpl) {
?><div>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value->getSpecDirs(), 'spec');
$_smarty_tpl->tpl_vars['spec']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['spec']->value) {
$_smarty_tpl->tpl_vars['spec']->do_else = false;
?>
    <input type="checkbox" name="xspec[<?php echo $_smarty_tpl->tpl_vars['spec']->value['id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['spec']->value['id'];?>
" id="spec_<?php echo $_smarty_tpl->tpl_vars['spec']->value['id'];?>
" <?php if (is_array($_smarty_tpl->tpl_vars['elem']->value['xspec']) && in_array($_smarty_tpl->tpl_vars['spec']->value['id'],$_smarty_tpl->tpl_vars['elem']->value['xspec'])) {?>checked<?php }?>>
    <label for="spec_<?php echo $_smarty_tpl->tpl_vars['spec']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['spec']->value['name'];?>
</label><br>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</div><?php }
}
