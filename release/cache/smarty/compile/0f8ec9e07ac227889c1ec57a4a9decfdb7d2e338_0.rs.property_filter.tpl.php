<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:34
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\filter\property_filter.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b6aba2360_14173835',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0f8ec9e07ac227889c1ec57a4a9decfdb7d2e338' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\filter\\property_filter.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/filter/view_filter.tpl' => 1,
  ),
),false)) {
function content_68a58b6aba2360_14173835 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<div class="property-filter <?php if ($_smarty_tpl->tpl_vars['fitem']->value->isActiveFilter()) {?>property-filter-open"<?php }?>">

    <a class="property-filter-toggle"
       data-toggle-class="property-filter-open"
       data-target-closest=".property-filter"><i class="zmdi"></i> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>искать по характеристикам<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>

    <div class="property-filter-forms">
        <?php $_smarty_tpl->_assignInScope('cat_properties', $_smarty_tpl->tpl_vars['fitem']->value->getProperties());?>
        <?php if ($_smarty_tpl->tpl_vars['cat_properties']->value) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cat_properties']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <div class="form-group">
                    <label><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</label><br>
                    <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/filter/view_filter.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('prop'=>$_smarty_tpl->tpl_vars['item']->value), 0, true);
?>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php } else { ?>
            <div class="no-category-properties"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>У выбранной категории нет характеристик<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
        <?php }?>
    </div>
</div><?php }
}
