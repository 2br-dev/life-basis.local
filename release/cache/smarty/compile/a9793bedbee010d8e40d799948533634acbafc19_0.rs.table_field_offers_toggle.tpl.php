<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:34
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\table_field_offers_toggle.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b6ad7daa5_23813087',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a9793bedbee010d8e40d799948533634acbafc19' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\table_field_offers_toggle.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b6ad7daa5_23813087 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
$_smarty_tpl->_assignInScope('product', $_smarty_tpl->tpl_vars['cell']->value->getRow());
$_smarty_tpl->_assignInScope('table', $_smarty_tpl->tpl_vars['cell']->value->getContainer());
if (count($_smarty_tpl->tpl_vars['product']->value->getOffers()) > 1) {?>
    <i class="offer-block-toggle zmdi" data-id="<?php echo $_smarty_tpl->tpl_vars['product']->value['id'];?>
" data-url-load-offers="<?php echo smarty_function_adminUrl(array('do'=>"getOffersTableData",'product_id'=>$_smarty_tpl->tpl_vars['product']->value['id']),$_smarty_tpl);?>
"></i>
<?php }
}
}
