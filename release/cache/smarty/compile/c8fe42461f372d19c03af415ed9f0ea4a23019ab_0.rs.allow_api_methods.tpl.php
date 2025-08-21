<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:27
  from 'D:\Projects\Hosts\life-basis.local\release\modules\externalapi\view\form\user\allow_api_methods.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e30b1a8820_61639269',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c8fe42461f372d19c03af415ed9f0ea4a23019ab' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\externalapi\\view\\form\\user\\allow_api_methods.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e30b1a8820_61639269 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('allow_mehods', $_smarty_tpl->tpl_vars['elem']->value->getExternalApiAllowMethods($_smarty_tpl->tpl_vars['site']->value['id']));
$_smarty_tpl->_assignInScope('sites', $_smarty_tpl->tpl_vars['elem']->value['__allow_api_methods']->sites);?>
<div>
    <?php if (count($_smarty_tpl->tpl_vars['sites']->value) > 1) {?>
        <ul class="tab-nav" role="tablist">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sites']->value, 'site');
$_smarty_tpl->tpl_vars['site']->index = -1;
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
$_smarty_tpl->tpl_vars['site']->index++;
$_smarty_tpl->tpl_vars['site']->first = !$_smarty_tpl->tpl_vars['site']->index;
$__foreach_site_10_saved = $_smarty_tpl->tpl_vars['site'];
?>
                <li <?php if ($_smarty_tpl->tpl_vars['site']->first) {?>class="active"<?php }?>>
                    <a class="" data-target="#tab_allowapimethods_site_<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
" role="tab" data-toggle="tab"><?php echo $_smarty_tpl->tpl_vars['site']->value['title'];?>
</a>
                </li>
            <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_10_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
    <?php }?>
    <div class="tab-content">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sites']->value, 'site');
$_smarty_tpl->tpl_vars['site']->index = -1;
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
$_smarty_tpl->tpl_vars['site']->index++;
$_smarty_tpl->tpl_vars['site']->first = !$_smarty_tpl->tpl_vars['site']->index;
$__foreach_site_11_saved = $_smarty_tpl->tpl_vars['site'];
?>
            <div class="tab-pane <?php if ($_smarty_tpl->tpl_vars['site']->first) {?>active<?php }?>" id="tab_allowapimethods_site_<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
" role="tabpanel">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value['__allow_api_methods']->getList(), 'data', false, 'key');
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
                    <label><input type="checkbox" name="allow_api_methods[<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
][]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if ((isset($_smarty_tpl->tpl_vars['allow_mehods']->value[$_smarty_tpl->tpl_vars['site']->value['id']])) && in_array($_smarty_tpl->tpl_vars['key']->value,$_smarty_tpl->tpl_vars['allow_mehods']->value[$_smarty_tpl->tpl_vars['site']->value['id']])) {?>checked<?php }?>> <?php echo $_smarty_tpl->tpl_vars['data']->value;?>
</label><br>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </div>
        <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_11_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
</div><?php }
}
