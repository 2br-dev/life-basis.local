<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:23:00
  from 'D:\Projects\Hosts\life-basis.local\release\modules\modcontrol\view\admin\col_enabled.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a329341b8362_47177237',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '773160f4245462da4a82701e2fa8e6e2493cc79b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\modcontrol\\view\\admin\\col_enabled.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a329341b8362_47177237 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
if ($_smarty_tpl->tpl_vars['cell']->value->getRow('installed')) {?>
    <?php if ($_smarty_tpl->tpl_vars['cell']->value->getValue()) {?>Да<?php } else { ?>Нет<?php }
} else { ?>
    <a class="not_installed crud-get" href="<?php echo smarty_function_adminUrl(array('do'=>'ajaxreinstall','module'=>$_smarty_tpl->tpl_vars['cell']->value->getRow('class')),$_smarty_tpl);?>
" title="Нажмите, чтобы установить модуль" style="white-space:nowrap">Не установлен</a>
<?php }
}
}
