<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:27
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\ajaxscriptload_after.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318ab68fdb1_09910587',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '02da3dceee52f62cfdc623672e0cdc0b438e7614' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\ajaxscriptload_after.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318ab68fdb1_09910587 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getCss('header')+$_smarty_tpl->tpl_vars['app']->value->getCss('footer'), 'css');
$_smarty_tpl->tpl_vars['css']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
$_smarty_tpl->tpl_vars['css']->do_else = false;
echo $_smarty_tpl->tpl_vars['css']->value['params']['before'];?>
<link type="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['type'] ?? null)===null||$tmp==='' ? "text/css" ?? null : $tmp);?>
" href="<?php echo $_smarty_tpl->tpl_vars['css']->value['file'];?>
" media="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['media'] ?? null)===null||$tmp==='' ? "all" ?? null : $tmp);?>
" rel="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['rel'] ?? null)===null||$tmp==='' ? "stylesheet" ?? null : $tmp);?>
"><?php echo $_smarty_tpl->tpl_vars['css']->value['params']['after'];?>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_smarty_tpl->_assignInScope('jslist', $_smarty_tpl->tpl_vars['app']->value->getJs('header')+$_smarty_tpl->tpl_vars['app']->value->getJs('footer'));
if (count($_smarty_tpl->tpl_vars['jslist']->value)) {?>
    <?php echo '<script'; ?>
>$LAB.loading = true; var _lab = $LAB;<?php echo '</script'; ?>
>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['jslist']->value, 'js');
$_smarty_tpl->tpl_vars['js']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['js']->value) {
$_smarty_tpl->tpl_vars['js']->do_else = false;
?>
    <?php echo $_smarty_tpl->tpl_vars['js']->value['params']['before'];
echo '<script'; ?>
>_lab = _lab.<?php if ($_smarty_tpl->tpl_vars['js']->value['params']['waitbefore']) {?>wait().<?php }?>script('<?php echo $_smarty_tpl->tpl_vars['js']->value['file'];?>
');<?php echo '</script'; ?>
><?php echo $_smarty_tpl->tpl_vars['js']->value['params']['after'];?>

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php echo '<script'; ?>
>
        _lab.wait(function() {
            $LAB.loading = false;
            $(window).trigger('LAB-loading-complete');
        });
    <?php echo '</script'; ?>
>
<?php } else { ?>
    <?php echo '<script'; ?>
>
        $LAB.loading = false;
        $(window).trigger('LAB-loading-complete');
    <?php echo '</script'; ?>
>
<?php }
}
}
