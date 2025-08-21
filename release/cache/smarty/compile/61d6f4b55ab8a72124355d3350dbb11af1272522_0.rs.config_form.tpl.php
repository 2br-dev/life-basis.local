<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:43:30
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\config_form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31ff2140470_35620546',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '61d6f4b55ab8a72124355d3350dbb11af1272522' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\config_form.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31ff2140470_35620546 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="formbox" <?php echo $_smarty_tpl->tpl_vars['elem']->value->getClassParameter('formbox_attr_line');?>
>
    {if $elem._before_form_template}{include file=$elem._before_form_template}{/if}

    <?php $_smarty_tpl->_assignInScope('groups', $_smarty_tpl->tpl_vars['prop']->value->getGroups(false,$_smarty_tpl->tpl_vars['switch']->value));?>
    <?php if (count($_smarty_tpl->tpl_vars['groups']->value) > 1) {?>
        <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'item', false, 'i');
$_smarty_tpl->tpl_vars['item']->index = -1;
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['item']->index++;
$_smarty_tpl->tpl_vars['item']->first = !$_smarty_tpl->tpl_vars['item']->index;
$__foreach_item_0_saved = $_smarty_tpl->tpl_vars['item'];
?>
                <li class="<?php if ($_smarty_tpl->tpl_vars['item']->first) {?> active<?php }?>"><a data-target="#<?php echo $_smarty_tpl->tpl_vars['elem']->value->getShortAlias();?>
-tab<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" data-toggle="tab" role="tab">{$elem->getPropertyIterator()->getGroupName(<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
)}</a></li>
            <?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_0_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
        <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'data', false, 'i');
$_smarty_tpl->tpl_vars['data']->index = -1;
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
$_smarty_tpl->tpl_vars['data']->index++;
$_smarty_tpl->tpl_vars['data']->first = !$_smarty_tpl->tpl_vars['data']->index;
$__foreach_data_1_saved = $_smarty_tpl->tpl_vars['data'];
?>
                <div class="tab-pane<?php if ($_smarty_tpl->tpl_vars['data']->first) {?> active<?php }?>" id="<?php echo $_smarty_tpl->tpl_vars['elem']->value->getShortAlias();?>
-tab<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
" role="tabpanel">
                    <?php if (count($_smarty_tpl->tpl_vars['data']->value['items'])) {?>
                        <?php $_smarty_tpl->_assignInScope('issetUserTemplate', false);?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['items'], 'item', false, 'name');
$_smarty_tpl->tpl_vars['item']->index = -1;
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['item']->index++;
$_smarty_tpl->tpl_vars['item']->first = !$_smarty_tpl->tpl_vars['item']->index;
$__foreach_item_2_saved = $_smarty_tpl->tpl_vars['item'];
?>
                            <?php if (is_a($_smarty_tpl->tpl_vars['item']->value,'RS\Orm\Type\UserTemplate')) {?>
                            {include file=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getRenderTemplate() field=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}
                                <?php $_smarty_tpl->_assignInScope('issetUserTemplate', true);?>
                            <?php }?>
                        <?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_2_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        <?php if (!$_smarty_tpl->tpl_vars['issetUserTemplate']->value) {?>
                            <table class="otable">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['items'], 'item', false, 'name');
$_smarty_tpl->tpl_vars['item']->index = -1;
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['item']->index++;
$_smarty_tpl->tpl_vars['item']->first = !$_smarty_tpl->tpl_vars['item']->index;
$__foreach_item_3_saved = $_smarty_tpl->tpl_vars['item'];
?>
                                    
                                        <tr <?php if (is_array($_smarty_tpl->tpl_vars['item']->value->trAttr)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value->trAttr, 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?> <?php echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}?>>
                                        <td class="otitle">{$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getTitle()}&nbsp;&nbsp;{if $elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getHint() != ''}<a class="help-icon" data-placement="right" title="{$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getHint()|escape}">?</a>{/if}
                                        </td>
                                        <td>{include file=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getRenderTemplate() field=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}</td>
                                        </tr>
                                    
                                <?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_3_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </table>
                        <?php }?>
                    <?php }?>
                </div>
            <?php
$_smarty_tpl->tpl_vars['data'] = $__foreach_data_1_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </form>
    </div>
    <?php } else { ?>
                <form method="POST" action="{urlmake}" enctype="multipart/form-data" class="crud-form">
            <input type="submit" value="" style="display:none">
            <div class="notabs">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'data', false, 'i');
$_smarty_tpl->tpl_vars['data']->index = -1;
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
$_smarty_tpl->tpl_vars['data']->index++;
$_smarty_tpl->tpl_vars['data']->first = !$_smarty_tpl->tpl_vars['data']->index;
$__foreach_data_5_saved = $_smarty_tpl->tpl_vars['data'];
?>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['items'], 'item', false, 'name');
$_smarty_tpl->tpl_vars['item']->index = -1;
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['item']->index++;
$_smarty_tpl->tpl_vars['item']->first = !$_smarty_tpl->tpl_vars['item']->index;
$__foreach_item_6_saved = $_smarty_tpl->tpl_vars['item'];
?>
                        <?php if (is_a($_smarty_tpl->tpl_vars['item']->value,'RS\Orm\Type\UserTemplate')) {?>
                        {include file=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getRenderTemplate() field=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}
                            <?php $_smarty_tpl->_assignInScope('issetUserTemplate', true);?>
                        <?php }?>
                    <?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_6_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <?php
$_smarty_tpl->tpl_vars['data'] = $__foreach_data_5_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                <?php if (!$_smarty_tpl->tpl_vars['issetUserTemplate']->value) {?>
                    <table class="otable">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'data', false, 'i');
$_smarty_tpl->tpl_vars['data']->index = -1;
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
$_smarty_tpl->tpl_vars['data']->index++;
$_smarty_tpl->tpl_vars['data']->first = !$_smarty_tpl->tpl_vars['data']->index;
$__foreach_data_7_saved = $_smarty_tpl->tpl_vars['data'];
?>
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['items'], 'item', false, 'name');
$_smarty_tpl->tpl_vars['item']->index = -1;
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
$_smarty_tpl->tpl_vars['item']->index++;
$_smarty_tpl->tpl_vars['item']->first = !$_smarty_tpl->tpl_vars['item']->index;
$__foreach_item_8_saved = $_smarty_tpl->tpl_vars['item'];
?>
                                
                                    <tr <?php if (is_array($_smarty_tpl->tpl_vars['item']->value->trAttr)) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value->trAttr, 'value', false, 'key');
$_smarty_tpl->tpl_vars['value']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->do_else = false;
?> <?php echo $_smarty_tpl->tpl_vars['key']->value;?>
="<?php echo $_smarty_tpl->tpl_vars['value']->value;?>
"<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}?>>
                                    <td class="otitle">{$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getTitle()}&nbsp;&nbsp;{if $elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getHint() != ''}<a class="help-icon" title="{$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getHint()|escape}">?</a>{/if}
                                    </td>
                                    <td>{include file=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
->getRenderTemplate() field=$elem.__<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
}</td>
                                    </tr>
                            <?php
$_smarty_tpl->tpl_vars['item'] = $__foreach_item_8_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        <?php
$_smarty_tpl->tpl_vars['data'] = $__foreach_data_7_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </table>
                <?php }?>
            </div>
        </form>
    <?php }?>
</div><?php }
}
