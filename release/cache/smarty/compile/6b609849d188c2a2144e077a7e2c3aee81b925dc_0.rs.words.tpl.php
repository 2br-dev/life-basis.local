<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:50
  from 'D:\Projects\Hosts\life-basis.local\release\modules\tags\view\admin\words.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b1ad8e195_11040327',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6b609849d188c2a2144e077a7e2c3aee81b925dc' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\tags\\view\\admin\\words.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b1ad8e195_11040327 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if (!empty($_smarty_tpl->tpl_vars['word_list']->value)) {?>
    <ul class="taglist">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['word_list']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
        <li><div class="padd"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['item']->value['word'], ENT_QUOTES, 'UTF-8', true);?>
 <a class="tagdel zmdi zmdi-close" data-lid="<?php echo $_smarty_tpl->tpl_vars['item']->value['lid'];?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a></div></li>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
<?php } else { ?>
    <div class="notags">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Не добавлено ни одного тега<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php }?>
        <?php }
}
