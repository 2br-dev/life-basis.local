<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:24
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\block_error.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3087c5f66_38207278',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'abb97afdd4b89bb26803a7cd5d71cd7111efc052' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\block_error.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e3087c5f66_38207278 (Smarty_Internal_Template $_smarty_tpl) {
if (!$_smarty_tpl->tpl_vars['view_options']->value || $_smarty_tpl->tpl_vars['view_options']->value['error']) {?>
<div class="field-error top-corner" data-field="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
">
    <?php if ($_smarty_tpl->tpl_vars['field']->value->hasErrors()) {?>
        <span class="text"><i class="cor"></i>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['field']->value->getErrors(), 'error');
$_smarty_tpl->tpl_vars['error']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['error']->value) {
$_smarty_tpl->tpl_vars['error']->do_else = false;
?>
            <?php echo $_smarty_tpl->tpl_vars['error']->value;?>
<br>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </span>
    <?php }?>
</div>
<?php }
}
}
