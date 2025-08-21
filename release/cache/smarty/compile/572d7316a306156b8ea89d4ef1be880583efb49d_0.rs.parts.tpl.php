<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:35
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\filter\parts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0b5e4cd8_36883488',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '572d7316a306156b8ea89d4ef1be880583efb49d' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\filter\\parts.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0b5e4cd8_36883488 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_assignInScope('parts', $_smarty_tpl->tpl_vars['fcontrol']->value->getParts());
if (count($_smarty_tpl->tpl_vars['parts']->value)) {?>
    <div class="filter-parts">
        <?php if (count($_smarty_tpl->tpl_vars['parts']->value) > 1) {?><span class="part clean_all"><a href="<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getCleanFilterUrl();?>
" class="clean call-update" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Сбросить все фильтры<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-close<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getAddClass();?>
" <?php if ($_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer()) {?>data-update-container="<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer();?>
"<?php }?>></i></a></span><?php }?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['parts']->value, 'part');
$_smarty_tpl->tpl_vars['part']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['part']->value) {
$_smarty_tpl->tpl_vars['part']->do_else = false;
?>
            <span class="part">
                <span class="text"><?php echo $_smarty_tpl->tpl_vars['part']->value['title'];?>
: <?php echo $_smarty_tpl->tpl_vars['part']->value['value'];?>
</span>
                <a href="<?php echo $_smarty_tpl->tpl_vars['part']->value['href_clean'];?>
" <?php if ($_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer()) {?>data-update-container="<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer();?>
"<?php }?> class="clean call-update" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Сбросить этот фильтр<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-close<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getAddClass();?>
" <?php if ($_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer()) {?>data-update-container="<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer();?>
"<?php }?>></i></a>
            </span>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
<?php }
}
}
