<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:20
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\tree\tree.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a49c3665_60087744',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8931a216673a0996215ef4ea7c7ab71ac609c299' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\tree\\tree.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/admin/html_elements/tree/tree_branch.tpl' => 1,
  ),
),false)) {
function content_68a318a49c3665_60087744 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"nestedSortable/jquery.mjs.nestedSortable.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.treeview.js",'basepath'=>"common"),$_smarty_tpl);?>

        
<div class="activetree tree" data-uniq="<?php echo $_smarty_tpl->tpl_vars['tree']->value->options['uniq'];?>
">
    <ul class="treehead">
        <li>
            <?php if (!$_smarty_tpl->tpl_vars['tree']->value->options['noCheckbox']) {?>
            <div class="chk"><input type="checkbox" class="select-page" data-name="<?php echo $_smarty_tpl->tpl_vars['tree']->value->getCheckboxName();?>
"></div>
            <?php }?>
            <?php if (!$_smarty_tpl->tpl_vars['tree']->value->options['noExpandCollapseButton']) {?>
            <a class="allplus" title="развернуть все"><i class="zmdi zmdi-plus"></i></a>
            <a class="allminus" title="свернуть все"><i class="zmdi zmdi-minus"></i></a>
            <?php }?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tree']->value->getHeadButtons(), 'button');
$_smarty_tpl->tpl_vars['button']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['button']->value) {
$_smarty_tpl->tpl_vars['button']->do_else = false;
?>
                <?php if ($_smarty_tpl->tpl_vars['button']->value['tag']) {
$_smarty_tpl->_assignInScope('tag', $_smarty_tpl->tpl_vars['button']->value['tag']);
} else {
$_smarty_tpl->_assignInScope('tag', "a");
}?>
                <<?php echo $_smarty_tpl->tpl_vars['tag']->value;?>
 <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, (($tmp = $_smarty_tpl->tpl_vars['button']->value['attr'] ?? null)===null||$tmp==='' ? array() ?? null : $tmp), 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?> <?php echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>><?php echo $_smarty_tpl->tpl_vars['button']->value['text'];?>
</<?php echo $_smarty_tpl->tpl_vars['tag']->value;?>
>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </li>
    </ul>

    <?php $_smarty_tpl->_assignInScope('tree_list_params', array());?>
    <?php if ((isset($_smarty_tpl->tpl_vars['local_options']->value['filter']))) {?>
        <?php $_smarty_tpl->_assignInScope('filter', $_smarty_tpl->tpl_vars['local_options']->value['filter']);?>
        <?php if ($_smarty_tpl->tpl_vars['filter']->value->getKeyVal()) {?>
            <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['tree_list_params']) ? $_smarty_tpl->tpl_vars['tree_list_params']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array[$_smarty_tpl->tpl_vars['filter']->value->getFilterVar()] = $_smarty_tpl->tpl_vars['filter']->value->getKeyVal();
$_smarty_tpl->_assignInScope('tree_list_params', $_tmp_array);?>
        <?php }?>
    <?php }?>
    <?php $_smarty_tpl->_assignInScope('tree_list_url', $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('GetTreeChildsHtml',$_smarty_tpl->tpl_vars['tree_list_params']->value));?>
    <ul class="treebody root<?php if ($_smarty_tpl->tpl_vars['tree']->value->options['sortable']) {?> treesort<?php }?>" data-sort-url="<?php echo $_smarty_tpl->tpl_vars['tree']->value->options['sortUrl'];?>
" data-tree-list-url="<?php echo $_smarty_tpl->tpl_vars['tree_list_url']->value;?>
"
        <?php if ($_smarty_tpl->tpl_vars['tree']->value->options['noExpandCollapseButton']) {?>data-no-expand-collapse="true"<?php }?>
        <?php if ($_smarty_tpl->tpl_vars['tree']->value->options['maxLevels']) {?>data-max-levels="<?php echo $_smarty_tpl->tpl_vars['tree']->value->options['maxLevels'];?>
"<?php }?>>

        <?php $_smarty_tpl->_subTemplateRender("rs:%system%/admin/html_elements/tree/tree_branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('level'=>"0",'list'=>$_smarty_tpl->tpl_vars['tree']->value->getData()), 0, false);
?>
        <?php if (!count($_smarty_tpl->tpl_vars['tree']->value->getData())) {?>
            <li class="empty-tree-row"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет элементов<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></li>
        <?php }?>
    </ul>
</div><?php }
}
