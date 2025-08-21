<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:25
  from 'D:\Projects\Hosts\life-basis.local\release\modules\users\view\form\user\ban_expire.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3091c84e0_40052426',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '37344c96b45a56ed6b280cd7020a0a093816cabb' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\users\\view\\form\\user\\ban_expire.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e3091c84e0_40052426 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<input type="checkbox" name="setban" value="1" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>заблокировать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" id="setban" <?php if ($_smarty_tpl->tpl_vars['elem']->value['ban_expire']) {?>checked<?php }?>>
<span class="ban-reason">
    <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__ban_expire']->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__ban_expire']), 0, true);
?><br>
    <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Причина блокировки<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
    <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__ban_reason']->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__ban_reason']), 0, true);
?>
</span>

<?php echo '<script'; ?>
>
    $(function() {        
        $('#setban').change(function() {
            var context = $(this).closest('td');
            if ($(this).is(':checked')) {
                $('.ban-reason', context).show();
            } else {
                $('[name="ban_expire"]', context).val('');
                $('[name="ban_reason"]', context).val('');
                $('.ban-reason', context).hide();
            }
        }).change();
    });
<?php echo '</script'; ?>
><?php }
}
