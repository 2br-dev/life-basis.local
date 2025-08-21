<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:40
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\weight.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b70805c32_82119386',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fd7d45c3aeab4d6ffb02773f392426ad104010b4' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\weight.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b70805c32_82119386 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('config', \RS\Config\Loader::byModule('catalog'));
$_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['field']->value->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?> <?php echo $_smarty_tpl->tpl_vars['config']->value->getShortWeightUnit();?>
.<?php }
}
