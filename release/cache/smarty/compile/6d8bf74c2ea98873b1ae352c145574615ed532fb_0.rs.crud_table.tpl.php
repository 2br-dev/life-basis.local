<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:17
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\crud_table.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e30198a641_34854579',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6d8bf74c2ea98873b1ae352c145574615ed532fb' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\crud_table.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e30198a641_34854579 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),));
if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
<div class="crud-ajax-group crud-view-table">
            <div class="updatable" data-url="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
">
<?php }?>
                <div class="top-toolbar viewport">
                    <div class="c-head">
                        <?php $_smarty_tpl->_assignInScope('mainMenuIndex', $_smarty_tpl->tpl_vars['elements']->value->getMainMenuIndex());?>
                        <h2 class="title">
                            <span class="go-to-menu titlebox" <?php if ($_smarty_tpl->tpl_vars['mainMenuIndex']->value !== false) {?>data-main-menu-index="<?php echo $_smarty_tpl->tpl_vars['mainMenuIndex']->value;?>
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

                        <div class="viewport">
                            <?php echo $_smarty_tpl->tpl_vars['elements']->value['beforeTableContent'];?>

                        </div>

                        <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['paginator'])) || (isset($_smarty_tpl->tpl_vars['elements']->value['filter']))) {?>
                        <div class="beforetable-line<?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['filter'])) && !(isset($_smarty_tpl->tpl_vars['elements']->value['paginator']))) {?> flex-d-row<?php }?>">
                            <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['paginator']))) {?>
                                <?php echo $_smarty_tpl->tpl_vars['elements']->value['paginator']->getView(array('short'=>true));?>

                            <?php }?>                        

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
                                <div class="common-column viewport">
                                    <?php echo $_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']->getView();?>

                                </div>
                            </div>
                        <?php }?>
                    </div>
                </div> <!-- .columns -->

<?php if (!$_smarty_tpl->tpl_vars['url']->value->isAjax()) {?>
            </div> <!-- .updatable -->
</div>
<?php }
}
}
