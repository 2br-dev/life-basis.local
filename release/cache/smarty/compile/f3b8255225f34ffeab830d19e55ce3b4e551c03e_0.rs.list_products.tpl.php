<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:10:13
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\list_products.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31825019b72_34424063',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f3b8255225f34ffeab830d19e55ce3b4e551c03e' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\list_products.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/one_product.tpl' => 1,
    'rs:%catalog%/one_table_product.tpl' => 1,
    'rs:%catalog%/list_products_paginator.tpl' => 1,
  ),
),false)) {
function content_68a31825019b72_34424063 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, array (
  'emptyList' => 
  array (
    'compiled_filepath' => 'D:\\Projects\\Hosts\\life-basis.local\\release\\cache\\smarty\\compile\\f3b8255225f34ffeab830d19e55ce3b4e551c03e_0.rs.list_products.tpl.php',
    'uid' => 'f3b8255225f34ffeab830d19e55ce3b4e551c03e',
    'call_name' => 'smarty_template_function_emptyList_167514963268a31824e5c381_18914439',
  ),
));
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),));
echo smarty_function_addjs(array('file'=>"core6/rsplugins/ajaxpaginator.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%catalog%/rscomponent/listproducts.js"),$_smarty_tpl);?>

<?php $_smarty_tpl->_assignInScope('list', $_smarty_tpl->tpl_vars['this_controller']->value->api->addProductsDirs($_smarty_tpl->tpl_vars['list']->value));
$_smarty_tpl->_assignInScope('list', $_smarty_tpl->tpl_vars['this_controller']->value->api->addProductsMultiOffersInfo($_smarty_tpl->tpl_vars['list']->value));?>
<div id="products">
    

    <?php if (count($_smarty_tpl->tpl_vars['list']->value) || $_smarty_tpl->tpl_vars['is_filter_active']->value) {?>
        <?php if (!in_array($_smarty_tpl->tpl_vars['view_as']->value,array('blocks','table'))) {
$_smarty_tpl->_assignInScope('view_as', 'blocks');
}?>
        <div class="mb-4">
            <div class="row align-items-center g-md-4 g-lg-5 g-3">
                <div class="col-sm-auto <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['filter_view_variant'] == 'visible') {?>d-xl-none<?php }?>">
                    <a role="button" class="offcanvas-open catalog-filter-btn" data-source=".rs-filter-wrapper">
                        <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.1048 4.60967H9.327C9.0032 3.64795 8.09355 2.95312 7.02404 2.95312C5.95453 2.95312 5.04488 3.64795 4.72108 4.60967H2.88266C2.4556 4.60967 2.10938 4.95589 2.10938 5.38296C2.10938 5.81002 2.4556 6.15624 2.88266 6.15624H4.72113C5.04494 7.11796 5.95458 7.81279 7.02409 7.81279C8.0936 7.81279 9.00325 7.11796 9.32705 6.15624H21.1048C21.5319 6.15624 21.8781 5.81002 21.8781 5.38296C21.8781 4.95589 21.5319 4.60967 21.1048 4.60967ZM7.02404 6.26621C6.53702 6.26621 6.14079 5.86997 6.14079 5.38296C6.14079 4.89594 6.53702 4.4997 7.02404 4.4997C7.51106 4.4997 7.90729 4.89594 7.90729 5.38296C7.90729 5.86997 7.51106 6.26621 7.02404 6.26621Z" />
                            <path d="M21.1048 11.2356H19.2663C18.9425 10.2739 18.0328 9.5791 16.9633 9.5791C15.8939 9.5791 14.9842 10.2739 14.6604 11.2356H2.88266C2.4556 11.2356 2.10938 11.5819 2.10938 12.0089C2.10938 12.436 2.4556 12.7822 2.88266 12.7822H14.6604C14.9842 13.7439 15.8939 14.4388 16.9634 14.4388C18.0328 14.4388 18.9425 13.7439 19.2663 12.7822H21.1048C21.5319 12.7822 21.8781 12.436 21.8781 12.0089C21.8781 11.5819 21.5319 11.2356 21.1048 11.2356ZM16.9634 12.8922C16.4764 12.8922 16.0801 12.4959 16.0801 12.0089C16.0801 11.5219 16.4764 11.1257 16.9634 11.1257C17.4504 11.1257 17.8466 11.5219 17.8466 12.0089C17.8466 12.4959 17.4504 12.8922 16.9634 12.8922Z" />
                            <path d="M21.1048 17.8616H12.6401C12.3163 16.8999 11.4067 16.2051 10.3372 16.2051C9.26766 16.2051 8.35802 16.8999 8.03422 17.8616H2.88266C2.4556 17.8616 2.10938 18.2078 2.10938 18.6349C2.10938 19.062 2.4556 19.4082 2.88266 19.4082H8.03422C8.35802 20.3699 9.26766 21.0647 10.3372 21.0647C11.4067 21.0647 12.3163 20.3699 12.6401 19.4082H21.1048C21.5319 19.4082 21.8781 19.062 21.8781 18.6349C21.8781 18.2078 21.5319 17.8616 21.1048 17.8616ZM10.3372 19.5182C9.85016 19.5182 9.45392 19.122 9.45392 18.635C9.45392 18.1479 9.85016 17.7517 10.3372 17.7517C10.8242 17.7517 11.2204 18.1479 11.2204 18.6349C11.2204 19.1219 10.8242 19.5182 10.3372 19.5182Z" />
                        </svg>
                        <?php $_smarty_tpl->_assignInScope('total_filter_count', count($_smarty_tpl->tpl_vars['filter']->value)+count($_smarty_tpl->tpl_vars['bfilter']->value));?>
                        <span class="ms-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Фильтры<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php if ($_smarty_tpl->tpl_vars['total_filter_count']->value) {?>(<?php echo $_smarty_tpl->tpl_vars['total_filter_count']->value;?>
)<?php }?></span>
                    </a>
                </div>
                <div class="col">
                    <div class="catalog-bar">
                        <div class="catalog-select">
                            <div class="catalog-select__label d-none d-md-block"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Сортировать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:</div>
                            <div class="catalog-select__options">
                                <select class="rs-list-sort-change">
                                    <option value="sortn" data-nsort="asc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'sortn') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>умолчанию<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <option value="cost" data-nsort="asc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'cost' && $_smarty_tpl->tpl_vars['cur_n']->value == 'asc') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>возрастанию цены<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <option value="cost" data-nsort="desc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'cost' && $_smarty_tpl->tpl_vars['cur_n']->value == 'desc') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>убыванию цены<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <option value="rating" data-nsort="desc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'rating') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>популярности<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <option value="dateof" data-nsort="desc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'dateof') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>новизне<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <option value="num" data-nsort="desc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'num') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>наличию<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <option value="title" data-nsort="asc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'title') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>названию<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <?php if ($_smarty_tpl->tpl_vars['can_rank_sort']->value) {?>
                                        <option value="rank" data-nsort="asc" <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == 'rank') {?>selected<?php }?>><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>релевантности<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></option>
                                    <?php }?>
                                </select>
                                <div class="catalog-select__value"></div>
                            </div>
                        </div>
                        <ul class="catalog-view-as ms-3">
                            <li>
                                <a class="rs-list-view-change <?php if ($_smarty_tpl->tpl_vars['view_as']->value == 'blocks') {?>view-as_active<?php }?>" data-view="blocks">
                                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M18.2191 13.6393C17.4905 13.6393 16.866 13.0147 16.866 12.2861C16.866 11.5575 17.4905 10.933 18.2191 10.933C18.9478 10.933 19.5723 11.5575 19.5723 12.2861C19.5723 13.0147 18.9478 13.6393 18.2191 13.6393ZM18.2191 7.70628C17.4905 7.70628 16.866 7.08174 16.866 6.35313C16.866 5.62451 17.4905 5 18.2191 5C18.9478 5 19.5723 5.62451 19.5723 6.35313C19.5723 7.08174 18.9478 7.70628 18.2191 7.70628ZM12.2861 19.5723C11.5575 19.5723 10.933 18.9478 10.933 18.2191C10.933 17.4905 11.5575 16.866 12.2861 16.866C13.0147 16.866 13.6393 17.4905 13.6393 18.2191C13.6393 18.9478 13.0147 19.5723 12.2861 19.5723ZM12.2861 13.6393C11.5575 13.6393 10.933 13.0147 10.933 12.2861C10.933 11.5575 11.5575 10.933 12.2861 10.933C13.0147 10.933 13.6393 11.5575 13.6393 12.2861C13.6393 13.0147 13.0147 13.6393 12.2861 13.6393ZM12.2861 7.70628C11.5575 7.70628 10.933 7.08174 10.933 6.35313C10.933 5.62451 11.5575 5 12.2861 5C13.0147 5 13.6393 5.62451 13.6393 6.35313C13.6393 7.08174 13.0147 7.70628 12.2861 7.70628ZM6.35313 19.5723C5.62451 19.5723 5 18.9478 5 18.2191C5 17.4905 5.62451 16.866 6.35313 16.866C7.08174 16.866 7.70628 17.4905 7.70628 18.2191C7.70628 18.9478 7.08174 19.5723 6.35313 19.5723ZM6.35313 13.6393C5.62451 13.6393 5 13.0147 5 12.2861C5 11.5575 5.62451 10.933 6.35313 10.933C7.08174 10.933 7.70628 11.5575 7.70628 12.2861C7.70628 13.0147 7.08174 13.6393 6.35313 13.6393ZM6.35313 7.70628C5.62451 7.70628 5 7.08174 5 6.35313C5 5.62451 5.62451 5 6.35313 5C7.08174 5 7.70628 5.62451 7.70628 6.35313C7.70628 7.08174 7.08174 7.70628 6.35313 7.70628ZM18.2191 16.866C18.9478 16.866 19.5723 17.4905 19.5723 18.2191C19.5723 18.9478 18.9478 19.5723 18.2191 19.5723C17.4905 19.5723 16.866 18.9478 16.866 18.2191C16.866 17.4905 17.4905 16.866 18.2191 16.866Z" />
                                    </svg>
                                </a>
                            </li>
                            <li>
                                <a class="rs-list-view-change <?php if ($_smarty_tpl->tpl_vars['view_as']->value == 'table') {?>view-as_active<?php }?>" data-view="table">
                                    <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M4.25 6C4.25 5.58579 4.58579 5.25 5 5.25H19C19.4142 5.25 19.75 5.58579 19.75 6C19.75 6.41421 19.4142 6.75 19 6.75H5C4.58579 6.75 4.25 6.41421 4.25 6ZM4.25 12C4.25 11.5858 4.58579 11.25 5 11.25H19C19.4142 11.25 19.75 11.5858 19.75 12C19.75 12.4142 19.4142 12.75 19 12.75H5C4.58579 12.75 4.25 12.4142 4.25 12ZM4.25 18C4.25 17.5858 4.58579 17.25 5 17.25H19C19.4142 17.25 19.75 17.5858 19.75 18C19.75 18.4142 19.4142 18.75 19 18.75H5C4.58579 18.75 4.25 18.4142 4.25 18Z" />
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['list']->value) {?>
            <?php if ($_smarty_tpl->tpl_vars['view_as']->value == 'blocks') {?>
                <div class="item-card-container">
                    <div class="row <?php if ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['filter_view_variant'] == 'visible') {?>row-cols-xxl-4<?php } else { ?>row-cols-xxl-5 row-cols-xl-4<?php }?> row-cols-md-3 row-cols-2 g-0 g-md-4 rs-products-list">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                            <div>
                                <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/one_product.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                            </div>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="item-list-container rs-products-list">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'product');
$_smarty_tpl->tpl_vars['product']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->do_else = false;
?>
                        <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/one_table_product.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </div>
            <?php }?>

            <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/list_products_paginator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

            <?php if ($_smarty_tpl->tpl_vars['category']->value['description'] && ($_smarty_tpl->tpl_vars['THEME_SETTINGS']->value['category_description_place'] == 'after_products') && $_smarty_tpl->tpl_vars['paginator']->value->page == 1) {?>
                <div class="mt-4">
                    <?php echo $_smarty_tpl->tpl_vars['category']->value['description'];?>

                </div>
            <?php }?>

        <?php } else { ?>
            <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "По вашему запросу ничего не найдено. Проверьте правильность установленных фильтров";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable2=ob_get_clean();
ob_start();
echo smarty_function_urlmake(array('filters'=>null,'pf'=>null,'bfilter'=>null,'p'=>null),$_smarty_tpl);
$_prefixVariable3=ob_get_clean();
ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Сбросить фильтры";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable4=ob_get_clean();
$_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'emptyList', array('reason'=>$_prefixVariable2,'button_link'=>$_prefixVariable3,'button_text'=>$_prefixVariable4), true);?>

        <?php }?>
    <?php } else { ?>
        <?php if ($_smarty_tpl->tpl_vars['query']->value === '') {?>
            <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "В этой категории нет товаров. Попробуйте найти ваш товар в другой категории.";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable5=ob_get_clean();
$_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'emptyList', array('button_link'=>false,'reason'=>$_prefixVariable5), true);?>

        <?php } else { ?>
            <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "По вашему запросу ничего не найдено. Проверьте правильность введенного запроса";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable6=ob_get_clean();
$_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'emptyList', array('reason'=>$_prefixVariable6), true);?>

        <?php }?>
    <?php }?>
