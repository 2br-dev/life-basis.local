<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:55:22
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\paginator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a330ca113c60_01903149',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2b256e14b4aa1644d178b20b5d455b79bcf548b1' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\paginator.tpl',
      1 => 1755524277,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a330ca113c60_01903149 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.devnull.php','function'=>'smarty_modifier_devnull',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if ($_smarty_tpl->tpl_vars['paginator']->value->total_pages > 1) {?>
    <?php $_smarty_tpl->_assignInScope('pagestr', t('Страница %page',array('page'=>$_smarty_tpl->tpl_vars['paginator']->value->page)));?>
    <?php if ($_smarty_tpl->tpl_vars['paginator']->value->page > 1 && !substr_count($_smarty_tpl->tpl_vars['app']->value->title->get(),$_smarty_tpl->tpl_vars['pagestr']->value)) {?>
        <?php echo smarty_modifier_devnull($_smarty_tpl->tpl_vars['app']->value->title->addSection($_smarty_tpl->tpl_vars['pagestr']->value,0,'after'));?>

    <?php }?>

    <?php if (!$_smarty_tpl->tpl_vars['paginator_len']->value) {?>
        <?php $_smarty_tpl->_assignInScope('paginator_len', 5);?>
    <?php }?>
    <?php echo smarty_modifier_devnull($_smarty_tpl->tpl_vars['paginator']->value->setPaginatorLen($_smarty_tpl->tpl_vars['paginator_len']->value));?>

    <div class="<?php echo (($tmp = $_smarty_tpl->tpl_vars['class']->value ?? null)===null||$tmp==='' ? "mt-5" ?? null : $tmp);?>
">
		<ul class="pagination">
			<?php if ($_smarty_tpl->tpl_vars['paginator']->value->page > 1) {?>
				<li class="pagination-page">
					<a href="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->getPageHref($_smarty_tpl->tpl_vars['paginator']->value->page-1);?>
" data-page="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->page-1;?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>предыдущая страница<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
						<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-left.svg" alt="Назад">
					</a>
				</li>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['paginator']->value->showFirst()) {?>
				<li class="pagination-page">
					<a href="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->getPageHref(1);?>
" data-page="1" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>первая страница<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"s>1</a>
				</li>
				<li class="pagination-dots">
					<span>...</span>
				</li>
			<?php }?>

			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['paginator']->value->getPageList(), 'page');
$_smarty_tpl->tpl_vars['page']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['page']->value) {
$_smarty_tpl->tpl_vars['page']->do_else = false;
?>
				<li class="pagination-page <?php if ($_smarty_tpl->tpl_vars['page']->value['act']) {?>active<?php }?>">
					<a href="<?php echo $_smarty_tpl->tpl_vars['page']->value['href'];?>
" data-page="<?php echo $_smarty_tpl->tpl_vars['page']->value['n'];?>
"><?php echo $_smarty_tpl->tpl_vars['page']->value['n'];?>
</a>
				</li>
			<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

			<?php if ($_smarty_tpl->tpl_vars['paginator']->value->showLast()) {?>
				<li class="pagination-dots">
					<span>...</span>
				</li>
				<li class="pagination-page">
					<a href="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->getPageHref($_smarty_tpl->tpl_vars['paginator']->value->total_pages);?>
" data-page="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->total_pages;?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>последняя страница<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><?php echo $_smarty_tpl->tpl_vars['paginator']->value->total_pages;?>
</a>
				</li>
			<?php }?>

			<?php if ($_smarty_tpl->tpl_vars['paginator']->value->page < $_smarty_tpl->tpl_vars['paginator']->value->total_pages) {?>
				<li class="pagination-page">
					<a href="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->getPageHref($_smarty_tpl->tpl_vars['paginator']->value->page+1);?>
" data-page="<?php echo $_smarty_tpl->tpl_vars['paginator']->value->page+1;?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>следующая страница<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
						<img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/chevron-right.svg" alt="Вперёд">
					</a>
				</li>
			<?php }?>
		</ul>
    </div>
<?php }
}
}
