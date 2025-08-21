<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:57
  from 'D:\Projects\Hosts\life-basis.local\release\modules\shop\view\widget\reservation.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a342092829d8_34850454',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e1aeb48b01f4e85aa6bca8368bdb59a8436089e5' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\shop\\view\\widget\\reservation.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%SYSTEM%/admin/widget/paginator.tpl' => 1,
  ),
),false)) {
function content_68a342092829d8_34850454 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.dateformat.php','function'=>'smarty_modifier_dateformat',),));
?>
<div class="widget-filters">
    <div class="dropdown">
        <a id="reservation-switcher" data-toggle="dropdown" class="widget-dropdown-handle">
            <?php if ($_smarty_tpl->tpl_vars['filter']->value == 'open') {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>открытые<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>закрытые<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?> <i class="zmdi zmdi-chevron-down"></i>
        </a>
        <ul class="dropdown-menu" aria-labelledby="reservation-switcher">
            <li<?php if ($_smarty_tpl->tpl_vars['filter']->value == 'open') {?> class="act"<?php }?>><a data-update-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"shop-widget-reservation",'filter'=>"open"),$_smarty_tpl);?>
" class="call-update"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Открытые<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></li>
            <li<?php if ($_smarty_tpl->tpl_vars['filter']->value == 'close') {?> class="act"<?php }?>><a data-update-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"shop-widget-reservation",'filter'=>"close"),$_smarty_tpl);?>
" class="call-update"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Закрытые<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></li>
        </ul>
    </div>
</div>

<?php if (count($_smarty_tpl->tpl_vars['list']->value)) {?>
    <table class="wtable mrg overable clickable">
        <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <tr class="crud-edit clickable" data-crud-options='{ "updateThis": true }' data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"shop-reservationctrl",'do'=>"edit",'id'=>$_smarty_tpl->tpl_vars['item']->value['id']),$_smarty_tpl);?>
">
                    <td class="f-14">
                        <div class="title">
                            <span title="<?php echo $_smarty_tpl->tpl_vars['item']->value['__status']->textView();?>
" class="w-point<?php if ($_smarty_tpl->tpl_vars['item']->value['status'] == 'open') {?> bg-deeporange<?php } else { ?> bg-green<?php }?>"></span>
                            <b>№ <?php echo $_smarty_tpl->tpl_vars['item']->value['id'];?>
</b>
                        </div>
                        <div class="sub-title"><?php echo $_smarty_tpl->tpl_vars['item']->value['product_title'];?>
<br/><?php echo $_smarty_tpl->tpl_vars['item']->value['phone'];?>
</div>
                    </td>
                    <td class="w-date">
                        <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['item']->value['dateof'],"j %v %!Y");?>
<br>
                        <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['item']->value['dateof'],"@time");?>

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
ob_start();?>Нет ни одной предзаказа<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php }?>

<?php $_smarty_tpl->_subTemplateRender("rs:%SYSTEM%/admin/widget/paginator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('paginatorClass'=>"with-top-line"), 0, false);
}
}
