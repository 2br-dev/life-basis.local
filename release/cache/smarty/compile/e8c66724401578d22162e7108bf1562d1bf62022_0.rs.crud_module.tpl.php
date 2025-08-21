<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:43:31
  from 'D:\Projects\Hosts\life-basis.local\release\modules\modcontrol\view\admin\crud_module.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31ff32ed998_14192123',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e8c66724401578d22162e7108bf1562d1bf62022' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\modcontrol\\view\\admin\\crud_module.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31ff32ed998_14192123 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxBefore();?>

<div class="crud-ajax-group">
    <?php if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
    <div id="content-layout">
        <div class="updatable" data-url="<?php echo smarty_function_adminUrl(array('mod'=>$_smarty_tpl->tpl_vars['module_item']->value->getName()),$_smarty_tpl);?>
">
    <?php }?>
            <div class="viewport">
                <a class="titlebox gray-around va-m-c" data-side-panel="<?php echo smarty_function_adminUrl(array('do'=>"ajaxModuleList",'mod_controller'=>"modcontrol-control"),$_smarty_tpl);?>
">
                    <i class="zmdi zmdi-tag-more f-20 m-r-10" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Все модули<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></i>
                    <span><?php echo $_smarty_tpl->tpl_vars['elements']->value['formTitle'];?>
</span>
                </a>

                <div class="middlebox">
                    <div class="crud-form-error">
                        <?php if (count($_smarty_tpl->tpl_vars['elements']->value['formErrors'])) {?>
                            <ul class="error-list">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elements']->value['formErrors'], 'data');
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
                                    <li>
                                        <div class="<?php echo (($tmp = $_smarty_tpl->tpl_vars['data']->value['class'] ?? null)===null||$tmp==='' ? "field" ?? null : $tmp);?>
"><?php echo $_smarty_tpl->tpl_vars['data']->value['fieldname'];?>
<i class="cor"></i></div>
                                        <div class="text">
                                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['errors'], 'error');
$_smarty_tpl->tpl_vars['error']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['error']->value) {
$_smarty_tpl->tpl_vars['error']->do_else = false;
?>
                                            <?php echo $_smarty_tpl->tpl_vars['error']->value;?>

                                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                        </div>
                                    </li>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </ul>
                        <?php }?>
                    </div>
                    <div class="crud-form-success text-success"></div>

                    <?php if ($_smarty_tpl->tpl_vars['module_item']->value->getConfig()->installed) {?>
                        <div class="columns">

                            <div class="form-column">
                                <?php $_smarty_tpl->_assignInScope('config', $_smarty_tpl->tpl_vars['module_item']->value->getConfig());?>
                                <?php if (!$_smarty_tpl->tpl_vars['config']->value->isMultisiteConfig()) {?>
                                    <br><div class="notice-box"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки данного модуля едины для всех сайтов в рамках мультисайтовости<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
                                <?php }?>

                                <?php $_smarty_tpl->_assignInScope('level', '');?>
                                <?php $_smarty_tpl->_assignInScope('license_text', $_smarty_tpl->tpl_vars['module_license_api']->value->getLicenseDataText($_smarty_tpl->tpl_vars['module_item']->value->getName(),$_smarty_tpl->tpl_vars['level']->value));?>
                                <?php if (!$_smarty_tpl->tpl_vars['config']->value['is_system'] && $_smarty_tpl->tpl_vars['license_text']->value) {?>
                                    <br><div class="notice m-b-10 text-<?php echo $_smarty_tpl->tpl_vars['level']->value;?>
">
                                        <?php echo $_smarty_tpl->tpl_vars['license_text']->value;?>

                                    </div>
                                <?php }?>

                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['form'];?>

                            </div>

                            <div class="tools-column">
                                <div class="controller_info">
                                    <h3><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Утилиты<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></h3>
                                    <a name="actions"></a>
                                    <ul class="list-with-help">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['module_item']->value->getTools(), 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                            <li>
                                                <a <?php if ((isset($_smarty_tpl->tpl_vars['item']->value['target']))) {?>target="<?php echo $_smarty_tpl->tpl_vars['item']->value['target'];?>
"<?php }?> <?php if (!empty($_smarty_tpl->tpl_vars['item']->value['confirm'])) {?>data-confirm-text="<?php echo $_smarty_tpl->tpl_vars['item']->value['confirm'];?>
"<?php }?> class="<?php if ($_smarty_tpl->tpl_vars['item']->value['class']) {
echo $_smarty_tpl->tpl_vars['item']->value['class'];
} else { ?>crud-get<?php }?>" href="<?php echo $_smarty_tpl->tpl_vars['item']->value['url'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['attr']) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value['attr'], 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?> <?php echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}?> style="text-decoration:underline">
                                                    <?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>

                                                </a>
                                                <?php if ($_smarty_tpl->tpl_vars['item']->value['description']) {?><div class="tool-descr"><?php echo $_smarty_tpl->tpl_vars['item']->value['description'];?>
</div><?php }?>
                                            </li>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    <?php } else { ?>
                        <div class="inform-block margvert10">
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Модуль не установлен.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <a href="<?php echo smarty_function_adminUrl(array('do'=>'ajaxreinstall','module'=>$_smarty_tpl->tpl_vars['module_item']->value->getName()),$_smarty_tpl);?>
" class="u-link crud-get"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Установить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                        </div>
                    <?php }?>
                </div>
            </div>

            <div class="footerspace"></div>
            <div class="bottom-toolbar fixed">
                <div class="viewport">
                    <div class="common-column">
                        <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']))) {?>
                            <?php echo $_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']->getView();?>

                        <?php }?>
                    </div>
                </div>
            </div>

    <?php if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
            </div>
    </div> <!-- .content -->
    <?php }?>
</div>
<?php echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxAfter();
}
}
