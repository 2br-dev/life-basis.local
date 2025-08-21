<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:36
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\table\coltype\strhead.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0c36ebe4_55003783',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f041a3078522007f1353249add64c7cfca2623d8' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\table\\coltype\\strhead.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0c36ebe4_55003783 (Smarty_Internal_Template $_smarty_tpl) {
if (!empty($_smarty_tpl->tpl_vars['cell']->value->property['Sortable'])) {?>
    <a href="<?php echo $_smarty_tpl->tpl_vars['cell']->value->sorturl;?>
" class="call-update sortable <?php echo mb_strtolower((string) $_smarty_tpl->tpl_vars['cell']->value->property['CurrentSort'], 'UTF-8');?>
"><?php echo $_smarty_tpl->tpl_vars['cell']->value->getTitle();?>
</a>
<?php } else { ?>
    <?php echo $_smarty_tpl->tpl_vars['cell']->value->getTitle();?>

<?php }
}
}
