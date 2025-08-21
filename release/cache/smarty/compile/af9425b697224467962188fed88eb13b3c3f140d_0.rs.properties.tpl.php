<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:41
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\properties.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b71b25724_11067076',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'af9425b697224467962188fed88eb13b3c3f140d' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\properties.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/form/product/property_form.tpl' => 1,
    'rs:%catalog%/form/product/property_group_product.tpl' => 1,
  ),
),false)) {
function content_68a58b71b25724_11067076 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addcss(array('file'=>"%catalog%/property.css?v=2"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%catalog%/property.js?v=2"),$_smarty_tpl);?>


<div data-name="tab2" id="propertyblock" data-owner-type="product" 
    data-get-property-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-propctrl",'do'=>"ajaxGetPropertyList"),$_smarty_tpl);?>
" 
    data-save-property-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-propctrl",'do'=>"ajaxCreateOrUpdateProperty"),$_smarty_tpl);?>
"
    data-get-property-value-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-propctrl",'do'=>"ajaxGetPropertyValueList"),$_smarty_tpl);?>
"
    data-create-property-value-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-propctrl",'do'=>"ajaxAddPropertyValue"),$_smarty_tpl);?>
"
    data-remove-property-value-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-propctrl",'do'=>"ajaxRemovePropertyValue"),$_smarty_tpl);?>
"
    >
    <div class="property-tools">
        <div class="property-actions">
            <a class="add-property underline va-m-c"><i class="zmdi zmdi-plus m-r-5"></i> <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавить характеристику<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></a>
            <span class="success-text"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Характеристика успешно добавлена<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
        </div>
        <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/form/product/property_form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('value_types'=>$_smarty_tpl->tpl_vars['field']->value->callPropertyFunction('getPropertyItemAllowTypeData')), 0, false);
?>
    </div>
    <div class="floatwrap">
        <a class="set-self-val underline va-m-c">
            <i class="zmdi zmdi-check-all m-r-5 f-17"></i>
            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Задать индивидуальные значения всем характеристикам<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
        </a>
    </div>
    
    <table class="property-container has-tools">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value->getPropObjects(), 'group', false, 'key');
$_smarty_tpl->tpl_vars['group']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->do_else = false;
?>
            <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/form/product/property_group_product.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('group'=>$_smarty_tpl->tpl_vars['group']->value), 0, true);
?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </table>
</div><?php }
}
