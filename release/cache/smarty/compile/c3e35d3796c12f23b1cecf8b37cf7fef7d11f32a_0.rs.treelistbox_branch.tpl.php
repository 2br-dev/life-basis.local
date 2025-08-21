<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:41
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\treelistbox_branch.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b71717797_66969270',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c3e35d3796c12f23b1cecf8b37cf7fef7d11f32a' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\treelistbox_branch.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/coreobject/type/form/treelistbox_branch.tpl' => 1,
  ),
),false)) {
function content_68a58b71717797_66969270 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('need_initialize', $_smarty_tpl->tpl_vars['load_recursive']->value ? '' : 'need-initialize');?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['iterator']->value, 'node');
$_smarty_tpl->tpl_vars['node']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['node']->value) {
$_smarty_tpl->tpl_vars['node']->do_else = false;
?>
    <li class="tree-select_list-item <?php if ($_smarty_tpl->tpl_vars['node']->value->getChildsCount()) {?>tree-branch tree-collapsed <?php echo $_smarty_tpl->tpl_vars['need_initialize']->value;
} else { ?>tree-leaf<?php }?>" data-id="<?php echo $_smarty_tpl->tpl_vars['node']->value->getId();?>
">
        <div class="tree-select_list-item_row tree-row">
            <i class="tree-select_list-item_sublist-toggle zmdi tree-branch-toggle"></i>
            <span class="tree-select_list-item_title"><?php echo $_smarty_tpl->tpl_vars['node']->value->getName();?>
</span>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['node']->value->getChildsCount()) {?>
            <ul class="tree-select_list-item_sublist">
                <?php if ($_smarty_tpl->tpl_vars['load_recursive']->value) {?>
                    <?php $_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/treelistbox_branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('iterator'=>$_smarty_tpl->tpl_vars['node']->value->getChilds()), 0, true);
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
