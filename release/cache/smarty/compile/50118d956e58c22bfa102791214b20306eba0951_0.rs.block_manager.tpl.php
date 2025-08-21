<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:39
  from 'D:\Projects\Hosts\life-basis.local\release\modules\templates\view\admin\block_manager.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31803d98908_60741704',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '50118d956e58c22bfa102791214b20306eba0951' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\templates\\view\\admin\\block_manager.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%templates%/gs/".((string)$_smarty_tpl->tpl_vars[\'grid_system\']->value)."/pageview.tpl' => 1,
  ),
),false)) {
function content_68a31803d98908_60741704 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_js']->value)."jquery.blockeditor.js",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_css']->value)."manager.css",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_css']->value)."moduleblocks.css",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/960gs/960.fluid.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
<div class="crud-ajax-group">
        <div class="viewport">
            <div class="updatable default-updatable" data-url="<?php echo smarty_function_adminUrl(array('context'=>$_smarty_tpl->tpl_vars['context']->value,'page_id'=>$_smarty_tpl->tpl_vars['page_id']->value),$_smarty_tpl);?>
">
<?php }?>
                <div class="top-toolbar">
                    <div class="c-head">
                        <?php $_smarty_tpl->_assignInScope('mainMenuIndex', $_smarty_tpl->tpl_vars['elements']->value->getMainMenuIndex());?>
                        <h2 class="title">
                            <span class="go-to-menu" <?php if ($_smarty_tpl->tpl_vars['mainMenuIndex']->value !== false) {?>data-main-menu-index="<?php echo $_smarty_tpl->tpl_vars['mainMenuIndex']->value;?>
"<?php }?>><?php echo $_smarty_tpl->tpl_vars['elements']->value['formTitle'];?>
</span>
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['topHelp']))) {?><a class="help-icon" data-toggle-class="open" data-target-closest=".top-toolbar">?</a><?php }?></h2>

                        <div class="buttons xs-dropdown place-left">
                            <a class="btn btn-default toggle visible-xs-inline-block" data-toggle="dropdown"  aria-haspopup="true" aria-expanded="false" id="clientHeadButtons" >
                                <i class="zmdi zmdi-more-vert"><!----></i>
                            </a>
                            <div class="xs-dropdown-menu" aria-labelledby="clientHeadButtons">
                                <?php if ($_smarty_tpl->tpl_vars['elements']->value['topToolbar']) {?>
                                    <?php echo $_smarty_tpl->tpl_vars['elements']->value['topToolbar']->getView();?>

                                <?php }?>
                            </div>
                        </div>
                    </div>

                    <div class="c-help notice notice-warning">
                        <?php echo $_smarty_tpl->tpl_vars['elements']->value['topHelp'];?>

                    </div>

                    <?php echo $_smarty_tpl->tpl_vars['elements']->value['headerHtml'];?>

                </div>

                <div class="context-setup">
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Контекст<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <span class="help-icon" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>В разном контексте могут быть различные настройки страниц, секций, блоков. Контексты могут привносить в систему различные модули, например, партнерский модуль. Так, для каждого партнера можно задать собственную разметку.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">?</span>:
                    <span class="dropdown">
                        <span class="btn btn-default" data-toggle="dropdown">
                            <?php echo $_smarty_tpl->tpl_vars['context_list']->value[$_smarty_tpl->tpl_vars['context']->value]['title'];?>

                            <span class="caret"></span>
                        </span>
                        <?php if (count($_smarty_tpl->tpl_vars['context_list']->value) > 1) {?>
                        <ul class="dropdown-menu">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['context_list']->value, 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                <?php if ($_smarty_tpl->tpl_vars['context']->value != $_smarty_tpl->tpl_vars['key']->value) {?>
                                <li>
                                    <a href="<?php echo smarty_function_adminUrl(array('context'=>$_smarty_tpl->tpl_vars['key']->value),$_smarty_tpl);?>
" class="block-link-item"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</a>
                                </li>
                                <?php }?>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </ul>
                        <?php }?>
                    </span>

                    <a href="<?php echo smarty_function_adminUrl(array('do'=>"contextOptions",'context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);?>
