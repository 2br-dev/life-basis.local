<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:50
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\admin\widget\widgets.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3420247a9d2_67714854',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cfa207bb6782ed9b30967e11b51cf841a4c93497' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\admin\\widget\\widgets.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a3420247a9d2_67714854 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),4=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
echo smarty_function_addjs(array('file'=>"%main%/widgetengine.js"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"%main%/widgetstyle.css?v=2"),$_smarty_tpl);?>


<div class="viewport<?php if (!$_smarty_tpl->tpl_vars['total']->value) {?> empty<?php }
if ($_smarty_tpl->tpl_vars['total']->value > 1) {?> cansort<?php }?>" id="widgets-block" data-widget-urls='{ "widgetList": "<?php echo smarty_function_adminUrl(array('do'=>"GetWidgetList"),$_smarty_tpl);?>
", "addWidget":"<?php echo smarty_function_adminUrl(array('do'=>"ajaxAddWidget"),$_smarty_tpl);?>
", "removeWidget":"<?php echo smarty_function_adminUrl(array('do'=>"ajaxRemoveWidget"),$_smarty_tpl);?>
", "moveWidget": "<?php echo smarty_function_adminUrl(array('do'=>"ajaxMoveWidget"),$_smarty_tpl);?>
" }'>
    <div id="noWidgetText">

        <p class="text"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройте<br><span class="small">свой рабочий стол</span><?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
        <div class="welcome-disk">
            <a class="<?php if ($_smarty_tpl->tpl_vars['can_add_widget']->value) {?>addwidget<?php }?>"><img src="<?php echo $_smarty_tpl->tpl_vars['mod_img']->value;?>
/nowidgets.png"></a>
        </div>

    </div>
    <div id="widget-zone">
        <div class="widget-column" data-column="1">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['widgets']->value, 'widget');
$_smarty_tpl->tpl_vars['widget']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['widget']->value) {
$_smarty_tpl->tpl_vars['widget']->do_else = false;
?>
                <?php echo smarty_function_moduleinsert(array('name'=>$_smarty_tpl->tpl_vars['widget']->value->getFullClass(),'widget'=>$_smarty_tpl->tpl_vars['widget']->value),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\modules\main\view\admin\widget\widgets.tpl');?>

            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
        <div class="widget-column" data-column="2"></div>
        <div class="widget-column" data-column="3"></div>
    </div>
    <a class="btn btn-default btn-lg btn-alt widget-change-position">
        <span class="change"><i class="zmdi zmdi-arrows"></i> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Изменить порядок виджетов<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
        <span class="save"><i class="zmdi zmdi-save"></i> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Сохранить порядок виджетов<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
    </a>
</div><?php }
}
