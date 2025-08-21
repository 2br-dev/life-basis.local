<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:39
  from 'D:\Projects\Hosts\life-basis.local\release\modules\templates\view\help\blockctrl_index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31803bf4154_79278629',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2e19c22d35fd65be6c33689f7cfc0f4bf3ed5f29' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\templates\\view\\help\\blockctrl_index.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31803bf4154_79278629 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('alias'=>"В этом разделе можно задать расположение блоков на различных страницах.."));
$_block_repeat=true;
echo smarty_block_t(array('alias'=>"В этом разделе можно задать расположение блоков на различных страницах.."), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В этом разделе можно задать расположение блоков на различных страницах.
Блок - это видимая часть модуля, которая может отображать пользователю определенную информацию. 
Каждый модуль может содержать множество блоков, например модуль "Каталог" может содержать блоки: "список категорий", "список товаров в категории", "посление просмотренные товары", и т.д.
<p>Секции необходимы, чтобы задавать структуру страницы по сетке и удерживать блоки или вложенные секции согласно заданной ширине.</p>
<?php $_block_repeat=false;
echo smarty_block_t(array('alias'=>"В этом разделе можно задать расположение блоков на различных страницах.."), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
}
