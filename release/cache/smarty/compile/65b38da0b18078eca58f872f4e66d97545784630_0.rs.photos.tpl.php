<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:46
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\photos.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b767b9815_82201148',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '65b38da0b18078eca58f872f4e66d97545784630' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\photos.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b767b9815_82201148 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
echo smarty_function_moduleinsert(array('name'=>"\Photo\Controller\Admin\BlockPhotos",'type'=>"catalog",'linkid'=>$_smarty_tpl->tpl_vars['elem']->value['id'],'indexTemplate'=>"%catalog%/form/product/photo/form_product.tpl",'photoTemplate'=>"%catalog%/form/product/photo/form_onepic_product.tpl"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\photos.tpl');
}
}
