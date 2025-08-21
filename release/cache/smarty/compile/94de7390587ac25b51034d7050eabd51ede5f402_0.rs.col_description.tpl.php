<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:23:00
  from 'D:\Projects\Hosts\life-basis.local\release\modules\modcontrol\view\admin\col_description.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a329340c5e40_85371503',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '94de7390587ac25b51034d7050eabd51ede5f402' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\modcontrol\\view\\admin\\col_description.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a329340c5e40_85371503 (Smarty_Internal_Template $_smarty_tpl) {
echo $_smarty_tpl->tpl_vars['cell']->value->getValue();?>


<?php $_smarty_tpl->_assignInScope('row', $_smarty_tpl->tpl_vars['cell']->value->getRow());
if ($_smarty_tpl->tpl_vars['row']->value['license_text']) {?>
    <div class="f-11 m-t-5 text-<?php echo $_smarty_tpl->tpl_vars['row']->value['license_text_level'];?>
">
        <i class="zmdi zmdi-info-outline"></i>
        <?php echo $_smarty_tpl->tpl_vars['row']->value['license_text'];?>

    </div>
<?php }
}
}
