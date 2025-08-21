<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:44
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\adminblocks\offerblock\offer_main.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b748ba432_05488715',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '201b5889686fe2d7df49f8e08f0b3156ce6053b0' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\adminblocks\\offerblock\\offer_main.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/admin/keyvaleditor.tpl' => 2,
  ),
),false)) {
function content_68a58b748ba432_05488715 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_assignInScope('config', \RS\Config\Loader::byModule('catalog'));?>
<h3><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Основная комплектация<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></h3>
<div class="main-offer main-offer-back" <?php if ($_smarty_tpl->tpl_vars['config']->value['inventory_control_enable']) {?>style="padding: 20px"<?php }?>>
    <input type="hidden" name="offers[main][id]" value="<?php echo $_smarty_tpl->tpl_vars['main_offer']->value['id'];?>
"/>
    <input name="offers[main][xml_id]" type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['main_offer']->value['xml_id'];?>
">
    <div class="table-mobile-wrapper">
    <table class="offer-table">
        <tbody class="">

        <?php if ($_smarty_tpl->tpl_vars['config']->value['inventory_control_enable']) {?>
            <p class="label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Название основной комплектации (используйте, если есть дополнительные комплектации)<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
            <input type="text" class="offers_title" name="offers[main][title]" value="<?php echo $_smarty_tpl->tpl_vars['main_offer']->value['title'];?>
"/><br/>
            <tr class="offer-table-head">
                <td class="no-border"></td>
                <td><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Доступно<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                <td><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Остаток<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                <td><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Резерв<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                <td><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Ожидание<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
            </tr>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['warehouses']->value, 'warehouse');
$_smarty_tpl->tpl_vars['warehouse']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['warehouse']->value) {
$_smarty_tpl->tpl_vars['warehouse']->do_else = false;
?>
            <?php $_smarty_tpl->_assignInScope('stocks', $_smarty_tpl->tpl_vars['main_offer']->value->getStocks());?>
            <tr class="offer-table-body">
                <td class="warehouse-title"><?php echo $_smarty_tpl->tpl_vars['warehouse']->value['title'];?>
</td>
                <td><?php echo (float)$_smarty_tpl->tpl_vars['stocks']->value[$_smarty_tpl->tpl_vars['warehouse']->value['id']]['stock'];?>
</td>
                <td><?php echo (float)$_smarty_tpl->tpl_vars['stocks']->value[$_smarty_tpl->tpl_vars['warehouse']->value['id']]['remains'];?>
</td>
                <td><?php echo (float)$_smarty_tpl->tpl_vars['stocks']->value[$_smarty_tpl->tpl_vars['warehouse']->value['id']]['reserve'];?>
</td>
                <td><?php echo (float)$_smarty_tpl->tpl_vars['stocks']->value[$_smarty_tpl->tpl_vars['warehouse']->value['id']]['waiting'];?>
</td>
            </tr>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <tr>
            <td colspan="5">
                <?php $_smarty_tpl->_subTemplateRender("rs:%system%/admin/keyvaleditor.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field_name'=>"offers[main][_propsdata]",'arr'=>$_smarty_tpl->tpl_vars['main_offer']->value['propsdata_arr'],'add_button_text'=>t('Добавить характеристику')), 0, false);
?>
            </td>
        </tr>
        <tr>
            <td class="images-row" colspan="5">
                <?php $_smarty_tpl->_assignInScope('images', $_smarty_tpl->tpl_vars['elem']->value->getImages());?>
                <div class="offer-images-line">
                    <?php if (!empty($_smarty_tpl->tpl_vars['images']->value)) {?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['images']->value, 'image');
$_smarty_tpl->tpl_vars['image']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['image']->value) {
$_smarty_tpl->tpl_vars['image']->do_else = false;
?>
                            <?php $_smarty_tpl->_assignInScope('is_act', is_array($_smarty_tpl->tpl_vars['main_offer']->value['photos_arr']) && in_array($_smarty_tpl->tpl_vars['image']->value['id'],$_smarty_tpl->tpl_vars['main_offer']->value['photos_arr']));?>
                            <a data-id="<?php echo $_smarty_tpl->tpl_vars['image']->value['id'];?>
" data-name="offers[main][photos_arr][]" class="<?php if ($_smarty_tpl->tpl_vars['is_act']->value) {?>act<?php }?>"><img src="<?php echo $_smarty_tpl->tpl_vars['image']->value->getUrl(30,30,'xy');?>
"/></a>
                            <?php if ($_smarty_tpl->tpl_vars['is_act']->value) {?><input type="hidden" name="offers[main][photos_arr][]" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id'];?>
"><?php }?>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <?php }?>
                </div>
            </td >
        </tr>
        <?php } else { ?>
            <tr>
               <td class="td title-td col2" rowspan="2">
                    <p class="label"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Название основной комплектации (используйте, если есть дополнительные комплектации)<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
                    <input type="text" class="offers_title" name="offers[main][title]" value="<?php echo $_smarty_tpl->tpl_vars['main_offer']->value['title'];?>
"/><br/>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['warehouses']->value, 'warehouse');
$_smarty_tpl->tpl_vars['warehouse']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['warehouse']->value) {
$_smarty_tpl->tpl_vars['warehouse']->do_else = false;
?>
                        <p class="label">"<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['title'];?>
" - <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>остаток<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
                        <input name="offers[main][stock_num][<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['id'];?>
]" type="text" value="<?php echo $_smarty_tpl->tpl_vars['main_offer']->value['stock_num'][$_smarty_tpl->tpl_vars['warehouse']->value['id']];?>
"/><br/>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                    <?php echo $_smarty_tpl->tpl_vars['other_fields_form']->value;?>

                </td>
                <td class="td keyval-td col3">
                    <?php $_smarty_tpl->_subTemplateRender("rs:%system%/admin/keyvaleditor.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field_name'=>"offers[main][_propsdata]",'arr'=>$_smarty_tpl->tpl_vars['main_offer']->value['propsdata_arr'],'add_button_text'=>t('Добавить характеристику')), 0, true);
?>
                </td>
            </tr>
            <tr>
                <td class="images-row" style="padding-left: 20px">
                   <?php $_smarty_tpl->_assignInScope('images', $_smarty_tpl->tpl_vars['elem']->value->getImages());?>
                      <div class="offer-images-line">
                      <?php if (!empty($_smarty_tpl->tpl_vars['images']->value)) {?>
                          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['images']->value, 'image');
$_smarty_tpl->tpl_vars['image']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['image']->value) {
$_smarty_tpl->tpl_vars['image']->do_else = false;
?>
                             <?php $_smarty_tpl->_assignInScope('is_act', is_array($_smarty_tpl->tpl_vars['main_offer']->value['photos_arr']) && in_array($_smarty_tpl->tpl_vars['image']->value['id'],$_smarty_tpl->tpl_vars['main_offer']->value['photos_arr']));?>
                             <a data-id="<?php echo $_smarty_tpl->tpl_vars['image']->value['id'];?>
" data-name="offers[main][photos_arr][]" class="<?php if ($_smarty_tpl->tpl_vars['is_act']->value) {?>act<?php }?>"><img src="<?php echo $_smarty_tpl->tpl_vars['image']->value->getUrl(30,30,'xy');?>
"/></a>
                             <?php if ($_smarty_tpl->tpl_vars['is_act']->value) {?><input type="hidden" name="offers[main][photos_arr][]" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id'];?>
"><?php }?>
                          <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                      <?php }?>
                      </div>
                </td>
            </tr>
        <?php }?>
        </tbody>
    </table>
    </div>
</div><?php }
}
