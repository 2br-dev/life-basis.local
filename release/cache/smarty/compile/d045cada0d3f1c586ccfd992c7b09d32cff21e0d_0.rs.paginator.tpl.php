<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:34
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\paginator\paginator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0a3025f3_31546481',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd045cada0d3f1c586ccfd992c7b09d32cff21e0d' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\paginator\\paginator.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0a3025f3_31546481 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if (empty($_smarty_tpl->tpl_vars['local_options']->value['short'])) {?>
    <span class="text hidden-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>страница<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
<?php }?>

    <a <?php if ($_smarty_tpl->tpl_vars['local_options']->value['is_virtual']) {?>data-<?php }?>href="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->left;?>
" class="prev <?php if (!$_smarty_tpl->tpl_vars['local_options']->value['is_virtual'] && !$_smarty_tpl->tpl_vars['local_options']->value['no_ajax']) {?>call-update<?php }?> <?php if ($_smarty_tpl->tpl_vars['paginator']->value->isNoUpdateUrl()) {?>no-update-hash<?php }?> zmdi zmdi-chevron-left" <?php if ($_smarty_tpl->tpl_vars['paginator']->value->getUpdateContainer()) {?>data-update-container="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->getUpdateContainer();?>
"<?php }?> title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>предыдущая страница<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
    <input type="text" class="page" name="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->page_key;?>
" value="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->page;?>
" onfocus="$(this).select()">

    <a <?php if ($_smarty_tpl->tpl_vars['local_options']->value['is_virtual']) {?>data-<?php }?>href="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->right;?>
" class="next <?php if (!$_smarty_tpl->tpl_vars['local_options']->value['is_virtual'] && !$_smarty_tpl->tpl_vars['local_options']->value['no_ajax']) {?>call-update<?php }?> <?php if ($_smarty_tpl->tpl_vars['paginator']->value->isNoUpdateUrl()) {?>no-update-hash<?php }?> zmdi zmdi-chevron-right" <?php if ($_smarty_tpl->tpl_vars['paginator']->value->getUpdateContainer()) {?>data-update-container="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->getUpdateContainer();?>
"<?php }?> title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>следующая страница<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
    <span class="text">из <?php echo $_smarty_tpl->tpl_vars['paginator']->value->page_count;?>
</span>

<?php if (empty($_smarty_tpl->tpl_vars['local_options']->value['short'])) {?>
    <span class="text perpage_block"><span class="hidden-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>показывать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> </span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>по<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> </span>
    <input type="text" class="perpage" name="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->pagesize_key;?>
" value="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->page_size;?>
" onfocus="$(this).select()">
    <button type="submit" class="btn btn-default"><i class="zmdi zmdi-check visible-xs"></i> <span class="hidden-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Применить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></button>

    <span class="total"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>всего записей: <?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?><span class="total_value"><?php echo $_smarty_tpl->tpl_vars['paginator']->value->total;?>
</span></span>
<?php }
}
}
