<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:02:04
  from 'D:\Projects\Hosts\life-basis.local\release\modules\users\view\notice\touser_register_sms.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e36c214876_69911007',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '76a5f7141cf18921dca874744d780822a4cba3c3' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\users\\view\\notice\\touser_register_sms.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e36c214876_69911007 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Вы успешно зарегистрированы!<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Логин<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>: <?php echo (($tmp = (($tmp = $_smarty_tpl->tpl_vars['data']->value->user['login'] ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['data']->value->user['e_mail'] ?? null : $tmp) ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['data']->value->user['phone'] ?? null : $tmp);?>

<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Пароль<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>: <?php echo $_smarty_tpl->tpl_vars['data']->value->password;?>

<?php echo $_smarty_tpl->tpl_vars['url']->value->getDomainStr();
}
}
