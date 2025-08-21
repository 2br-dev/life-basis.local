<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:58
  from 'D:\Projects\Hosts\life-basis.local\release\modules\statistic\view\blocks\plot_with_list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3420a0be4a3_92856970',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c3c84031611d75c3488ac814a7c701318ec2b93f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\statistic\\view\\blocks\\plot_with_list.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a3420a0be4a3_92856970 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"flot/excanvas.js",'basepath'=>"common",'before'=>"<!--[if lte IE 8]>",'after'=>"<![endif]-->"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.tooltip.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.resize.js",'basepath'=>"common",'waitbefore'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"flot/jquery.flot.pie.js",'basepath'=>"common",'waitbefore'=>true),$_smarty_tpl);?>

<?php if (!$_smarty_tpl->tpl_vars['param']->value['widget']) {?>
    <?php echo smarty_function_addjs(array('file'=>"jquery.rs.tableimage.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php }?>

<?php echo smarty_function_addjs(array('file'=>"%statistic%/diagram.js"),$_smarty_tpl);?>

<div class="updatable stat-report" data-url="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" data-update-block-id="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
" id="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
" data-update-replace="true">
    <?php if (!$_smarty_tpl->tpl_vars['param']->value['widget']) {?>
        <div class="viewport">
            <h2 class="stat-h2"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</h2>

            <?php echo $_smarty_tpl->tpl_vars['period_selector']->value->render();?>

        </div>
    <?php } else { ?>
        <?php echo $_smarty_tpl->tpl_vars['period_selector']->value->render();?>

    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['total']->value) {?>
        <div class="plot">
            <div class="graph"></div>
            <div class="flc-plot m-20"></div>
        </div>

        <?php echo '<script'; ?>
>
            $.allReady(function() {
                statisticShowPlot('#<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
', <?php echo $_smarty_tpl->tpl_vars['json_data']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['json_unit']->value;?>
); // Круговая диаграмма
            });
        <?php echo '</script'; ?>
>
    <?php }?>

    <?php if (!$_smarty_tpl->tpl_vars['param']->value['widget']) {?>
        <div class="beforetable-line">

            <form method="GET" action="<?php echo smarty_function_adminUrl(array('do'=>false,'mod_controller'=>$_smarty_tpl->tpl_vars['this_controller']->value->getUrlName()),$_smarty_tpl);?>
" class="paginator form-call-update no-update-hash">
                <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
_filter_date_from" value="<?php echo $_smarty_tpl->tpl_vars['period_selector']->value->date_from;?>
">
                <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
_filter_date_to" value="<?php echo $_smarty_tpl->tpl_vars['period_selector']->value->date_to;?>
">
                <?php echo $_smarty_tpl->tpl_vars['paginator']->value->getView(array('short'=>true));?>

            </form>
            <?php if ((isset($_smarty_tpl->tpl_vars['filters']->value))) {?>
                <?php echo $_smarty_tpl->tpl_vars['filters']->value->getView();?>

            <?php }?>
        </div>

        <div class="viewport">
            <?php if ((isset($_smarty_tpl->tpl_vars['filters']->value))) {?>
                <?php echo $_smarty_tpl->tpl_vars['filters']->value->getPartsHtml();?>

            <?php }?>
        </div>

        <?php if ($_smarty_tpl->tpl_vars['total']->value) {?>
            <div class="table-mobile-wrapper">
                <?php echo $_smarty_tpl->tpl_vars['data_provider']->value->getTable($_smarty_tpl->tpl_vars['paginator']->value)->getView();?>

            </div>
        <?php } else { ?>
            <div class="stat-nodata"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет ни одной записи за выбранный период<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
        <?php }?>

        <div class="viewport">
            <form method="GET" action="<?php echo smarty_function_adminUrl(array('do'=>false,'mod_controller'=>$_smarty_tpl->tpl_vars['this_controller']->value->getUrlName()),$_smarty_tpl);?>
" class="paginator form-call-update no-update-hash">
                <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
_filter_date_from" value="<?php echo $_smarty_tpl->tpl_vars['period_selector']->value->date_from;?>
">
                <input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['block_id']->value;?>
_filter_date_to" value="<?php echo $_smarty_tpl->tpl_vars['period_selector']->value->date_to;?>
">
                <?php echo $_smarty_tpl->tpl_vars['paginator']->value->getView();?>

            </form>
        </div>
    <?php }?>

</div>
<?php }
}