" class="crud-edit btn btn-default">
                        <i class="zmdi zmdi-settings m-r-5"></i>
                        <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки темы оформления<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                    </a>
                </div>


                <div class="columns">
                    <div class="common-column">

                        <div class="rs-tabs" role="tabpanel">
                            <ul class="tab-nav resizable-column" role="tablist" data-min-width="280">
                                <li <?php if ($_smarty_tpl->tpl_vars['currentPage']->value['route_id'] == 'default') {?>class="active"<?php }?>>
                                    <span class="item">
                                        <a class="call-update" href="<?php echo smarty_function_adminUrl(array('context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>По умолчанию<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                                        <a class="crud-edit tool zmdi zmdi-settings" href="<?php echo smarty_function_adminUrl(array('do'=>"editPage",'context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки страницы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                                        <a class="crud-edit tool zmdi zmdi-search-for" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"pageseo-ctrl",'do'=>"edit",'id'=>"default",'context'=>$_smarty_tpl->tpl_vars['context']->value,'create'=>1),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки SEO<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                                    </span>
                                </li>

                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['pages']->value, 'page');
$_smarty_tpl->tpl_vars['page']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['page']->value) {
$_smarty_tpl->tpl_vars['page']->do_else = false;
?>
                                    <?php if ($_smarty_tpl->tpl_vars['page']->value['route_id'] != 'default') {?>
                                        <li <?php if ($_smarty_tpl->tpl_vars['currentPage']->value['id'] == $_smarty_tpl->tpl_vars['page']->value['id']) {?>class="active"<?php }?>>
                                            <span class="item">
                                                <a class="call-update" href="<?php echo smarty_function_adminUrl(array('page_id'=>$_smarty_tpl->tpl_vars['page']->value['id'],'context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);?>
"><?php if ($_smarty_tpl->tpl_vars['page']->value->getRoute() !== null) {
echo $_smarty_tpl->tpl_vars['page']->value->getRoute()->getDescription();
} else {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Маршрут не найден <span class="help-icon" title="<?php echo $_smarty_tpl->tpl_vars['page']->value['route_id'];?>
">?</span><?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?></a>
                                                <a class="crud-edit tool zmdi zmdi-settings" href="<?php echo smarty_function_adminUrl(array('do'=>"editPage",'id'=>$_smarty_tpl->tpl_vars['page']->value['id']),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки страницы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                                                <a class="crud-edit tool zmdi zmdi-search-for" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"pageseo-ctrl",'do'=>"edit",'id'=>$_smarty_tpl->tpl_vars['page']->value['route_id'],'create'=>1),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки SEO<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                                                <a class="crud-remove-one tool zmdi zmdi-close c-red" href="<?php echo smarty_function_adminUrl(array('do'=>"delPage",'id'=>$_smarty_tpl->tpl_vars['page']->value['id']),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                                            </span>
                                        </li>
                                    <?php }?>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </ul>

                            <div class="tab-content depend-resizable-column">
                                <?php $_smarty_tpl->_subTemplateRender("rs:%templates%/gs/".((string)$_smarty_tpl->tpl_vars['grid_system']->value)."/pageview.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                            </div>
                        </div>
                    </div>
                </div> <!-- .columns -->

<?php if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
            </div> <!-- .updatable -->
        </div> <!-- .viewport -->
</div>

<?php echo '<script'; ?>
>
    $.contentReady(function() {
        $('.pageview').blockEditor({
            sortContainerUrl: '<?php echo smarty_function_adminUrl(array('do'=>"ajaxMoveContainer",'ajax'=>1),$_smarty_tpl);?>
',
            sortSectionUrl: '<?php echo smarty_function_adminUrl(array('do'=>"ajaxMoveSection",'ajax'=>1),$_smarty_tpl);?>
',
            sortBlockUrl: '<?php echo smarty_function_adminUrl(array('do'=>"ajaxMoveBlock",'ajax'=>1),$_smarty_tpl);?>
',
            toggleViewBlock: '<?php echo smarty_function_adminUrl(array('do'=>"ajaxToggleViewModule",'ajax'=>1),$_smarty_tpl);?>
',
            gridSystem: '<?php echo $_smarty_tpl->tpl_vars['grid_system']->value;?>
'
        });
    });
<?php echo '</script'; ?>
>
<?php }
}
}
