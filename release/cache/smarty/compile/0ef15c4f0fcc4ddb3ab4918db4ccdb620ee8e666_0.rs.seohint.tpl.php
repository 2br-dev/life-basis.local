<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:38
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\hint\seohint.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b6e45dbd6_92791414',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0ef15c4f0fcc4ddb3ab4918db4ccdb620ee8e666' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\hint\\seohint.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b6e45dbd6_92791414 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('alias'=>"Подсказка по SEO генератору"));
$_block_repeat=true;
echo smarty_block_t(array('alias'=>"Подсказка по SEO генератору"), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В этом поле Вы можете использовать переменные, вместо которых будут<br/> 
подставлены соотвествующие значения:  <br/><br/>
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['hints']->value, 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?>
    &nbsp;&nbsp;<b>{<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
}</b> - <?php echo $_smarty_tpl->tpl_vars['value']->value;?>
 <br/>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><br/>
<b>Характеристики:</b><br/>
&nbsp;&nbsp;<b>{prop.id}</b> - Значение характеристики, где id - это номер характеристики<br/> 
<br/>
Сокращайте заменяемый текст с помощью конструкции {title|100},<br/>
где - 100 это количество символов от начала в значении,<br/>
a title - это доступное поле
<br/><br/>
<b>{title|.|3}</b> - эта конструкция обрежет заголовок(поле title)<br/> 
предложение до третьей точки (".") включительно.<br/>
Первый аргумент после "|" это поле объекта, второй символ поиска, третий число вхождений.<?php $_block_repeat=false;
echo smarty_block_t(array('alias'=>"Подсказка по SEO генератору"), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
}
