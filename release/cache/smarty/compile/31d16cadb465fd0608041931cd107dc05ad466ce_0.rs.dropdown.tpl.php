<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:21
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\action\dropdown.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a58b1188_67772029',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '31d16cadb465fd0608041931cd107dc05ad466ce' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\action\\dropdown.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318a58b1188_67772029 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.substr.php','function'=>'smarty_modifier_substr',),));
?>
<div class="btn-group">
    <a class="tool dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="zmdi zmdi-more"></i></a>
    <ul class="dropdown-menu dropdown-menu-right">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tool']->value->getItems(), 'item');
$_smarty_tpl->tpl_vars['item']->index = -1;
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['item']->index++;
$_smarty_tpl->tpl_vars['item']->first = !$_smarty_tpl->tpl_vars['item']->index;
$__foreach_item_5_saved = $_smarty_tpl->tpl_vars['item'];
?>
            <?php if (!$_smarty_tpl->tpl_vars['tool']->value->isItemHidden($_smarty_tpl->tpl_vars['item']->value)) {?>
            <li <?php if ($_smarty_tpl->tpl_vars['item']->first) {?>class="first"<?php }?>>
                <a <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value['attr'], 'val', false, 'key');
$_smarty_tpl->tpl_vars['val']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->do_else = false;
if ($_smarty_tpl->tpl_vars['key']->value[0] == '@') {
echo smarty_modifier_substr($_smarty_tpl->tpl_vars['key']->value,"1");
} else {
echo $_smarty_tpl->tpl_vars['key']->value;
}?>="<?php if ($_smarty_tpl->tpl_vars['key']->value[0] == '@') {
echo $_smarty_tpl->tpl_vars['cell']->value->getHref($_smarty_tpl->tpl_vars['val']->value);
} else {
echo $_smarty_tpl->tpl_vars['val']->value;
}?>" <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</a></li>
            <?php }?>
        <?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_5_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
</div><?php }
}
