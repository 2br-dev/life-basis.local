<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:33
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\tree\path_to_current.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b09e054f4_74923671',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e0e1cf0a971b485f399edf0a213e41fccaa42dce' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\tree\\path_to_current.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b09e054f4_74923671 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tree']->value->getPathToFirst(), 'item', true);
$_smarty_tpl->tpl_vars['item']->iteration = 0;
$_smarty_tpl->tpl_vars['item']->index = -1;
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['item']->iteration++;
$_smarty_tpl->tpl_vars['item']->index++;
$_smarty_tpl->tpl_vars['item']->first = !$_smarty_tpl->tpl_vars['item']->index;
$_smarty_tpl->tpl_vars['item']->last = $_smarty_tpl->tpl_vars['item']->iteration === $_smarty_tpl->tpl_vars['item']->total;
$__foreach_item_1_saved = $_smarty_tpl->tpl_vars['item'];
?>
    <?php if (!$_smarty_tpl->tpl_vars['item']->first) {?><i class="zmdi zmdi-chevron-right"></i><?php }?>
    <?php $_smarty_tpl->_assignInScope('cell', $_smarty_tpl->tpl_vars['tree']->value->getMainColumn($_smarty_tpl->tpl_vars['item']->value));?>
    <?php if ((isset($_smarty_tpl->tpl_vars['cell']->value->property['href'])) && !$_smarty_tpl->tpl_vars['item']->last) {?><a href="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getHref();?>
" class="item call-update"><?php } else { ?><span class="item"><?php }?>
    <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['cell']->value->getBodyTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['cell']->value), 0, true);
?>
    <?php if ((isset($_smarty_tpl->tpl_vars['cell']->value->property['href'])) && !$_smarty_tpl->tpl_vars['item']->last) {?></a><?php } else { ?></span><?php }
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_1_saved;
}
if ($_smarty_tpl->tpl_vars['item']->do_else) {
?>
    <span class="item"><?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Не выбрано";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable1=ob_get_clean();
echo (($tmp = $_smarty_tpl->tpl_vars['tree']->value->options['unselectedTitle'] ?? null)===null||$tmp==='' ? $_prefixVariable1 ?? null : $tmp);?>
</span>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
