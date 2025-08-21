<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:56
  from 'D:\Projects\Hosts\life-basis.local\release\modules\shop\view\widget\lastorder.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a342086a26b7_93545036',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '95e8bec414694a72f03aae9604a75bff87400d76' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\shop\\view\\widget\\lastorder.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%SYSTEM%/admin/widget/paginator.tpl' => 1,
  ),
),false)) {
function content_68a342086a26b7_93545036 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.dateformat.php','function'=>'smarty_modifier_dateformat',),));
?>
<div class="widget-filters">
    <div class="dropdown">
        <a id="last-order-switcher" data-toggle="dropdown" class="widget-dropdown-handle"><?php if ($_smarty_tpl->tpl_vars['filter']->value == 'active') {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>незавершенные<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>все<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?> <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="last-order-switcher">
            <li<?php if ($_smarty_tpl->tpl_vars['filter']->value == 'active') {?> class="act"<?php }?>><a data-update-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"shop-widget-lastorders",'filter'=>"active"),$_smarty_tpl);?>
" class="call-update"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Незавершенные<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></li>
            <li<?php if ($_smarty_tpl->tpl_vars['filter']->value == 'all') {?> class="act"<?php }?>><a data-update-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"shop-widget-lastorders",'filter'=>"all"),$_smarty_tpl);?>
" class="call-update"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Все<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></li>
        </ul>
    </div>
</div>

<?php if (count($_smarty_tpl->tpl_vars['orders']->value)) {?>
    <table class="wtable mrg overable table-lastorder">
        <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orders']->value, 'order');
$_smarty_tpl->tpl_vars['order']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['order']->value) {
$_smarty_tpl->tpl_vars['order']->do_else = false;
?>
            <?php $_smarty_tpl->_assignInScope('status', $_smarty_tpl->tpl_vars['order']->value->getStatus());?>
            <tr onclick="window.open('<?php echo smarty_function_adminUrl(array('mod_controller'=>"shop-orderctrl",'do'=>"edit",'id'=>$_smarty_tpl->tpl_vars['order']->value['id']),$_smarty_tpl);?>
', '_blank')" class="clickable">
                <td class="number f-14">
                    <div class="title">
                        <span style="background:<?php echo $_smarty_tpl->tpl_vars['status']->value->bgcolor;?>
" title="<?php echo $_smarty_tpl->tpl_vars['status']->value->title;?>
" class="w-point"></span>
                        <b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('num'=>$_smarty_tpl->tpl_vars['order']->value['order_num']));
$_block_repeat=true;
echo smarty_block_t(array('num'=>$_smarty_tpl->tpl_vars['order']->value['order_num']), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Заказ №%num<?php $_block_repeat=false;
echo smarty_block_t(array('num'=>$_smarty_tpl->tpl_vars['order']->value['order_num']), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></b>
                    </div>
                    <div class="price"><?php echo $_smarty_tpl->tpl_vars['order']->value->getTotalPrice();?>
</div>
                </td>
                <td class="w-date">
                    <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['order']->value['dateof'],"j %v %!Y");?>
<br>
                    <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['order']->value['dateof'],"@time");?>

                </td>
            </tr>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </tbody>
    </table>
<?php } else { ?>
    <div class="empty-widget">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет ни одного заказа<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php }?>

<?php $_smarty_tpl->_subTemplateRender("rs:%SYSTEM%/admin/widget/paginator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('paginatorClass'=>"with-top-line"), 0, false);
}
}