</div><?php }
/* smarty_template_function_emptyList_167514963268a31824e5c381_18914439 */
if (!function_exists('smarty_template_function_emptyList_167514963268a31824e5c381_18914439')) {
function smarty_template_function_emptyList_167514963268a31824e5c381_18914439(Smarty_Internal_Template $_smarty_tpl,$params) {
foreach ($params as $key => $value) {
$_smarty_tpl->tpl_vars[$key] = new Smarty_Variable($value, $_smarty_tpl->isRenderingCache);
}
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>

        <div class="text-center mt-6 container col-lg-4 col-md-6 col-sm-8">
            <div class="mb-lg-6 mb-4">
                <img class="empty-page-img" src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/decorative/search.svg" alt="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Ничего не найдено<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
            </div>
            <p class="mb-lg-6 mb-5"><?php echo $_smarty_tpl->tpl_vars['reason']->value;?>
</p>
            <?php if ($_smarty_tpl->tpl_vars['button_link']->value) {?>
                <a href="<?php echo (($tmp = $_smarty_tpl->tpl_vars['button_link']->value ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl() ?? null : $tmp);?>
" class="btn btn-primary"><?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "На главную";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable1=ob_get_clean();
echo (($tmp = $_smarty_tpl->tpl_vars['button_text']->value ?? null)===null||$tmp==='' ? $_prefixVariable1 ?? null : $tmp);?>
</a>
            <?php }?>
        </div>
    <?php
}}
/*/ smarty_template_function_emptyList_167514963268a31824e5c381_18914439 */
}
