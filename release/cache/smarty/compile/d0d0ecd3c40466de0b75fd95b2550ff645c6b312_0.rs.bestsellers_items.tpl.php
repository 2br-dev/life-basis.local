<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:09:04
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\widget\bestsellers_items.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34210b5b055_79227006',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd0d0ecd3c40466de0b75fd95b2550ff645c6b312' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\widget\\bestsellers_items.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a34210b5b055_79227006 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if ($_smarty_tpl->tpl_vars['error']->value) {?>
    <div class="empty-widget">
        <?php echo $_smarty_tpl->tpl_vars['error']->value;?>

    </div>
<?php } elseif ($_smarty_tpl->tpl_vars['items']->value) {?>
    <div class="best-sellers owl-carousel owl-theme">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
            <div class="best-sellers_item">
                <h2 class="best-sellers_title"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</h2>
                <div class="best-sellers_description">
                    <p><?php echo $_smarty_tpl->tpl_vars['item']->value['description'];?>
</p>
                </div>
                <div class="best-sellers_actions">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['this_controller']->value->api->prepareLink($_smarty_tpl->tpl_vars['item']->value['link']);?>
" target="_blank" class="btn btn-default btn-alt best-sellers_action"><?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Узнать больше";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable1=ob_get_clean();
echo (($tmp = $_smarty_tpl->tpl_vars['item']->value['link_title'] ?? null)===null||$tmp==='' ? $_prefixVariable1 ?? null : $tmp);?>
</a>
                </div>
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
ob_start();?>Нет предложений<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php }
}
}
