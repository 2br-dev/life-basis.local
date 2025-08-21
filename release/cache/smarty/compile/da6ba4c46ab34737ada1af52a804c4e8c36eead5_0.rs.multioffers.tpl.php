<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:43
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\adminblocks\offerblock\multioffers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b73c59715_61281027',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'da6ba4c46ab34737ada1af52a804c4e8c36eead5' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\adminblocks\\offerblock\\multioffers.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b73c59715_61281027 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\vendor\\smarty\\smarty\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
$_smarty_tpl->_assignInScope('multioffer_help_url', 'http://readyscript.ru/manual/catalog_products.html#catalog_multioffers');?>

<?php if (!empty($_smarty_tpl->tpl_vars['all_props']->value)) {?>
    <div id="multioffer-wrap">
        <div class="multioffer-warning">
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>&quot;Многомерные комплектации&quot; недоступны, т.к. у товара не добавлены или не отмеченны списковые характеристики.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <a href="<?php echo $_smarty_tpl->tpl_vars['multioffer_help_url']->value;?>
" target="_blank" class="how-to"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Подробнее...<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
        </div>
        <div id="multi-check-wrap">
            <input type="checkbox" id="use-multioffer" name="multioffers[use]" value="1" <?php if ($_smarty_tpl->tpl_vars['elem']->value->isMultiOffersUse()) {?>checked<?php }?>> 
            <label for="use-multioffer"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Использовать многомерные комплектации<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>. <span><a href="<?php echo $_smarty_tpl->tpl_vars['multioffer_help_url']->value;?>
" target="_blank" class="how-to"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Как использовать?<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></span></label>
        </div>

        <div class="multioffer-wrap">
            <div class="item">
                <table class="multioffer-table">
                    <tbody>
                        <tr>
                            <td class="is_photo">
                                <label><input type="radio" name="multioffers[is_photo]" value="0" checked="checked"/> <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>без фото<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></label>
                            </td>
                            <td class="key">
                                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Название параметра комплектации<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:
                            </td>
                            <td class="value">
                                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Списковые характеристики<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:
                            </td>
                            <td class="delete-level-td"></td>
                        </tr>
                    </tbody>
                    <tbody class="offers-body">
                        <?php if ($_smarty_tpl->tpl_vars['elem']->value->isMultiOffersUse()) {?>
                            <?php $_smarty_tpl->_assignInScope('m', 0);?>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value['multioffers']['levels'], 'level', false, 'k');
$_smarty_tpl->tpl_vars['level']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['k']->value => $_smarty_tpl->tpl_vars['level']->value) {
$_smarty_tpl->tpl_vars['level']->do_else = false;
?>
                                <tr class="line">
                                    <td class="is_photo">
                                        <label><input type="radio" name="multioffers[is_photo]" value="<?php echo $_smarty_tpl->tpl_vars['m']->value+1;?>
" <?php if ($_smarty_tpl->tpl_vars['level']->value['is_photo']) {?>checked="checked"<?php }?>/> <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>фото<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></label>
                                    </td>
                                    <td class="key">
                                       <input type="text" name="multioffers[levels][<?php echo $_smarty_tpl->tpl_vars['m']->value;?>
][title]" maxlength="255" value="<?php echo $_smarty_tpl->tpl_vars['level']->value['title'];?>
"/> 
                                    </td>
                                    <td class="value">
                                        <?php echo smarty_function_html_options(array('name'=>"multioffers[levels][".((string)$_smarty_tpl->tpl_vars['m']->value)."][prop]",'options'=>$_smarty_tpl->tpl_vars['all_props']->value,'selected'=>$_smarty_tpl->tpl_vars['level']->value['prop_id']),$_smarty_tpl);?>

                                    </td>
                                    <td class="delete-level-td">
                                        <a href="#" class="delete-level zmdi zmdi-close c-red" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                                    </td>
                                </tr>
                                <?php $_smarty_tpl->_assignInScope('m', $_smarty_tpl->tpl_vars['m']->value+1);?>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <div class="add-wrap">
                <div class="keyval-container" data-id=".multioffer-wrap .row">
                    <a class="btn btn-default va-m-c add-level" href="javascript:;">
                        <i class="zmdi zmdi-plus f-21 m-r-5"></i>
                        <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>добавить параметр<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                    </a>
                </div>
                <div>
                   <input type="checkbox" id="create-auto-offers" name="offers[create_autooffers]" value="1" > 
                   <label for="create-auto-offers"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Создавать комплектации<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <a class="help-icon"
                    title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Установите данный флаг, если есть необходимость изменения цены или количества товара для разных комплектаций<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">?</a></label> 
                </div>
                <div class="bottom-bar">
                    <input class="btn btn-default create-complexs" type="button" name="" value="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>создать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"/>
                </div>
            </div>
        </div>

        <?php echo '<script'; ?>
 type="text/x-tmpl" id="multioffer-line">
            <tr class="line">
                <td class="is_photo">
                    <label><input type="radio" name="multioffers[is_photo]" value="0"/> <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>фото<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></label>
                </td>
                <td class="key">
                   <input type="text" name="multioffers[levels][0][title]" maxlength="255"/> 
                </td>
                <td class="value">
                   <?php echo smarty_function_html_options(array('name'=>"multioffers[levels][0][prop]",'options'=>$_smarty_tpl->tpl_vars['all_props']->value,'data-prop-id'=>"0"),$_smarty_tpl);?>

                </td>
                <td class="delete-level-td">
                    <a href="#" class="delete-level zmdi zmdi-close c-red" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                </td>
            </tr>
        <?php echo '</script'; ?>
>
    </div>
<?php }
}
}
