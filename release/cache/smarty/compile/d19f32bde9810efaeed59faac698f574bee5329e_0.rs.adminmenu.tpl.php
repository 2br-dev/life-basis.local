<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\admin\adminmenu.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31806557363_85874233',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd19f32bde9810efaeed59faac698f574bee5329e' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\admin\\adminmenu.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%menu%/admin/adminmenu_branch.tpl' => 1,
  ),
),false)) {
function content_68a31806557363_85874233 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.meter.php','function'=>'smarty_function_meter',),));
echo smarty_function_addjs(array('file'=>"%menu%/admin_menu.js"),$_smarty_tpl);?>

<div class="side-menu-overlay"></div>
<ul class="side-menu side-main">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
        <li <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?> class="sm-node rs-meter-group"<?php }?>>
            <a <?php if ((isset($_smarty_tpl->tpl_vars['sel_id']->value)) && $_smarty_tpl->tpl_vars['sel_id']->value == $_smarty_tpl->tpl_vars['item']->value['fields']['id']) {?>class="active"<?php }?> <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?>data-url="<?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['link'];?>
"<?php } else { ?>href="<?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['link'];?>
"<?php }?>>
                <i class="rs-icon rs-icon-<?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['alias'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['fields']['iconstyle']) {?>style="<?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['iconstyle'];?>
"<?php }?>>
                    <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?>
                        <?php echo smarty_function_meter(array(),$_smarty_tpl);?>

                    <?php } else { ?>
                        <?php echo smarty_function_meter(array('key'=>"rs-admin-menu-".((string)$_smarty_tpl->tpl_vars['item']->value['fields']['alias'])),$_smarty_tpl);?>

                    <?php }?>
                </i>
                <span class="title"><?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['title'];?>
</span>
            </a>
            <?php if ($_smarty_tpl->tpl_vars['item']->value->getChildsCount()) {?>
                <div class="sm">
                    <div class="sm-head">
                        <a class="menu-close"><i class="zmdi zmdi-close"></i></a>
                        <?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['title'];?>

                    </div>
                    <div class="sm-body">
                        <ul>
                            <?php $_smarty_tpl->_subTemplateRender("rs:%menu%/admin/adminmenu_branch.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('list'=>$_smarty_tpl->tpl_vars['item']->value['child'],'is_second_level'=>true), 0, true);
?>
                        </ul>
                    </div>
                </div>
            <?php }?>
        </li>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</ul><?php }
}
