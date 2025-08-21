<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:51
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\admin\widget\widget_wrapper.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34203efb2b2_17357689',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e41358d6d05f913ae2968f322557b72ecb9071d' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\admin\\widget\\widget_wrapper.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a34203efb2b2_17357689 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
?>
<div class="widget" wid="<?php echo $_smarty_tpl->tpl_vars['widget']->value['id'];?>
" wclass="<?php echo $_smarty_tpl->tpl_vars['widget']->value['class'];?>
" data-positions='<?php echo $_smarty_tpl->tpl_vars['widget']->value['item']->getPositionsJson();?>
'>
<?php echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxBefore();?>

    <div class="widget-border">
        <div class="widget-head">
            <div class="widget-title"><?php echo $_smarty_tpl->tpl_vars['widget']->value['title'];?>
</div>
            <div class="widget-tools">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['widget']->value['self']->getTools(), 'tool');
$_smarty_tpl->tpl_vars['tool']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['tool']->value) {
$_smarty_tpl->tpl_vars['tool']->do_else = false;
?>
                    <a <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tool']->value, 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
if ($_smarty_tpl->tpl_vars['key']->value[0] == '~') {?> <?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['key']->value,"~",'');?>
='<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
'<?php } else { ?> <?php echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"<?php }
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>></a>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <a class="widget-close zmdi zmdi-close" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Скрыть виджет<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
            </div>
        </div>  
        <div class="widget-content updatable" data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>$_smarty_tpl->tpl_vars['widget']->value['class'],'do'=>false),$_smarty_tpl);?>
" data-update-block-id="<?php echo $_smarty_tpl->tpl_vars['widget']->value['class'];?>
">
            <?php echo $_smarty_tpl->tpl_vars['widget']->value['inside_html'];?>

        </div>
    </div>
<?php echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxAfter();?>
    
</div><?php }
}
