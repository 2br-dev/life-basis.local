<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:02:03
  from 'D:\Projects\Hosts\life-basis.local\release\modules\users\view\notice\touser_register.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e36b43efc4_00209865',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '65b3a6f0d338dfb5f58f2b5dbd61cb3fac37b202' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\users\\view\\notice\\touser_register.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e36b43efc4_00209865 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_42625420468a5e36b4114b2_96227003', "content");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "%alerts%/notice_template.tpl");
}
/* {block "content"} */
class Block_42625420468a5e36b4114b2_96227003 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_42625420468a5e36b4114b2_96227003',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>

     <?php ob_start();
echo (($tmp = (($tmp = $_smarty_tpl->tpl_vars['data']->value->user['login'] ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['data']->value->user['e_mail'] ?? null : $tmp) ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['data']->value->user['phone'] ?? null : $tmp);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('alias'=>"Сообщение пользователю регистрация",'site'=>$_smarty_tpl->tpl_vars['url']->value->getDomainStr(),'login'=>$_prefixVariable1,'pass'=>$_smarty_tpl->tpl_vars['data']->value->password,'user_link'=>$_smarty_tpl->tpl_vars['router']->value->getUrl('users-front-profile',array(),true)));
$_block_repeat=true;
echo smarty_block_t(array('alias'=>"Сообщение пользователю регистрация",'site'=>$_smarty_tpl->tpl_vars['url']->value->getDomainStr(),'login'=>$_prefixVariable1,'pass'=>$_smarty_tpl->tpl_vars['data']->value->password,'user_link'=>$_smarty_tpl->tpl_vars['router']->value->getUrl('users-front-profile',array(),true)), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>

     <h1>Вы успешно зарегистрированы!</h1>
     <p>Мы рады приветствовать Вас на сайте %site.</p>

     <p>Логин: %login<br>
     Пароль: %pass</p>

     <p>Используйте этот логин и пароль для входа в личный кабинет по адресу <a href="%user_link">%user_link</a>.<br>
     В личном кабинете можно посмотреть историю Ваших заказов, их текущие статусы, а также написать письмо в службу поддержки клиентов.</p>

     <p>Чтобы сократить время оформления заказа, Вы можете использовать этот логин и пароль при следующем оформлении заказа.<br>
     Пожалуйста обратите внимание, что Вы можете изменять пароль в любое время редактируя ваш Профиль.</p><?php $_block_repeat=false;
echo smarty_block_t(array('alias'=>"Сообщение пользователю регистрация",'site'=>$_smarty_tpl->tpl_vars['url']->value->getDomainStr(),'login'=>$_prefixVariable1,'pass'=>$_smarty_tpl->tpl_vars['data']->value->password,'user_link'=>$_smarty_tpl->tpl_vars['router']->value->getUrl('users-front-profile',array(),true)), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
}
/* {/block "content"} */
}
