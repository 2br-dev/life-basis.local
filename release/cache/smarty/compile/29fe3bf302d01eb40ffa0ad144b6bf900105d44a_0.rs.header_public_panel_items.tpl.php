<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:23:27
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\adminblocks\headerpanel\header_public_panel_items.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b3f5a1639_74531477',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '29fe3bf302d01eb40ffa0ad144b6bf900105d44a' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\adminblocks\\headerpanel\\header_public_panel_items.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b3f5a1639_74531477 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
<li>
    <a <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value['attr'], 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?> <?php if ($_smarty_tpl->tpl_vars['key']->value != 'icon') {
echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"<?php }?> <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>>
        <?php if ($_smarty_tpl->tpl_vars['item']->value['attr']['icon']) {?><i class="rs-icon rs-public-icon <?php echo $_smarty_tpl->tpl_vars['item']->value['attr']['icon'];?>
"><!----></i><?php }?>
        <span><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</span>
    </a>
</li>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
