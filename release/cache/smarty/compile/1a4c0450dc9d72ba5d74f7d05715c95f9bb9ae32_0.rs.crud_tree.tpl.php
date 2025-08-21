<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:20
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\crud_tree.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a434e9e2_88797019',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1a4c0450dc9d72ba5d74f7d05715c95f9bb9ae32' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\crud_tree.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318a434e9e2_88797019 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),));
if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
<div class="crud-ajax-group">
    <div id="content-layout">
            <div class="updatable" data-url="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
">
<?php }?>
                <div class="top-toolbar viewport">
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

                <div class="columns">
                    <div class="column common-column">
                        <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['filter']))) {?>
                            <div class="beforetable-line">
                                <div class="filter-block">
                                    <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['filter']))) {?>
                                        <?php echo $_smarty_tpl->tpl_vars['elements']->value['filter']->getView();?>

                                    <?php }?>

                                    <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['filterContent']))) {?>
                                        <?php echo $_smarty_tpl->tpl_vars['elements']->value['filterContent'];?>

                                    <?php }?>
                                </div>
                            </div>
                        <?php }?>

                        <div class="viewport">
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['treeFilter']))) {?>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['treeFilter']->getPartsHtml();?>

                            <?php }?>
                        </div>

                        <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['tree']))) {?>
                            <form method="POST" enctype="multipart/form-data" action="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" class="crud-list-form">
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['tree']->getView();?>

                            </form>
                        <?php }?>

                        <?php if ($_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']) {?>
                            <div class="bottom-toolbar">
                                <div class="common-column viewport">
                                    <?php echo $_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']->getView();?>

                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div> <!-- .columns -->

<?php if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
            </div> <!-- .updatable -->
    </div> <!-- #content -->
</div>
<?php }
}
}
