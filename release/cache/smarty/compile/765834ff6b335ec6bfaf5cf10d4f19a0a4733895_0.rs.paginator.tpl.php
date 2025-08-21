<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:56
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\widget\paginator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34208b014d3_26738190',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '765834ff6b335ec6bfaf5cf10d4f19a0a4733895' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\widget\\paginator.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a34208b014d3_26738190 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['paginator']->value->total_pages > 1) {?>
    <div class="widget-paginator <?php echo $_smarty_tpl->tpl_vars['paginatorClass']->value;?>
">
        <div class="putright">
            <?php ob_start();
echo (($tmp = $_smarty_tpl->tpl_vars['paginator_len']->value ?? null)===null||$tmp==='' ? 5 ?? null : $tmp);
$_prefixVariable6 = ob_get_clean();
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['paginator']->value->setPaginatorLen($_prefixVariable6)->getPages(), 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
            <?php if ($_smarty_tpl->tpl_vars['item']->value['class'] == 'page') {?>
                <a data-update-url="<?php echo $_smarty_tpl->tpl_vars['item']->value['href'];?>
" class="call-update<?php if ($_smarty_tpl->tpl_vars['noUpdateHash']->value) {?> no-update-hash<?php }
if ($_smarty_tpl->tpl_vars['item']->value['act']) {?> act<?php }?>"><span><?php echo $_smarty_tpl->tpl_vars['item']->value['n'];?>
</span></a>
            <?php } else { ?>
                <?php if ($_smarty_tpl->tpl_vars['item']->value['class'] == 'right') {?>
                    <a data-update-url="<?php echo $_smarty_tpl->tpl_vars['item']->value['href'];?>
" class="call-update<?php if ($_smarty_tpl->tpl_vars['noUpdateHash']->value) {?> no-update-hash<?php }
if ($_smarty_tpl->tpl_vars['item']->value['act']) {?> act<?php }?>"><span><?php echo $_smarty_tpl->tpl_vars['item']->value['n'];?>
&raquo;</span></a>
                <?php } else { ?>
                    <a data-update-url="<?php echo $_smarty_tpl->tpl_vars['item']->value['href'];?>
" class="call-update<?php if ($_smarty_tpl->tpl_vars['noUpdateHash']->value) {?> no-update-hash<?php }
if ($_smarty_tpl->tpl_vars['item']->value['act']) {?> act<?php }?>"><span>&laquo;<?php echo $_smarty_tpl->tpl_vars['item']->value['n'];?>
</span></a>
                <?php }?>
            <?php }?>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
<?php }
}
}
