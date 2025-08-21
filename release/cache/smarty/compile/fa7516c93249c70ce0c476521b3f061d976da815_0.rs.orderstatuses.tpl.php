<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:56
  from 'D:\Projects\Hosts\life-basis.local\release\modules\shop\view\widget\orderstatuses.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34208e21be9_29688626',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fa7516c93249c70ce0c476521b3f061d976da815' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\shop\\view\\widget\\orderstatuses.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a34208e21be9_29688626 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"flot/excanvas.js",'basepath'=>"common",'before'=>"<!--[if lte IE 8]>",'after'=>"<![endif]-->"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.tooltip.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.resize.js",'basepath'=>"common",'waitbefore'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.pie.js",'basepath'=>"common",'waitbefore'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%shop%/orderstatuses.js",'basepath'=>"root",'waitbefore'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"%shop%/orderstatuses.css"),$_smarty_tpl);?>


<div class="order-statuses">
    <?php if ($_smarty_tpl->tpl_vars['total']->value) {?>
        <div id="orderStatusesGraph" class="graph" style="height:300px"></div>
        <div class="flc-orderStatusesLegend"></div>
        
        <div class="orderStatusesData">
            <table width="100%">
                <tr align="center" style="font-weight:bold">
                    <td width="33%"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Всего<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                    <td width="33%"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Открыто<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                    <td width="33%"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Завершено<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                </tr>
                <tr align="center">
                    <td><?php echo $_smarty_tpl->tpl_vars['total']->value;?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['inwork']->value;?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['finished']->value;?>
</td>
                </tr>            
            </table>
        </div>
        <?php echo '<script'; ?>
>
            $.allReady(function() {
                var data = <?php echo $_smarty_tpl->tpl_vars['json_data']->value;?>
;
                initOrderStatusesWidget(data);
            });
        <?php echo '</script'; ?>
>    
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
</div><?php }
}
