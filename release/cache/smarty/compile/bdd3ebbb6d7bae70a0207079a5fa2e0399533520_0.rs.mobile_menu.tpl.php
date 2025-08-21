<?php
/* Smarty version 4.3.1, created on 2025-08-19 17:51:57
  from 'D:\Projects\Hosts\life-basis.local\release\templates\life-basis\moduleview\menu\mobile_menu.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a48f8d98ef17_55343471',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'bdd3ebbb6d7bae70a0207079a5fa2e0399533520' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\life-basis\\moduleview\\menu\\mobile_menu.tpl',
      1 => 1755615113,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a48f8d98ef17_55343471 (Smarty_Internal_Template $_smarty_tpl) {
?><ul class="sidenav" id="mobile-menu">
<?php if ($_smarty_tpl->tpl_vars['items']->value->count()) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'node');
$_smarty_tpl->tpl_vars['node']->index = -1;
$_smarty_tpl->tpl_vars['node']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['node']->value) {
$_smarty_tpl->tpl_vars['node']->do_else = false;
$_smarty_tpl->tpl_vars['node']->index++;
$_smarty_tpl->tpl_vars['node']->first = !$_smarty_tpl->tpl_vars['node']->index;
$__foreach_node_0_saved = $_smarty_tpl->tpl_vars['node'];
?>         <?php $_smarty_tpl->_assignInScope('menu', $_smarty_tpl->tpl_vars['node']->value->getObject());?>
        <li <?php if ($_smarty_tpl->tpl_vars['node']->first) {?>class="offcanvas__list-separator"<?php }?>>
            <a class="offcanvas__list-item" href="<?php echo (($tmp = $_smarty_tpl->tpl_vars['menu']->value->getHref() ?? null)===null||$tmp==='' ? "#" ?? null : $tmp);?>
" <?php if ($_smarty_tpl->tpl_vars['menu']->value['target_blank']) {?>target="_blank"<?php }?> <?php echo $_smarty_tpl->tpl_vars['menu']->value->getDebugAttributes();?>
><?php echo $_smarty_tpl->tpl_vars['menu']->value['title'];?>
</a>
                    </li>
    <?php
$_smarty_tpl->tpl_vars['node'] = $__foreach_node_0_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}?>
</ul><?php }
}
