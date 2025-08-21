<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:25
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\crud_form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a91ed6d1_33167992',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'efd72481f4cd05e5db5366a945142f0f60e9da77' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\crud_form.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318a91ed6d1_33167992 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxBefore();?>

<div class="crud-ajax-group">

    <div class="viewport contentbox<?php if (!(isset($_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']))) {?> no-bottom-toolbar<?php }?>">

        <?php if ($_smarty_tpl->tpl_vars['elements']->value['topToolbar'] || $_smarty_tpl->tpl_vars['elements']->value['formTitle']) {?>
            <div class="headerbox">
                <?php if ($_smarty_tpl->tpl_vars['elements']->value['topToolbar']) {?>
                    <div class="buttons">
                        <?php echo $_smarty_tpl->tpl_vars['elements']->value['topToolbar']->getView();?>

                    </div>
                <?php }?>

                    <span class="titlebox gray-around"><?php echo $_smarty_tpl->tpl_vars['elements']->value['formTitle'];?>
</span>
            </div>
        <?php }?>

        <?php echo $_smarty_tpl->tpl_vars['elements']->value['headerHtml'];?>


        <div class="middlebox <?php echo $_smarty_tpl->tpl_vars['middleclass']->value;?>
">
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

            <?php echo $_smarty_tpl->tpl_vars['elements']->value['form'];?>

        </div>
    </div> <!-- .viewport -->

    <?php if ((isset($_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']))) {?>
        <div class="footerspace"></div>
        <div class="bottom-toolbar fixed">
            <div class="viewport">
                <div class="common-column">
                        <?php echo $_smarty_tpl->tpl_vars['elements']->value['bottomToolbar']->getView();?>

                </div>
            </div>
        </div>
    <?php }?>
</div>
<?php echo $_smarty_tpl->tpl_vars['app']->value->autoloadScripsAjaxAfter();
}
}
