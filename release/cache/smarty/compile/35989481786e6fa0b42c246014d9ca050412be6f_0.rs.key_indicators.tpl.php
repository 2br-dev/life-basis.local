<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:59
  from 'D:\Projects\Hosts\life-basis.local\release\modules\statistic\view\blocks\key_indicators.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3420b1cc1e6_15603157',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '35989481786e6fa0b42c246014d9ca050412be6f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\statistic\\view\\blocks\\key_indicators.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a3420b1cc1e6_15603157 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.dateformat.php','function'=>'smarty_modifier_dateformat',),4=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.format_price.php','function'=>'smarty_modifier_format_price',),));
echo smarty_function_addjs(array('file'=>"flot/excanvas.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.resize.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.pie.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%statistic%/jquery.flot.orderbars.js"),$_smarty_tpl);?>


<div class="updatable stat-report" data-url="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" data-update-block-id="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
" data-update-replace="true">

    <?php if (!$_smarty_tpl->tpl_vars['param']->value['widget']) {?>
        <div class="viewport">
            <h2 class="stat-h2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Ключевые показатели<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></h2>

            <?php echo $_smarty_tpl->tpl_vars['period_selector']->value->render();?>


            <?php echo smarty_function_addjs(array('file'=>"%statistic%/diagram.js"),$_smarty_tpl);?>

            <div class="plot">
                <div class="graph"></div>
            </div>
            <?php echo '<script'; ?>
>
                $.allReady(function() {
                    statisticShowKeyIndicator("#<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
 .graph", <?php echo $_smarty_tpl->tpl_vars['json_bars']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['json_values']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['json_ticks']->value;?>
);
                });
            <?php echo '</script'; ?>
>
            <div class="stat-last-period">
                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Предыдущий период - с<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['period_selector']->value->getPrevDateFrom());?>
 <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>по<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['period_selector']->value->getPrevDateTo());?>

            </div>
        </div>
    <?php } else { ?>
        <?php echo $_smarty_tpl->tpl_vars['period_selector']->value->render();?>

    <?php }?>

    <?php if (empty($_smarty_tpl->tpl_vars['param']->value['no_list'])) {?>
        <div class="<?php if ($_smarty_tpl->tpl_vars['param']->value['widget']) {?>m-l-20 m-r-20 no-space<?php }?> m-b-20">
            <table border="0" class="<?php if ($_smarty_tpl->tpl_vars['param']->value['widget']) {?>wtable<?php } else { ?>rs-table<?php }?> stat-key-table overable-type2">
                <thead>
                    <tr>
                        <th class="l-w-space"></th>
                        <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выбранный период<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                        <th><?php if ($_smarty_tpl->tpl_vars['param']->value['widget']) {?><span title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>с<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['period_selector']->value->getPrevDateFrom());?>
 <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>по<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['period_selector']->value->getPrevDateTo());?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Предыдущий период<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span><?php } else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Значение за предыдущий период<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?></th>
                        <th class="r-w-space"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['raw_data']->value, 'row');
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>
                        <tr>
                            <td class="l-w-space"></td>
                            <td class="stat-nowrap">
                                <?php echo $_smarty_tpl->tpl_vars['row']->value['label'];?>
&nbsp;<?php if ($_smarty_tpl->tpl_vars['row']->value['help']) {?><span class="help-icon" title="<?php echo $_smarty_tpl->tpl_vars['row']->value['help'];?>
">?</span><?php }?><br>
                                <span class="stat-value"><?php echo smarty_modifier_format_price($_smarty_tpl->tpl_vars['row']->value['values'][1]);?>
 <small><?php echo $_smarty_tpl->tpl_vars['row']->value['unit'];?>
</small></span> <sup class="<?php if ($_smarty_tpl->tpl_vars['row']->value['percent'] < 0) {?>red<?php } else { ?>green<?php }?>"><?php if ($_smarty_tpl->tpl_vars['row']->value['percent'] < 0) {
echo $_smarty_tpl->tpl_vars['row']->value['percent'];
} else { ?>+<?php echo $_smarty_tpl->tpl_vars['row']->value['percent'];
}?>%</sup>
                            </td>
                            <td class="stat-nowrap"><?php echo smarty_modifier_format_price($_smarty_tpl->tpl_vars['row']->value['values'][0]);?>
 <small><?php echo $_smarty_tpl->tpl_vars['row']->value['unit'];?>
</small></td>
                            <td class="r-w-space"></td>
                        </tr>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </tbody>
            </table>
        </div>
    <?php }?>   
</div><?php }
}
