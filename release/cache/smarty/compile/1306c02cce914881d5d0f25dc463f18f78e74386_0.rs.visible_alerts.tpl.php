<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\adminblocks\rsvisiblealerts\visible_alerts.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31806a53355_35743926',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1306c02cce914881d5d0f25dc463f18f78e74386' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\adminblocks\\rsvisiblealerts\\visible_alerts.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31806a53355_35743926 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if ($_smarty_tpl->tpl_vars['visible_alerts']->value->canShow()) {?>
<div class="alert alert-warning viewport m-b-20 c-black visible-alerts-block">
    <a class="pull-right close" style="line-height:100%" data-cookie-name="<?php echo $_smarty_tpl->tpl_vars['cookie_param_name']->value;?>
" data-cookie-value="<?php echo $_smarty_tpl->tpl_vars['messages_hash']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['timestamp']->value;?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?><nobr>Скрыть на 14 дней</nobr><?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">&times;</a>

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['visible_alerts']->value->getMessages(), 'message_data');
$_smarty_tpl->tpl_vars['message_data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['message_data']->value) {
$_smarty_tpl->tpl_vars['message_data']->do_else = false;
?>
        <?php echo $_smarty_tpl->tpl_vars['message_data']->value['message'];?>

        <?php if ($_smarty_tpl->tpl_vars['message_data']->value['href']) {?>
            <a class="u-link" href="<?php echo $_smarty_tpl->tpl_vars['message_data']->value['href'];?>
" <?php if ($_smarty_tpl->tpl_vars['message_data']->value['target']) {?>target="<?php echo $_smarty_tpl->tpl_vars['message_data']->value['target'];?>
"<?php }?>><?php echo $_smarty_tpl->tpl_vars['message_data']->value['link_title'];?>
</a>
        <?php }?><br>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</div>
<?php }
}
}
