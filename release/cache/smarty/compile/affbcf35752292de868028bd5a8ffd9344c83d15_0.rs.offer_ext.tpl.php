<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:45
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\adminblocks\offerblock\offer_ext.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b756dd378_19123136',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'affbcf35752292de868028bd5a8ffd9344c83d15' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\adminblocks\\offerblock\\offer_ext.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b756dd378_19123136 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.hook.php','function'=>'smarty_block_hook',),));
$_smarty_tpl->_assignInScope('currencies', $_smarty_tpl->tpl_vars['elem']->value->getCurrencies());?>

<div class="filter-line virtual-form" data-action="<?php echo smarty_function_adminUrl(array('do'=>false,'product_id'=>$_smarty_tpl->tpl_vars['product_id']->value,'offer_page_size'=>$_smarty_tpl->tpl_vars['offer_page_size']->value,'mod_controller'=>"catalog-block-offerblock"),$_smarty_tpl);?>
">
    <div class="filter-top">
        <a class="openfilter va-m-c" onclick="$(this).closest('.filter-line').toggleClass('open'); return false;">
            <i class="zmdi zmdi-search f-18"></i>
            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Фильтр<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
        </a>
        <?php if (count($_smarty_tpl->tpl_vars['filter_parts']->value) > 1) {?>
        <span class="part clean_all"><a class="clean" data-href="<?php echo smarty_function_adminUrl(array('do'=>false,'product_id'=>$_smarty_tpl->tpl_vars['product_id']->value,'mod_controller'=>"catalog-block-offerblock"),$_smarty_tpl);?>
"></a></span>            
        <?php }?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['filter_parts']->value, 'part');
$_smarty_tpl->tpl_vars['part']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['part']->value) {
$_smarty_tpl->tpl_vars['part']->do_else = false;
?>
            <span class="part"><?php echo $_smarty_tpl->tpl_vars['part']->value['text'];?>
<a class="clean" data-href="<?php echo $_smarty_tpl->tpl_vars['part']->value['clean_url'];?>
"></a></span>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
    <table class="filter-form">
        <tr>
            <td class="key"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Название<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
            <td class="val"><input type="text" name="offer_filter[title]" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value['title'];?>
"></td>
        </tr>
        <tr>
            <td class="key"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Артикул<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
            <td class="val"><input type="text" name="offer_filter[barcode]" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value['barcode'];?>
"></td>
        </tr>
        <tr>
            <td class="key"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Общий остаток<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
            <td class="val"><select name="offer_filter[cmp_num]">
                <option <?php if ($_smarty_tpl->tpl_vars['filter']->value['cmp_num'] == '=') {?>selected<?php }?>>=</option>
                <option <?php if ($_smarty_tpl->tpl_vars['filter']->value['cmp_num'] == '&lt;') {?>selected<?php }?>>&lt;</option>
                <option <?php if ($_smarty_tpl->tpl_vars['filter']->value['cmp_num'] == '&gt;') {?>selected<?php }?>>&gt;</option>
            </select>
            <input type="text" name="offer_filter[num]" value="<?php echo $_smarty_tpl->tpl_vars['filter']->value['num'];?>
">
            </td>
        </tr>
        <tr>
            <td></td>
            <td><span class="btn btn-primary virtual-submit"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Применить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></td>
        </tr>
    </table>
</div>

<div class="tools-top">
    <a class="btn btn-success add-offer va-m-c">
        <i class="zmdi zmdi-plus m-r-5 f-18"></i>
        <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавить комплектацию<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
    </a>
</div>

<div class="table-mobile-wrapper">
    <table class="rs-table editable-table offer-list localform" data-sort-request="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'offerMove','product_id'=>$_smarty_tpl->tpl_vars['product_id']->value),'catalog-block-offerblock');?>
" data-refresh-url="<?php echo smarty_function_adminUrl(array('do'=>false,'product_id'=>$_smarty_tpl->tpl_vars['product_id']->value,'offer_filter'=>$_smarty_tpl->tpl_vars['filter']->value,'offer_page'=>$_smarty_tpl->tpl_vars['paginator']->value->page,'offer_page_size'=>$_smarty_tpl->tpl_vars['offer_page_size']->value,'mod_controller'=>"catalog-block-offerblock"),$_smarty_tpl);?>
">
        <thead>
            <tr>
                <th class="chk" style="width:26px">
                    <div class="chkhead-block">
                        <input type="checkbox" data-name="offers[]" class="chk_head select-page" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отметить элементы на этой странице<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
                        <div class="onover">
                            <input type="checkbox" class="select-all" value="on" name="selectAll" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отметить элементы на всех страницах<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
                        </div>
                    </div>
                </th>
                <th class="drag" width="20"><span class="sortable sortdot asc"><span></span></span></th>
                <th class="title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Название<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th class="barcode"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Артикул<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th class="amount"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Остаток<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th class="price"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Цена<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th class="actions"></th>
            </tr>
        </thead>
        <tbody>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['offers']->value, 'offer', false, 'key');
