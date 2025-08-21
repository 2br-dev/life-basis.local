<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:20
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\tree\tree_branch.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a4e9d044_18970429',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '589ce5ca6bb4c13ead2d756d677ca8e14b83cb6f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\tree\\tree_branch.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/admin/html_elements/tree/tree_branch.tpl' => 1,
  ),
),false)) {
function content_68a318a4e9d044_18970429 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.devnull.php','function'=>'smarty_modifier_devnull',),));
if ((isset($_smarty_tpl->tpl_vars['local_options']->value['render_all_nodes']))) {?>
    <?php $_smarty_tpl->_assignInScope('render_all_nodes', $_smarty_tpl->tpl_vars['local_options']->value['render_all_nodes']);
}
if ((isset($_smarty_tpl->tpl_vars['local_options']->value['render_opened_nodes']))) {?>
    <?php $_smarty_tpl->_assignInScope('render_opened_nodes', $_smarty_tpl->tpl_vars['local_options']->value['render_opened_nodes']);
}
if ((isset($_smarty_tpl->tpl_vars['local_options']->value['forced_open_nodes']))) {?>
    <?php $_smarty_tpl->_assignInScope('forced_open_nodes', $_smarty_tpl->tpl_vars['local_options']->value['forced_open_nodes']);
}?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
    <?php $_smarty_tpl->_assignInScope('object', $_smarty_tpl->tpl_vars['item']->value->getObject());?>
    <?php $_smarty_tpl->_assignInScope('is_disabled', (isset($_smarty_tpl->tpl_vars['tree']->value->options['disabledField'])) && $_smarty_tpl->tpl_vars['object']->value[$_smarty_tpl->tpl_vars['tree']->value->options['disabledField']] === $_smarty_tpl->tpl_vars['tree']->value->options['disabledValue'] ? 'disabled' : '');?>
    <?php $_smarty_tpl->_assignInScope('is_current', (isset($_smarty_tpl->tpl_vars['tree']->value->options['activeField'])) && $_smarty_tpl->tpl_vars['tree']->value->options['activeValue'] == $_smarty_tpl->tpl_vars['object']->value[$_smarty_tpl->tpl_vars['tree']->value->options['activeField']] ? 'current' : '');?>
    <?php $_smarty_tpl->_assignInScope('class_field', $_smarty_tpl->tpl_vars['object']->value[$_smarty_tpl->tpl_vars['tree']->value->options['classField']] ? $_smarty_tpl->tpl_vars['object']->value[$_smarty_tpl->tpl_vars['tree']->value->options['classField']] : '');?>
    <?php $_smarty_tpl->_assignInScope('is_opened', $_smarty_tpl->tpl_vars['item']->value->isOpened() || $_smarty_tpl->tpl_vars['forced_open_nodes']->value);?>
    <?php $_smarty_tpl->_assignInScope('render_childs', $_smarty_tpl->tpl_vars['render_all_nodes']->value || ($_smarty_tpl->tpl_vars['render_opened_nodes']->value && $_smarty_tpl->tpl_vars['is_opened']->value));?>
    <?php $_smarty_tpl->_assignInScope('closed_class', $_smarty_tpl->tpl_vars['is_opened']->value ? 'tree-expanded' : 'tree-collapsed');?>
    <?php $_smarty_tpl->_assignInScope('is_branch', $_smarty_tpl->tpl_vars['item']->value->getChildsCount() ? 'tree-branch' : '');?>
    <?php $_smarty_tpl->_assignInScope('need_initialize', $_smarty_tpl->tpl_vars['item']->value->getChildsCount() && !$_smarty_tpl->tpl_vars['render_childs']->value ? 'need-initialize' : '');?>
    <?php $_smarty_tpl->_assignInScope('is_root', $_smarty_tpl->tpl_vars['object']->value['is_root_element'] ? 'root noDraggable' : '');?>

    <?php $_smarty_tpl->_assignInScope('item_id', $_smarty_tpl->tpl_vars['object']->value[$_smarty_tpl->tpl_vars['tree']->value->options['sortIdField']]);?>

    <li class="<?php echo $_smarty_tpl->tpl_vars['is_disabled']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['is_current']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['class_field']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['closed_class']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['is_branch']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['need_initialize']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['is_root']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['tree']->value->isNoDraggable($_smarty_tpl->tpl_vars['object']->value)) {?>data-notmove="notmove"<?php }?> data-id="<?php echo $_smarty_tpl->tpl_vars['item_id']->value;?>
">
        <div class="item">
            <div class="chk" unselectable="on">
                <?php if (!$_smarty_tpl->tpl_vars['tree']->value->isNoCheckbox($_smarty_tpl->tpl_vars['object']->value) && !$_smarty_tpl->tpl_vars['tree']->value->options['noCheckbox']) {?>
                    <input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['tree']->value->getCheckboxName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['object']->value[$_smarty_tpl->tpl_vars['tree']->value->options['activeField']];?>
" <?php if ($_smarty_tpl->tpl_vars['tree']->value->isChecked($_smarty_tpl->tpl_vars['object']->value[$_smarty_tpl->tpl_vars['tree']->value->options['activeField']])) {?>checked<?php }?> <?php if ($_smarty_tpl->tpl_vars['tree']->value->isDisabledCheckbox($_smarty_tpl->tpl_vars['object']->value)) {?>disabled<?php }?>>
                <?php }?>
            </div>
            <div class="line">
                <div class="toggle">
                    <i class="zmdi"></i>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['tree']->value->options['sortable']) {?><div class="move<?php if ($_smarty_tpl->tpl_vars['tree']->value->isNoDraggable($_smarty_tpl->tpl_vars['object']->value)) {?> no-move<?php }?>"><i class="zmdi zmdi-unfold-more"></i></div><?php }?>
                <?php if (!$_smarty_tpl->tpl_vars['tree']->value->isNoRedMarker($_smarty_tpl->tpl_vars['object']->value)) {?>
                    <div class="redmarker"></div>
                <?php }?>
                <div class="data">
                    <div class="textvalue">
                        <?php $_smarty_tpl->_assignInScope('cell', $_smarty_tpl->tpl_vars['tree']->value->getMainColumn($_smarty_tpl->tpl_vars['object']->value));?>
                        <?php if ((isset($_smarty_tpl->tpl_vars['cell']->value->property['href']))) {?><a href="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getHref();?>
" <?php echo $_smarty_tpl->tpl_vars['cell']->value->getLinkAttr();?>
><?php }?>
                        <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['cell']->value->getBodyTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['cell']->value), 0, true);
