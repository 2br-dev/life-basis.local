<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:09:05
  from 'D:\Projects\Hosts\life-basis.local\release\modules\marketplace\view\widget\newmodules\newmodules_item.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a342118d9d95_03694108',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd246b03d8fdfc06cea9d9c9a78d8d3199851ac1e' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\marketplace\\view\\widget\\newmodules\\newmodules_item.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a342118d9d95_03694108 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if ($_smarty_tpl->tpl_vars['error']->value) {?>
    <div class="empty-widget">
        <?php echo $_smarty_tpl->tpl_vars['error']->value;?>

    </div>
<?php } elseif ($_smarty_tpl->tpl_vars['items']->value) {?>
    <div class="mp-modules__list<?php if (count($_smarty_tpl->tpl_vars['items']->value) == 1) {?> no-columns<?php }?>">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
            <div class="item">
                <a class="pic" href="<?php echo smarty_function_adminUrl(array('do'=>false,'mod_controller'=>"marketplace-ctrl"),$_smarty_tpl);?>
#<?php echo $_smarty_tpl->tpl_vars['item']->value['url'];?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['item']->value['image'];?>
"></a>
                <p class="description"><?php echo $_smarty_tpl->tpl_vars['item']->value['description'];?>
</p>
            </div>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
<?php } else { ?>
    <div class="empty-widget">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет рекомендуемых виджетов<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php }
}
}