$_smarty_tpl->tpl_vars['offer']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['offer']->value) {
$_smarty_tpl->tpl_vars['offer']->do_else = false;
?>
            <tr class="item" data-id="<?php echo $_smarty_tpl->tpl_vars['offer']->value['id'];?>
">
                <td class="chk"><input type="checkbox" name="offers[]" value="<?php echo $_smarty_tpl->tpl_vars['offer']->value['id'];?>
"></td>
                <td class="drag drag-handle"><a data-sortid="<?php echo $_smarty_tpl->tpl_vars['offer']->value['id'];?>
" class="sort dndsort">
                        <i class="zmdi zmdi-unfold-more"></i>
                    </a></td>
                <td class="title clickable"><?php echo $_smarty_tpl->tpl_vars['offer']->value['title'];?>
</td>
                <td class="barcode clickable"><?php echo $_smarty_tpl->tpl_vars['offer']->value['barcode'];?>
</td>
                <td class="amount"><?php echo $_smarty_tpl->tpl_vars['offer']->value['num'];?>
</td>
                <td class="price clickable">
                    <?php if ($_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['oneprice']['use']) {?>
                        <?php echo $_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['oneprice']['znak'];?>
 <?php echo (($tmp = $_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['oneprice']['original_value'] ?? null)===null||$tmp==='' ? "0" ?? null : $tmp);?>

                        <?php if ($_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['oneprice']['unit'] == '%') {?>%<?php } else {
echo $_smarty_tpl->tpl_vars['currencies']->value[$_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['oneprice']['unit']];
}?>
                    <?php } else { ?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value->getCostList(), 'onecost');
$_smarty_tpl->tpl_vars['onecost']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['onecost']->value) {
$_smarty_tpl->tpl_vars['onecost']->do_else = false;
?>
                            <?php if ($_smarty_tpl->tpl_vars['onecost']->value['type'] != 'auto') {?>
                                <div class="one-price">
                                    <?php echo (($tmp = $_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['price'][$_smarty_tpl->tpl_vars['onecost']->value['id']]['znak'] ?? null)===null||$tmp==='' ? "+" ?? null : $tmp);?>

                                    <?php echo (($tmp = $_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['price'][$_smarty_tpl->tpl_vars['onecost']->value['id']]['original_value'] ?? null)===null||$tmp==='' ? "0" ?? null : $tmp);?>

                                    <?php $_smarty_tpl->_assignInScope('unit', $_smarty_tpl->tpl_vars['offer']->value['pricedata_arr']['price'][$_smarty_tpl->tpl_vars['onecost']->value['id']]['unit']);?>
                                    <?php if ($_smarty_tpl->tpl_vars['unit']->value == '%') {?>%<?php } elseif ($_smarty_tpl->tpl_vars['unit']->value > 0) {
echo $_smarty_tpl->tpl_vars['currencies']->value[$_smarty_tpl->tpl_vars['unit']->value];
} else {
echo $_smarty_tpl->tpl_vars['currencies']->value[$_smarty_tpl->tpl_vars['default_currency']->value['id']];
}?>
                                     <?php echo $_smarty_tpl->tpl_vars['onecost']->value['title'];?>

                                </div>
                            <?php }?>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <?php }?>
                </td>
                <td class="actions">
                    <span class="loader"></span>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('hook', array('name'=>"catalog-product:offers-action",'title'=>"Карточка товара:действия с комплектациями",'offer'=>$_smarty_tpl->tpl_vars['offer']->value));
$_block_repeat=true;
echo smarty_block_hook(array('name'=>"catalog-product:offers-action",'title'=>"Карточка товара:действия с комплектациями",'offer'=>$_smarty_tpl->tpl_vars['offer']->value), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
                    <div class="inline-tools">
                        <a class="tool offer-edit" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Редактировать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-edit"></i></a>
                        <a class="tool offer-change-with-main" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>сделать основной<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-upload"></i></a>
                        <a class="tool offer-del" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-delete c-red"></i></a>
                    </div>
                    <?php $_block_repeat=false;
echo smarty_block_hook(array('name'=>"catalog-product:offers-action",'title'=>"Карточка товара:действия с комплектациями",'offer'=>$_smarty_tpl->tpl_vars['offer']->value), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </td>
            </tr>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php if (empty($_smarty_tpl->tpl_vars['offers']->value)) {?>
            <tr class="empty-row no-hover">
                <td colspan="7"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>нет дополнительных комплектаций<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>
</div>

<div class="tools-bottom">
    <div class="paginator virtual-form" data-action="<?php echo smarty_function_adminUrl(array('do'=>false,'product_id'=>$_smarty_tpl->tpl_vars['product_id']->value,'offer_filter'=>$_smarty_tpl->tpl_vars['filter']->value,'mod_controller'=>"catalog-block-offerblock"),$_smarty_tpl);?>
">
        <?php echo $_smarty_tpl->tpl_vars['paginator']->value->getView(array('is_virtual'=>true));?>

    </div>
</div>

<div class="group-toolbar">
    <span class="checked-offers"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отмеченные<br> значения:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
    <a class="btn btn-default edit"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Редактировать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
    <a class="btn btn-danger delete"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
</div><?php }
}
