<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:32
  from 'D:\Projects\Hosts\life-basis.local\release\modules\externalapi\view\update_data_info.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3100a5dc8_13705871',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b5095531e454e05779b544a02da2915003675d97' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\externalapi\\view\\update_data_info.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%externalapi%/update_data_info.tpl' => 1,
  ),
),false)) {
function content_68a5e3100a5dc8_13705871 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<ul><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data_scheme']->value, 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
if ($_smarty_tpl->tpl_vars['key']->value[0] != '@') {
if ($_smarty_tpl->tpl_vars['item']->value['@is_node']) {?><li><b>[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]</b><?php if ($_smarty_tpl->tpl_vars['item']->value['@require']) {?>, <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>обязательный<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
if ($_smarty_tpl->tpl_vars['item']->value['@title']) {?>, <?php echo $_smarty_tpl->tpl_vars['item']->value['@title'];
}
$_smarty_tpl->_subTemplateRender("rs:%externalapi%/update_data_info.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('data_scheme'=>$_smarty_tpl->tpl_vars['item']->value), 0, true);
?></li><?php } else { ?><li><b>[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]</b><?php if ($_smarty_tpl->tpl_vars['item']->value['@type']) {?>, <i><?php echo $_smarty_tpl->tpl_vars['item']->value['@type'];?>
</i><?php }
if ($_smarty_tpl->tpl_vars['item']->value['@arrayitemtype']) {?> of <?php echo $_smarty_tpl->tpl_vars['item']->value['@arrayitemtype'];
}
if ($_smarty_tpl->tpl_vars['item']->value['@require']) {?>, <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>обязательный<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
if ($_smarty_tpl->tpl_vars['item']->value['@title']) {?>, <?php echo $_smarty_tpl->tpl_vars['item']->value['@title'];
}
if ($_smarty_tpl->tpl_vars['item']->value['@allowable_values']) {?><br><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>возможные значения:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?><ul><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value['@allowable_values'], 'value');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?><li><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</li><?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></ul><?php }?></li><?php }
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></ul><?php }
}
