<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:35
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\table.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0b9d7c82_62074980',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '66e1e492fba993548fe51a33f5d19b3a7881979a' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\table.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0b9d7c82_62074980 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<div class="table-mobile-wrapper">
    <table border="0" <?php echo $_smarty_tpl->tpl_vars['table']->value->getTableAttr();?>
>
        <thead>
        <tr>
            <th class="l-w-space"></th>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['table']->value->getColumns(), 'item', false, 'col');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['col']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <?php if (!$_smarty_tpl->tpl_vars['item']->value->property['hidden']) {?>
                <th <?php echo $_smarty_tpl->tpl_vars['item']->value->getThAttr();?>
><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['item']->value->getHeadTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['item']->value), 0, true);
?></th>
                <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <th class="r-w-space"></th>
        </tr>
        </thead>
        <tbody>
                            <?php if ((isset($_smarty_tpl->tpl_vars['anyrows']->value[0])) && empty($_smarty_tpl->tpl_vars['rows']->value)) {?>
                <tr <?php echo $_smarty_tpl->tpl_vars['table']->value->getAnyRowAttr($_smarty_tpl->tpl_vars['rownum']->value);?>
>
                    <td class="l-w-space"></td>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['anyrows']->value[0], 'anycell');
$_smarty_tpl->tpl_vars['anycell']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['anycell']->value) {
$_smarty_tpl->tpl_vars['anycell']->do_else = false;
?>
                    <td <?php echo $_smarty_tpl->tpl_vars['anycell']->value->getTdAttr();?>
>
                        <?php if ((isset($_smarty_tpl->tpl_vars['anycell']->value->property['href']))) {?><a href="<?php echo $_smarty_tpl->tpl_vars['anycell']->value->getHref();?>
" <?php echo $_smarty_tpl->tpl_vars['anycell']->value->getLinkAttr();?>
><?php }?>
                            <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['anycell']->value->getBodyTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['anycell']->value), 0, true);
?>
                        <?php if ((isset($_smarty_tpl->tpl_vars['anycell']->value->property['href']))) {?></a><?php }?>
                    </td>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <td class="r-w-space"></td>
                </tr>
                <?php }?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['rows']->value, 'row', false, 'rownum');
$_smarty_tpl->tpl_vars['row']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['rownum']->value => $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->do_else = false;
?>
            <?php if ((isset($_smarty_tpl->tpl_vars['anyrows']->value[$_smarty_tpl->tpl_vars['rownum']->value]))) {?>
            <tr <?php echo $_smarty_tpl->tpl_vars['table']->value->getAnyRowAttr($_smarty_tpl->tpl_vars['rownum']->value);?>
>
                <td class="l-w-space"></td>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['anyrows']->value[$_smarty_tpl->tpl_vars['rownum']->value], 'anycell');
$_smarty_tpl->tpl_vars['anycell']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['anycell']->value) {
$_smarty_tpl->tpl_vars['anycell']->do_else = false;
?>
                <td <?php echo $_smarty_tpl->tpl_vars['anycell']->value->getTdAttr();?>
>
                    <?php if ((isset($_smarty_tpl->tpl_vars['anycell']->value->property['href']))) {?><a href="<?php echo $_smarty_tpl->tpl_vars['anycell']->value->getHref();?>
" <?php echo $_smarty_tpl->tpl_vars['anycell']->value->getLinkAttr();?>
><?php }?>
                        <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['anycell']->value->getBodyTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['anycell']->value), 0, true);
?>
                    <?php if ((isset($_smarty_tpl->tpl_vars['anycell']->value->property['href']))) {?></a><?php }?>
                </td>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <td class="r-w-space"></td>
            </tr>
            <?php }?>
            <tr <?php echo $_smarty_tpl->tpl_vars['table']->value->getRowAttr($_smarty_tpl->tpl_vars['rownum']->value);?>
>
                <td class="l-w-space"></td>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['row']->value, 'cell', false, 'col');
$_smarty_tpl->tpl_vars['cell']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['col']->value => $_smarty_tpl->tpl_vars['cell']->value) {
$_smarty_tpl->tpl_vars['cell']->do_else = false;
?>
                <td <?php echo $_smarty_tpl->tpl_vars['cell']->value->getTdAttr();?>
>
                    <?php if ((isset($_smarty_tpl->tpl_vars['cell']->value->property['href']))) {?><a href="<?php echo $_smarty_tpl->tpl_vars['cell']->value->getHref();?>
" <?php echo $_smarty_tpl->tpl_vars['cell']->value->getLinkAttr();?>
><?php }?>
                        <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['cell']->value->getBodyTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('cell'=>$_smarty_tpl->tpl_vars['cell']->value), 0, true);
?>
                    <?php if ((isset($_smarty_tpl->tpl_vars['cell']->value->property['href']))) {?></a><?php }?>
                </td>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <td class="r-w-space"></td>
            </tr>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            <?php if (empty($_smarty_tpl->tpl_vars['anyrows']->value) && empty($_smarty_tpl->tpl_vars['rows']->value)) {?>
            <tr>
                <?php $_smarty_tpl->_assignInScope('count', count($_smarty_tpl->tpl_vars['table']->value->getColumns()));?>
                <td class="l-w-space"></td>
                <td colspan="<?php echo $_smarty_tpl->tpl_vars['count']->value;?>
" align="center"> <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет элементов<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> </td>
                <td class="r-w-space"></td>
            </tr>
            <?php }?>
        </tbody>
    </table>
</div><?php }
}
