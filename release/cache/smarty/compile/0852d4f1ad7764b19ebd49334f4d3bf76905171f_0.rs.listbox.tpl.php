<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:26
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\listbox.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318aabe1a18_78120176',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0852d4f1ad7764b19ebd49334f4d3bf76905171f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\listbox.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/coreobject/type/form/block_error.tpl' => 1,
  ),
),false)) {
function content_68a318aabe1a18_78120176 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.rshtml_options.php','function'=>'smarty_function_rshtml_options',),));
if ($_smarty_tpl->tpl_vars['field']->value->isHaveAttrKey('listFilter')) {?>     <?php echo smarty_function_addjs(array('file'=>"jquery.rs.selectfilter.js",'basepath'=>"common"),$_smarty_tpl);?>

    <div class="selectFilterWrapper">
        <div class="selectFilter">
            <input type="text" class="filter" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Фильтр<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"/>
        </div>
<?php }?>
        <?php $_smarty_tpl->_assignInScope('options', $_smarty_tpl->tpl_vars['field']->value->getList());?>
        <select name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
>
            <?php echo smarty_function_rshtml_options(array('options'=>$_smarty_tpl->tpl_vars['options']->value,'selected'=>$_smarty_tpl->tpl_vars['field']->value->get()),$_smarty_tpl);?>

        </select>
<?php if ($_smarty_tpl->tpl_vars['field']->value->getAttrByKey('listFilter')) {?>
    </div>
<?php }
$_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/block_error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
