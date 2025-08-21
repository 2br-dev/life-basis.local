<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:20
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\toolbar\button\dropup.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a46c4a17_95549094',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2e2448d9aa584fc2c76c528d99d6b88333b85a6f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\toolbar\\button\\dropup.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318a46c4a17_95549094 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('first', $_smarty_tpl->tpl_vars['button']->value->getFirstItem());?>

<?php if (count($_smarty_tpl->tpl_vars['button']->value->getAllItems()) > 1) {?>
    <div <?php echo $_smarty_tpl->tpl_vars['button']->value->getAttrLine();?>
>
        <?php if ((isset($_smarty_tpl->tpl_vars['first']->value['attr']['href']))) {?>
            <a class="split-link <?php echo $_smarty_tpl->tpl_vars['button']->value->getItemClass($_smarty_tpl->tpl_vars['first']->value);?>
" <?php echo $_smarty_tpl->tpl_vars['button']->value->getItemAttrLine($_smarty_tpl->tpl_vars['first']->value);?>
><?php echo $_smarty_tpl->tpl_vars['first']->value['title'];?>
</a>
            <a class="split-caret l-border <?php echo $_smarty_tpl->tpl_vars['button']->value->getItemClass($_smarty_tpl->tpl_vars['first']->value,'toggle');?>
" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></a>
        <?php } else { ?>
            <a class="split-group <?php echo $_smarty_tpl->tpl_vars['button']->value->getItemClass($_smarty_tpl->tpl_vars['first']->value,'toggle');?>
" <?php echo $_smarty_tpl->tpl_vars['button']->value->getItemAttrLine($_smarty_tpl->tpl_vars['first']->value);?>
 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $_smarty_tpl->tpl_vars['first']->value['title'];?>
 <span class="caret"></span></a>
        <?php }?>
        <?php if (count($_smarty_tpl->tpl_vars['button']->value->getDropItems())) {?>
            <ul class="dropdown-menu dropdown-menu-right">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['button']->value->getDropItems(), 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <li>
                    <a class="<?php echo $_smarty_tpl->tpl_vars['button']->value->getItemClass($_smarty_tpl->tpl_vars['item']->value,'listitem');?>
" <?php echo $_smarty_tpl->tpl_vars['button']->value->getItemAttrLine($_smarty_tpl->tpl_vars['item']->value);?>
><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</a>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </ul>
        <?php }?>
    </div>
<?php } else { ?>
    <a class="<?php echo $_smarty_tpl->tpl_vars['button']->value->getItemClass($_smarty_tpl->tpl_vars['first']->value);?>
" <?php echo $_smarty_tpl->tpl_vars['button']->value->getItemAttrLine($_smarty_tpl->tpl_vars['first']->value);?>
><?php echo $_smarty_tpl->tpl_vars['first']->value['title'];?>
</a>
<?php }
}
}
