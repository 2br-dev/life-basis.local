<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\admin\adminmenu_branch.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318068115c2_22065128',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e1ba14b808bd096fd6e1db8ad0977e8600a1e794' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\admin\\adminmenu_branch.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%menu%/admin/adminmenu_branch.tpl' => 1,
  ),
),false)) {
function content_68a318068115c2_22065128 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.meter.php','function'=>'smarty_function_meter',),));
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
    <li <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?> class="sm-node rs-meter-group"<?php }?>>
        <a class="<?php if ((isset($_smarty_tpl->tpl_vars['sel_id']->value)) && $_smarty_tpl->tpl_vars['sel_id']->value == $_smarty_tpl->tpl_vars['item']->value['fields']['id']) {?>active<?php }?>" href="<?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['link'];?>
">
            
                <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?>
                    <?php echo smarty_function_meter(array(),$_smarty_tpl);?>

                <?php } else { ?>
                    <?php echo smarty_function_meter(array('key'=>"rs-admin-menu-".((string)$_smarty_tpl->tpl_vars['item']->value['fields']['alias'])),$_smarty_tpl);?>

                <?php }?>
            
            <span class="sm-node-title"><?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['title'];?>
</span>
        </a>
        <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?>
            <ul>
                <?php $_smarty_tpl->_subTemplateRender("rs:%menu%/admin/adminmenu_branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('list'=>$_smarty_tpl->tpl_vars['item']->value['child'],'is_first_level'=>false), 0, true);
?>
            </ul>
        <?php }?>
    </li>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
