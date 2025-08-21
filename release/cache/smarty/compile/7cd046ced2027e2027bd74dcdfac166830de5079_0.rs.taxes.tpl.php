<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:47
  from 'D:\Projects\Hosts\life-basis.local\release\modules\shop\view\productform\taxes.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b7788aa39_37103330',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7cd046ced2027e2027bd74dcdfac166830de5079' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\shop\\view\\productform\\taxes.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b7788aa39_37103330 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<input type="hidden" name="tax_ids" value="<?php echo $_smarty_tpl->tpl_vars['elem']->value['tax_ids'];?>
">

<input type="checkbox" class="tax_items" value="category" id="tax_category" <?php if ($_smarty_tpl->tpl_vars['elem']->value['tax_ids'] == 'category') {?>checked<?php }?>> 
<label for="tax_category"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Как у основной категории<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></label><br>
<?php ob_start();
echo (($tmp = $_smarty_tpl->tpl_vars['elem']->value['tax_ids'] ?? null)===null||$tmp==='' ? '' ?? null : $tmp);
$_prefixVariable8 = ob_get_clean();
$_smarty_tpl->_assignInScope('checked_taxes', explode(",",$_prefixVariable8));?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['field']->value->getList(), 'tax', false, 'key');
$_smarty_tpl->tpl_vars['tax']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['tax']->value) {
$_smarty_tpl->tpl_vars['tax']->do_else = false;
?>
    <input type="checkbox" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" class="tax_items tax_items_other" id="tax_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if (in_array($_smarty_tpl->tpl_vars['key']->value,$_smarty_tpl->tpl_vars['checked_taxes']->value)) {?>checked<?php }?>> 
    <label for="tax_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['tax']->value;?>
</label><br>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php echo '<script'; ?>
>
$(function() {
    var checkTax = function() {
        if (this.checked) {
            $('.tax_items_other').prop('checked', false).prop('disabled', true);
        } else {
            $('.tax_items_other').prop('disabled', false);
        }
    }
    
    $('#tax_category').change(checkTax).change();
    $('.tax_items').change(function() {
        var value = new Array();
        $('.tax_items:checked').each(function() {
            value.push($(this).val());
        });
        $('input[name="tax_ids"]').val(value.join(','));
    });
})
<?php echo '</script'; ?>
><?php }
}
