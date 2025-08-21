<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:41
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\xdir_treelistbox.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b711838f3_53121399',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '07db38ef536649e241e10535ca53ccff591fea5f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\xdir_treelistbox.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/coreobject/type/form/treelistbox_branch.tpl' => 1,
    'rs:%system%/coreobject/type/form/block_error.tpl' => 1,
  ),
),false)) {
function content_68a58b711838f3_53121399 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"jquery.rs.treeselect.js",'basepath'=>"common"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['form_name']->value === null) {?>
    <?php $_smarty_tpl->_assignInScope('form_name', $_smarty_tpl->tpl_vars['field']->value->getFormName());
}
if ($_smarty_tpl->tpl_vars['values']->value === null) {?>
    <?php $_smarty_tpl->_assignInScope('values', $_smarty_tpl->tpl_vars['field']->value->get());
}?>

<?php $_smarty_tpl->_assignInScope('iterator', $_smarty_tpl->tpl_vars['field']->value->getTreeList());
$_smarty_tpl->_assignInScope('api', $_smarty_tpl->tpl_vars['iterator']->value->getApi());
$_smarty_tpl->_assignInScope('attributes', $_smarty_tpl->tpl_vars['field']->value->getAttrArray());?>

<?php  $_prefixVariable1 = $_smarty_tpl->tpl_vars['iterator']->value;
$_smarty_tpl->_assignInScope('multiple', !empty($_smarty_tpl->tpl_vars['attributes']->value[$_prefixVariable1::ATTRIBUTE_MULTIPLE]));
$_prefixVariable2 = $_smarty_tpl->tpl_vars['iterator']->value;
$_smarty_tpl->_assignInScope('disallow_select_branches', !empty($_smarty_tpl->tpl_vars['attributes']->value[$_prefixVariable2::ATTRIBUTE_DISALLOW_SELECT_BRANCHES]));?>

<?php if ($_smarty_tpl->tpl_vars['values']->value && !is_array($_smarty_tpl->tpl_vars['values']->value)) {?>
    <?php $_smarty_tpl->_assignInScope('values', array($_smarty_tpl->tpl_vars['values']->value));
}
$_smarty_tpl->_assignInScope('path_to_first', array());
$_smarty_tpl->_assignInScope('valid_values', array());
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['values']->value, 'value');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?>
    <?php $_prefixVariable3 = $_smarty_tpl->tpl_vars['api']->value->getPathToFirst($_smarty_tpl->tpl_vars['value']->value);
$_smarty_tpl->_assignInScope('path', $_prefixVariable3);
if ($_prefixVariable3) {?>
        <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['path_to_first']) ? $_smarty_tpl->tpl_vars['path_to_first']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[$_smarty_tpl->tpl_vars['value']->value] = $_smarty_tpl->tpl_vars['path']->value;
$_smarty_tpl->_assignInScope('path_to_first', $_tmp_array);?>
        <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['valid_values']) ? $_smarty_tpl->tpl_vars['valid_values']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[] = $_smarty_tpl->tpl_vars['value']->value;
$_smarty_tpl->_assignInScope('valid_values', $_tmp_array);?>
    <?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php $_smarty_tpl->_assignInScope('method_name', '');
if ($_smarty_tpl->tpl_vars['elem']->value instanceof \RS\Orm\FormObject || $_smarty_tpl->tpl_vars['elem']->value instanceof \RS\Orm\ControllerParamObject) {?>
    <?php $_smarty_tpl->_assignInScope('class', get_class($_smarty_tpl->tpl_vars['elem']->value->getParentObject()));?>
    <?php $_smarty_tpl->_assignInScope('method_name', $_smarty_tpl->tpl_vars['elem']->value->getParentParamMethod());
} else { ?>
    <?php $_smarty_tpl->_assignInScope('class', get_class($_smarty_tpl->tpl_vars['elem']->value));
}?>

