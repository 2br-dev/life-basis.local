<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:43
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\property_val.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b731bd214_09454014',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '04c6d796203fbb29b28331e5b5bb016591ff4b8c' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\property_val.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/form/product/property_val_big_list.tpl' => 1,
  ),
),false)) {
function content_68a58b731bd214_09454014 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['self']->value->isListType()) {?>
    <?php if (count($_smarty_tpl->tpl_vars['self']->value->valuesArr()) > 20) {?>
        <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/form/product/property_val_big_list.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <?php } else { ?>
        <?php $_smarty_tpl->_assignInScope('values', $_smarty_tpl->tpl_vars['self']->value->valuesArr());?>
        <input type="hidden" name="prop[<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];?>
][value][]" value="" class="h-val">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['values']->value, 'oneitem', false, 'key');
$_smarty_tpl->tpl_vars['oneitem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['oneitem']->value) {
$_smarty_tpl->tpl_vars['oneitem']->do_else = false;
?>
            <span class="inline-item property-type-list">
                <input type="checkbox" name="prop[<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];?>
][value][]" class="h-val" <?php echo $_smarty_tpl->tpl_vars['disabled']->value;?>
 id="ch_<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];
echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if (is_array($_smarty_tpl->tpl_vars['value']->value) && in_array($_smarty_tpl->tpl_vars['key']->value,$_smarty_tpl->tpl_vars['value']->value)) {?>checked<?php }?>>
                <label for="ch_<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];
echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['oneitem']->value;?>
</label>
                <a class="p-remove-val">&times;</a>
            </span>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php }
} elseif ($_smarty_tpl->tpl_vars['self']->value['type'] == 'bool') {?>
    <input type="hidden" name="prop[<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];?>
][value]" value="0" class="h-val">
    <input type="checkbox" value="1" <?php if (!empty($_smarty_tpl->tpl_vars['value']->value)) {?>checked<?php }?> name="prop[<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];?>
][value]" class="h-val" <?php echo $_smarty_tpl->tpl_vars['disabled']->value;?>
>
<?php } elseif ($_smarty_tpl->tpl_vars['self']->value['type'] == 'text') {?>
    <textarea type="text" name="prop[<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];?>
][value]" class="h-val" rows="5" cols="50" <?php echo $_smarty_tpl->tpl_vars['disabled']->value;?>
><?php echo $_smarty_tpl->tpl_vars['value']->value;?>
</textarea>
<?php } else { ?>
    <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" name="prop[<?php echo $_smarty_tpl->tpl_vars['self']->value['id'];?>
][value]" class="h-val" <?php echo $_smarty_tpl->tpl_vars['disabled']->value;?>
>
<?php }
}
}
