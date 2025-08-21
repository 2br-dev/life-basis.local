<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:34
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\image.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b6ae36fd8_00814961',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'de61e8715c86b9e59e525f0f7154db311ff43913' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\image.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b6ae36fd8_00814961 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),));
echo smarty_function_addjs(array('file'=>"jquery.rs.tableimage.js",'basepath'=>"common"),$_smarty_tpl);?>

<span class="cell-image" data-preview-url="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getPreviewUrl();?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getImageSrc();?>
"></span><?php }
}