?>
                        <?php if ((isset($_smarty_tpl->tpl_vars['cell']->value->property['href']))) {?></a><?php }?>
                    </div>
                    <?php if (!$_smarty_tpl->tpl_vars['tree']->value->isNoOtherColumns($_smarty_tpl->tpl_vars['object']->value)) {?>
                        <?php if ((isset($_smarty_tpl->tpl_vars['object']->value['treeTools']))) {?>
                            <?php echo smarty_modifier_devnull($_smarty_tpl->tpl_vars['object']->value['treeTools']->setRow($_smarty_tpl->tpl_vars['object']->value));?>

                            <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['object']->value['treeTools']->getBodyTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['object']->value['treeTools']), 0, true);
?>
                        <?php } else { ?>
                            <?php if ($_smarty_tpl->tpl_vars['tree']->value->getTools()) {?>
                                <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['tree']->value->getTools()->getBodyTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['tree']->value->getTools($_smarty_tpl->tpl_vars['object']->value)), 0, true);
?>
                            <?php }?>
                        <?php }?>
                    <?php }?>
                </div>
            </div>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?>
            <ul class="childroot">
                <?php if ($_smarty_tpl->tpl_vars['render_childs']->value) {?>
                    <?php $_smarty_tpl->_subTemplateRender("rs:%system%/admin/html_elements/tree/tree_branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('list'=>$_smarty_tpl->tpl_vars['item']->value['child'],'level'=>$_smarty_tpl->tpl_vars['level']->value+1), 0, true);
?>
                <?php }?>
            </ul>
        <?php }?>
    </li>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
