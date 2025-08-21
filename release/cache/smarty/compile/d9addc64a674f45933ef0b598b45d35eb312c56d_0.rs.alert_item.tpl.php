<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\adminblocks\rsalerts\alert_item.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3180622a1e8_96560351',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd9addc64a674f45933ef0b598b45d35eb312c56d' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\adminblocks\\rsalerts\\alert_item.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a3180622a1e8_96560351 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.meter.php','function'=>'smarty_function_meter',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if ($_smarty_tpl->tpl_vars['rights']->value['main_alerts']) {?>
    <li>
        <a class="rs-alerts" data-urls='{ "list":"<?php echo smarty_function_adminUrl(array('mod_controller'=>"main-block-rsalerts",'alerts_do'=>"ajaxgetalerts"),$_smarty_tpl);?>
" }'>
            <i class="rs-icon rs-icon-mail">
                <?php ob_start();
if ($_smarty_tpl->tpl_vars['counter_status']->value == "warning") {
echo "bg-amber";
}
$_prefixVariable5=ob_get_clean();
echo smarty_function_meter(array('key'=>"rs-notice",'class'=>$_prefixVariable5),$_smarty_tpl);?>

                <i class="hi-count bg-amber">9</i>
            </i>
            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Уведомления<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
        </a>
    </li>
<?php }
}
}
