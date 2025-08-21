<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:10:10
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\layout.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318221f3935_54060448',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dc07bf26a9845eb1a65b7dc366163c054a96c33c' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\layout.tpl',
      1 => 1754993321,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318221f3935_54060448 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addmeta.php','function'=>'smarty_function_addmeta',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),));
echo smarty_function_addcss(array('file'=>"master.css"),$_smarty_tpl);?>


<?php echo smarty_function_addmeta(array('http-equiv'=>"Content-Security-Policy",'content'=>"object-src 'self' docs.google.com ajax.googleapis.com; script-src * 'unsafe-inline' 'unsafe-eval' "),$_smarty_tpl);?>

<?php echo smarty_function_addmeta(array('charset'=>"UTF-8"),$_smarty_tpl);?>

<?php echo smarty_function_addmeta(array('name'=>"viewport",'content'=>"width=device-width, initial-scale=1.0"),$_smarty_tpl);?>


<?php echo smarty_function_addjs(array('file'=>"master.js"),$_smarty_tpl);?>


<?php echo $_smarty_tpl->tpl_vars['app']->value->blocks->renderLayout();?>
	<?php }
}
