<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:21:33
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\crud_table_tree.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a328dd5a54a1_89229205',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '109b64aa86d9d89afe86f081939d002ca2f92e51' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\crud_table_tree.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a328dd5a54a1_89229205 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
<div class="crud-ajax-group crud-view-table-tree <?php if ($_COOKIE['viewas'] == 'table') {?>left-up<?php }?>">
            <div class="updatable" data-url="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
">
<?php }?>
                <div class="top-toolbar viewport">
                    <div class="c-head">
                        <?php $_smarty_tpl->_assignInScope('mainMenuIndex', $_smarty_tpl->tpl_vars['elements']->value->getMainMenuIndex());?>
                        <h2 class="title">
                            <span class="go-to-menu" <?php if ($_smarty_tpl->tpl_vars['mainMenuIndex']->value !== false) {?>data-main-menu-index="<?php echo $_smarty_tpl->tpl_vars['mainMenuIndex']->value;?>
"<?php }?>><?php echo $_smarty_tpl->tpl_vars['elements']->value['formTitle'];?>
</span> <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['topHelp']))) {?><a class="help-icon" data-toggle-class="open" data-target-closest=".top-toolbar">?</a><?php }?></h2>

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

                <?php if ($_smarty_tpl->tpl_vars['elements']->value['tree']) {?>
                    <div class="collapsed viewport">
                            <div class="path">
                                <i class="zmdi zmdi-folder-outline icon" data-toggle-class="left-open" data-target-closest=".crud-view-table-tree"></i>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['tree']->getPathView();?>

                            </div>
                            <div class="buttons">
                                <a class="select-tree btn btn-default" data-toggle-class="left-open" data-target-closest=".crud-view-table-tree">
                                    <span class="tree-show"><i class="zmdi zmdi-chevron-right"></i></span>
                                    <span class="tree-hide"><i class="zmdi zmdi-close"></i></span>
                                </a>
                            </div>
                    </div>
                <?php }?>

                <div class="columns">
                    <div class="column left-column">
                        <div class="filter-back" data-filter-placement="left">
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['treeFilter']))) {?>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['treeFilter']->getView();?>

                            <?php }?>
                        </div>

                        <div class="viewport">
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['treeFilter']))) {?>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['treeFilter']->getPartsHtml();?>

                            <?php }?>
                        </div>

                        <form method="POST" enctype="multipart/form-data" action="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" id="tree-form" class="twisted-left">
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['tree']))) {?>
                                <?php $_smarty_tpl->_assignInScope('local_options', array());?>
                                <?php $_smarty_tpl->_assignInScope('forced_open_nodes', false);?>
                                <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['treeFilter']))) {?>
                                    <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['local_options']) ? $_smarty_tpl->tpl_vars['local_options']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['filter'] = $_smarty_tpl->tpl_vars['elements']->value['treeFilter'];
$_smarty_tpl->_assignInScope('local_options', $_tmp_array);?>

                                    <?php if ($_smarty_tpl->tpl_vars['elements']->value['treeFilter']->getKeyVal()) {?>
                                        <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['local_options']) ? $_smarty_tpl->tpl_vars['local_options']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['forced_open_nodes'] = true;
$_smarty_tpl->_assignInScope('local_options', $_tmp_array);?>
                                    <?php }?>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
                                    <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['local_options']) ? $_smarty_tpl->tpl_vars['local_options']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['render_opened_nodes'] = true;
$_smarty_tpl->_assignInScope('local_options', $_tmp_array);?>
                                <?php }?>

                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['tree']->getView($_smarty_tpl->tpl_vars['local_options']->value);?>

                            <?php }?>

                            <?php if ($_smarty_tpl->tpl_vars['elements']->value['treeBottomToolbar']) {?>
                            <div class="bottom-toolbar">
                                <div class="viewport">
                                    <?php echo $_smarty_tpl->tpl_vars['elements']->value['treeBottomToolbar']->getView();?>

                                </div>
                            </div>
                            <?php }?>
                        </form>
                    </div> <!-- .left-column -->

                    <div class="column right-column">
                        <div class="beforetable-line">
                            <div class="view-control">
                                <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['paginator']))) {?>
                                    <?php echo $_smarty_tpl->tpl_vars['elements']->value['paginator']->getView(array('short'=>true));?>

                                <?php }?>
                                <div class="view-switcher">
                                    <a class="view-as-tree-table" data-remove-class="left-up"
                                                                  data-target-closest=".crud-view-table-tree"
                                                                  data-set-cookie="viewas"
                                                                  data-set-cookie-value="table-tree"
                                                                  data-set-cookie-path="."
                                                                  title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Категории слева<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>

                                    <a class="view-as-table" data-add-class="left-up"
                                                             data-target-closest=".crud-view-table-tree"
                                                             data-set-cookie="viewas"
                                                             data-set-cookie-value="table"
                                                             data-set-cookie-path=""
                                                             title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Категории сверху<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                                </div>
                            </div>
                            <div class="filter-control" data-filter-placement="right">
                                <div class="filter-block">
                                    <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['filter']))) {?>
                                        <?php echo $_smarty_tpl->tpl_vars['elements']->value['filter']->getView();?>

                                    <?php }?>

                                    <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['filterContent']))) {?>
                                        <?php echo $_smarty_tpl->tpl_vars['elements']->value['filterContent'];?>

                                    <?php }?>
                                </div>
                            </div>


                        </div>

                        <div class="viewport">
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['filter']))) {?>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['filter']->getPartsHtml();?>

                            <?php }?>
                        </div>

                        <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['table']))) {?>
                            <form method="POST" enctype="multipart/form-data" action="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" class="crud-list-form">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elements']->value['hiddenFields'], 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
">
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['table']->getView();?>

                            </form>
                        <?php }?>

                        <div class="viewport">
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['paginator']))) {?>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['paginator']->getView();?>

                            <?php }?>
                        </div>

                        <?php if ($_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']) {?>
                            <div class="bottom-toolbar">
                                <div class="viewport">
                                    <?php echo $_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']->getView();?>

                                </div>
                            </div>
                        <?php }?>
                    </div> <!-- .right-column -->
                </div> <!-- .columns -->
<?php if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
            </div> <!-- .updatable -->
</div>
<?php }
}
}
