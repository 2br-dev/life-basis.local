<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:42
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\html.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31806d7e947_94655290',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8706b1357edbb85a0de869bd292e4284e387de7b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\html.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31806d7e947_94655290 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE <?php echo $_smarty_tpl->tpl_vars['app']->value->getDoctype();?>
>
<html <?php echo $_smarty_tpl->tpl_vars['app']->value->getHtmlAttrLine();?>
 <?php if ($_smarty_tpl->tpl_vars['SITE']->value['language']) {?>lang="<?php echo $_smarty_tpl->tpl_vars['SITE']->value['language'];?>
"<?php }?>>
<head <?php echo $_smarty_tpl->tpl_vars['app']->value->getHeadAttributes(true);?>
>
<title><?php echo $_smarty_tpl->tpl_vars['app']->value->title->get();?>
</title>
<?php echo $_smarty_tpl->tpl_vars['app']->value->meta->get();?>


<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getCss(), 'css');
$_smarty_tpl->tpl_vars['css']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
$_smarty_tpl->tpl_vars['css']->do_else = false;
echo $_smarty_tpl->tpl_vars['css']->value['params']['before'];?>
<link <?php if (!empty($_smarty_tpl->tpl_vars['css']->value['params']['type'])) {?>type="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['type'] ?? null)===null||$tmp==='' ? "text/css" ?? null : $tmp);?>
"<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['css']->value['file'];?>
" <?php if (!empty($_smarty_tpl->tpl_vars['css']->value['params']['hreflang'])) {?>hreflang="<?php echo $_smarty_tpl->tpl_vars['css']->value['params']['hreflang'];?>
"<?php }?> <?php if (!empty($_smarty_tpl->tpl_vars['css']->value['params']['media'])) {?>media="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['media'] ?? null)===null||$tmp==='' ? "all" ?? null : $tmp);?>
"<?php }?> rel="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['rel'] ?? null)===null||$tmp==='' ? "stylesheet" ?? null : $tmp);?>
"<?php if (!empty($_smarty_tpl->tpl_vars['css']->value['params']['as'])) {?> as="<?php echo $_smarty_tpl->tpl_vars['css']->value['params']['as'];?>
"<?php }
if (!empty($_smarty_tpl->tpl_vars['css']->value['params']['crossorigin'])) {?> crossorigin="<?php echo $_smarty_tpl->tpl_vars['css']->value['params']['crossorigin'];?>
"<?php }?>><?php echo $_smarty_tpl->tpl_vars['css']->value['params']['after'];?>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php echo '<script'; ?>
>
    window.global = <?php echo $_smarty_tpl->tpl_vars['app']->value->getJsonJsVars();?>
;
<?php echo '</script'; ?>
>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getJs(), 'js');
$_smarty_tpl->tpl_vars['js']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['js']->value) {
$_smarty_tpl->tpl_vars['js']->do_else = false;
echo $_smarty_tpl->tpl_vars['js']->value['params']['before'];
echo '<script'; ?>
 <?php if ($_smarty_tpl->tpl_vars['js']->value['params']['type']) {?>type="<?php echo $_smarty_tpl->tpl_vars['js']->value['params']['type'];?>
"<?php }?> src="<?php echo $_smarty_tpl->tpl_vars['js']->value['file'];?>
"<?php if ($_smarty_tpl->tpl_vars['js']->value['params']['async']) {?> async<?php }
if ($_smarty_tpl->tpl_vars['js']->value['params']['defer']) {?> defer<?php }?>><?php echo '</script'; ?>
><?php echo $_smarty_tpl->tpl_vars['js']->value['params']['after'];?>

<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php if (!empty($_smarty_tpl->tpl_vars['app']->value->getJsCode('header'))) {
echo '<script'; ?>
><?php echo $_smarty_tpl->tpl_vars['app']->value->getJsCode('header');
echo '</script'; ?>
>
<?php }
echo $_smarty_tpl->tpl_vars['app']->value->microdata->getHeadMicrodataHtml();?>

<?php echo $_smarty_tpl->tpl_vars['app']->value->getAnyHeadData();?>

</head>
<body <?php if ($_smarty_tpl->tpl_vars['app']->value->getBodyClass() != '') {?>class="<?php echo $_smarty_tpl->tpl_vars['app']->value->getBodyClass();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['app']->value->getBodyAttrLine();?>
>
    <?php echo $_smarty_tpl->tpl_vars['body']->value;?>

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getCss('footer'), 'css');
$_smarty_tpl->tpl_vars['css']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['css']->value) {
$_smarty_tpl->tpl_vars['css']->do_else = false;
?>
    <?php echo $_smarty_tpl->tpl_vars['css']->value['params']['before'];?>
<link <?php if ($_smarty_tpl->tpl_vars['css']->value['params']['type'] !== false) {?>type="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['type'] ?? null)===null||$tmp==='' ? "text/css" ?? null : $tmp);?>
"<?php }?> href="<?php echo $_smarty_tpl->tpl_vars['css']->value['file'];?>
" <?php if ($_smarty_tpl->tpl_vars['css']->value['params']['media'] !== false) {?>media="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['media'] ?? null)===null||$tmp==='' ? "all" ?? null : $tmp);?>
"<?php }?> rel="<?php echo (($tmp = $_smarty_tpl->tpl_vars['css']->value['params']['rel'] ?? null)===null||$tmp==='' ? "stylesheet" ?? null : $tmp);?>
"><?php echo $_smarty_tpl->tpl_vars['css']->value['params']['after'];?>

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['app']->value->getJs('footer'), 'js');
$_smarty_tpl->tpl_vars['js']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['js']->value) {
$_smarty_tpl->tpl_vars['js']->do_else = false;
?>

    <?php echo $_smarty_tpl->tpl_vars['js']->value['params']['before'];
echo '<script'; ?>
 <?php if ($_smarty_tpl->tpl_vars['js']->value['params']['type']) {?>type="<?php echo $_smarty_tpl->tpl_vars['js']->value['params']['type'];?>
"<?php }?> src="<?php echo $_smarty_tpl->tpl_vars['js']->value['file'];?>
"<?php if ($_smarty_tpl->tpl_vars['js']->value['params']['async']) {?> async<?php } else { ?> defer<?php }?>><?php echo '</script'; ?>
><?php echo $_smarty_tpl->tpl_vars['js']->value['params']['after'];?>

    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php if (!empty($_smarty_tpl->tpl_vars['app']->value->getJsCode('footer'))) {?>
        <?php echo '<script'; ?>
><?php echo $_smarty_tpl->tpl_vars['app']->value->getJsCode('footer');
echo '</script'; ?>
>
    <?php }?>
</body>
</html><?php }
}
