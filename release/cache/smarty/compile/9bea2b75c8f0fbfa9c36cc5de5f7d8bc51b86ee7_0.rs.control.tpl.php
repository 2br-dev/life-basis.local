<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:34
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\filter\control.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0a8b2708_02938383',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9bea2b75c8f0fbfa9c36cc5de5f7d8bc51b86ee7' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\filter\\control.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0a8b2708_02938383 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"jquery.rs.filter.js"),$_smarty_tpl);?>


<div class="filter">

    <a class="openfilter">
        <i class="zmdi zmdi-search"></i>
        <span class="filter-title hidden-xs"><?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getCaption();?>
</span>
        <span class="visible-xs-inline-block"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Поиск<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
    </a>

    <form id="<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->uniq;?>
" method="GET" class="filter-form form-call-update<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getAddClass();?>
" <?php if ($_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer()) {?>data-update-container="<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getUpdateContainer();?>
"<?php }?> data-clean-url="<?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getCleanFilterUrl();?>
">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['fcontrol']->value->getAddParam('hiddenfields'), 'val', false, 'key');
$_smarty_tpl->tpl_vars['val']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->do_else = false;
?>
            <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['val']->value;?>
">
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

        <?php echo $_smarty_tpl->tpl_vars['fcontrol']->value->getContainerView();?>

    </form>

</div><?php }
}
