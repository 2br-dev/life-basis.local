<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:45
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\keyvaleditor.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b7536ec65_70707749',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '780db2d08a181ebbf7fba087267d12d020bb1f65' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\keyvaleditor.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b7536ec65_70707749 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"jquery.tablednd/jquery.tablednd.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.keyvaleditor.js",'basepath'=>"common"),$_smarty_tpl);?>

<div class="keyval-container" data-var="<?php echo $_smarty_tpl->tpl_vars['field_name']->value;?>
">
    <table class="keyvalTable<?php if (empty($_smarty_tpl->tpl_vars['arr']->value)) {?> hidden<?php }?>">
        <thead>
            <tr>
                <th width="20"></th>
                <th class="kv-head-key"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Параметр<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th class="kv-head-val"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Значение<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th width="20"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (is_array($_smarty_tpl->tpl_vars['arr']->value)) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['arr']->value, 'prop_val', false, 'prop_key');
$_smarty_tpl->tpl_vars['prop_val']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['prop_key']->value => $_smarty_tpl->tpl_vars['prop_val']->value) {
$_smarty_tpl->tpl_vars['prop_val']->do_else = false;
?>
            <tr>
                <td class="kv-sort">
                    <div class="ksort">
                        <i class="zmdi zmdi-unfold-more"></i>
                    </div>
                </td>
                <td class="kv-key"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['field_name']->value;?>
[key][]" value="<?php echo $_smarty_tpl->tpl_vars['prop_key']->value;?>
"></td>
                <td class="kv-val"><input type="text" name="<?php echo $_smarty_tpl->tpl_vars['field_name']->value;?>
[val][]" value="<?php echo $_smarty_tpl->tpl_vars['prop_val']->value;?>
"></td>
                <td class="kv-del"><a class="remove zmdi zmdi-delete"></a></td>
            </tr>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php }?>
        </tbody>
    </table>
    <a class="btn btn-default add-pair va-m-c">
        <i class="zmdi zmdi-plus"></i>
        <span><?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Добавить";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable6=ob_get_clean();
echo (($tmp = $_smarty_tpl->tpl_vars['add_button_text']->value ?? null)===null||$tmp==='' ? $_prefixVariable6 ?? null : $tmp);?>
</span>
    </a>
</div><?php }
}
