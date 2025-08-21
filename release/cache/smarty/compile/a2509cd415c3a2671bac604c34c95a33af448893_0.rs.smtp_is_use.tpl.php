<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:48:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\main\view\form\sysoptions\smtp_is_use.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a32f3a3647b3_96291425',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a2509cd415c3a2671bac604c34c95a33af448893' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\main\\view\\form\\sysoptions\\smtp_is_use.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a32f3a3647b3_96291425 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_is_use']->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_is_use']), 0, true);
echo '<script'; ?>
>
    $(function() {       
        $('[name="smtp_is_use"]').change(function() {
            var enable = $(this).is(':checked');
            $('[name="smtp_host"], [name="smtp_port"], [name="smtp_secure"], [name="smtp_auth"], [name="smtp_username"], [name="smtp_password"]').each(function() {
                $(this).closest('tr').toggle(enable);
            });
        }).change();
        
        $('[name="smtp_auth"]').change(function() {
            var enable = $(this).is(':checked');
            $('[name="smtp_username"], [name="smtp_password"]').prop('disabled', !enable);
        }).change();
    });
<?php echo '</script'; ?>
><?php }
}