<?php $_smarty_tpl->_assignInScope('tree_list_params', array('class'=>$_smarty_tpl->tpl_vars['class']->value,'method_name'=>$_smarty_tpl->tpl_vars['method_name']->value,'property'=>$_smarty_tpl->tpl_vars['field']->value->getName()));
$_smarty_tpl->_assignInScope('tree_list_url', $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('getTreeChilds',$_smarty_tpl->tpl_vars['tree_list_params']->value,'main-ormfieldrequester'));?>
<div class="tree-select" data-form-name="<?php echo $_smarty_tpl->tpl_vars['form_name']->value;?>
" data-tree-list-url="<?php echo $_smarty_tpl->tpl_vars['tree_list_url']->value;?>
"
        <?php if ($_smarty_tpl->tpl_vars['multiple']->value) {
$_prefixVariable4 = $_smarty_tpl->tpl_vars['iterator']->value;
echo $_prefixVariable4::ATTRIBUTE_MULTIPLE;?>
="1"<?php }?>
        <?php if ($_smarty_tpl->tpl_vars['disallow_select_branches']->value) {
$_prefixVariable5 = $_smarty_tpl->tpl_vars['iterator']->value;
echo $_prefixVariable5::ATTRIBUTE_DISALLOW_SELECT_BRANCHES;?>
="1"<?php }?>
>
    <div class="tree-select_selected-box">
        <ul class="tree-select_selected-values">
            <?php if ($_smarty_tpl->tpl_vars['valid_values']->value) {?>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['valid_values']->value, 'value');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?>
                    <?php $_smarty_tpl->_assignInScope('last', end($_smarty_tpl->tpl_vars['path_to_first']->value[$_smarty_tpl->tpl_vars['value']->value]));?>
                    <?php if ($_smarty_tpl->tpl_vars['last']->value['is_spec_dir'] == 'N') {?>
                        <?php $_smarty_tpl->_assignInScope('path_ids', array());?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['path_to_first']->value[$_smarty_tpl->tpl_vars['value']->value], 'orm');
$_smarty_tpl->tpl_vars['orm']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['orm']->value) {
$_smarty_tpl->tpl_vars['orm']->do_else = false;
?>
                            <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['path_ids']) ? $_smarty_tpl->tpl_vars['path_ids']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[] = $_smarty_tpl->tpl_vars['orm']->value[$_smarty_tpl->tpl_vars['api']->value->getIdField()]+0;
$_smarty_tpl->_assignInScope('path_ids', $_tmp_array);?>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        <li class="tree-select_selected-value-item" data-id="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
" data-path-ids='<?php echo json_encode($_smarty_tpl->tpl_vars['path_ids']->value);?>
'>
                            <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['form_name']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
">
                            <span class="tree-select_selected-value-item_title-path">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, array_slice($_smarty_tpl->tpl_vars['path_to_first']->value[$_smarty_tpl->tpl_vars['value']->value],0,-1), 'orm');
$_smarty_tpl->tpl_vars['orm']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['orm']->value) {
$_smarty_tpl->tpl_vars['orm']->do_else = false;
?>
                                <span class="tree-select_selected-value-item_title-path-part"><?php echo $_smarty_tpl->tpl_vars['orm']->value[$_smarty_tpl->tpl_vars['api']->value->getNameField()];?>
</span>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </span>
                            <?php $_smarty_tpl->_assignInScope('orm', end($_smarty_tpl->tpl_vars['path_to_first']->value[$_smarty_tpl->tpl_vars['value']->value]));?>
                            <span class="tree-select_selected-value-item_title-end-part"><?php echo $_smarty_tpl->tpl_vars['orm']->value[$_smarty_tpl->tpl_vars['api']->value->getNameField()];?>
</span>
                            <i class="tree-select_selected-value-item_remove zmdi zmdi-close"></i>
                        </li>
                    <?php }?>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php } else { ?>
                <li class="tree-select_selected-value-stub"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>- Ничего не выбрано -<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></li>
            <?php }?>
        </ul>
        <div class="tree-select_drop-chevron-box">
            <i class="tree-select_drop-chevron zmdi zmdi-chevron-down"></i>
        </div>
    </div>

    <div class="tree-select_drop-box">
        <div class="tree-select_search-box">
            <input class="tree-select_search-input" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>поиск<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
            <i class="tree-select_search-input-icon zmdi zmdi-search"></i>
        </div>
        <ul class="tree-select_list">
            <?php $_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/treelistbox_branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('iterator'=>$_smarty_tpl->tpl_vars['iterator']->value), 0, false);
?>
        </ul>
    </div>
</div>
<?php $_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/block_error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
