<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:26
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\user\personal_price.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e30ae16ad2_05818390',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f3b313159d8bba743958cbbbb07e0e5cb923eda7' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\user\\personal_price.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e30ae16ad2_05818390 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('sites_prepare', $_smarty_tpl->tpl_vars['field']->value->cost_api->getUserSelectList());
$_smarty_tpl->_assignInScope('sites', $_smarty_tpl->tpl_vars['field']->value->cost_api->fillUsersPriceList($_smarty_tpl->tpl_vars['elem']->value,$_smarty_tpl->tpl_vars['sites_prepare']->value));?>

<table class="rs-space-table">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sites']->value, 'site');
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
?>
          <tr>
             <td width="20%">
                <?php if (count($_smarty_tpl->tpl_vars['sites']->value) > 1) {
echo $_smarty_tpl->tpl_vars['site']->value['title'];
}?>
             </td>
             <td>
                <?php if (!empty($_smarty_tpl->tpl_vars['site']->value['prices'])) {?>
                  <select name="user_cost[<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
]">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['site']->value['prices'], 'price');
$_smarty_tpl->tpl_vars['price']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['price']->value) {
$_smarty_tpl->tpl_vars['price']->do_else = false;
?>
                        <option  value="<?php echo $_smarty_tpl->tpl_vars['price']->value['id'];?>
" <?php if ($_smarty_tpl->tpl_vars['price']->value['selected']) {?>selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['price']->value['title'];?>
</option>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                  </select>
                <?php }?>
             </td>
          </tr>
           
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</table>
<?php }
}
