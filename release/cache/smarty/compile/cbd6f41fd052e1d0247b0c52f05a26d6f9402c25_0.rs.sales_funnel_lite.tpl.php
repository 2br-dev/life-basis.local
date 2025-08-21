<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:59
  from 'D:\Projects\Hosts\life-basis.local\release\modules\statistic\view\blocks\sales_funnel_lite.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3420ba20649_25962442',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cbd6f41fd052e1d0247b0c52f05a26d6f9402c25' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\statistic\\view\\blocks\\sales_funnel_lite.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a3420ba20649_25962442 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<div class="updatable stat-report" data-url="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" data-update-block-id="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
" data-update-replace="true">
    <?php echo $_smarty_tpl->tpl_vars['period_selector']->value->render();?>

    
    <?php if ($_smarty_tpl->tpl_vars['percents']->value) {?>
        <div class="plot stat-funnel m-b-10">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['percents']->value, 'val', false, 'key');
$_smarty_tpl->tpl_vars['val']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['val']->value) {
$_smarty_tpl->tpl_vars['val']->do_else = false;
?>
                <div class="stat-funnel__row">
                    <div class="stat-funnel__legend"><?php echo $_smarty_tpl->tpl_vars['titles']->value[$_smarty_tpl->tpl_vars['key']->value];?>
</div>
                    <div class="stat-funnel__funnel">
                        <div class="stat-funnel__bar" style="width: <?php echo $_smarty_tpl->tpl_vars['percents']->value[$_smarty_tpl->tpl_vars['key']->value];?>
%"></div>
                        <div class="stat-funnel__number"><?php echo $_smarty_tpl->tpl_vars['counts']->value[$_smarty_tpl->tpl_vars['key']->value];?>
</div>
                    </div>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    <?php } else { ?>
        <div class="stat-nodata"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет ни одной записи за выбранный период<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
    <?php }?>
</div><?php }
}
