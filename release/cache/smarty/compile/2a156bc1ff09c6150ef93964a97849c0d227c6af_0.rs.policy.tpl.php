<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:48:42
  from 'D:\Projects\Hosts\life-basis.local\release\modules\site\view\form\site\policy.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a32f3a979508_16219181',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2a156bc1ff09c6150ef93964a97849c0d227c6af' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\site\\view\\form\\site\\policy.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a32f3a979508_16219181 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<p><a id="load-<?php echo $_smarty_tpl->tpl_vars['field']->value->getName();?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Загрузить типовой документ<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a></p>
<?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['field']->value->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['field']->value), 0, true);
?>

<?php echo '<script'; ?>
>
    $('#load-<?php echo $_smarty_tpl->tpl_vars['field']->value->getName();?>
').click(function() {
        if (confirm(lang.t('Вы действительно желаете загрузить типовой документ (текущий текст в редакторе будет заменен)?'))) {
            $.ajaxQuery({
                url: '<?php echo $_smarty_tpl->tpl_vars['field']->value->loadDefaultUrl;?>
',
                success: function(response) {
                    if (response.success) {
                        $('[name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getName();?>
"]').val(response.html);
                    }
                }
            });
        }
    });
<?php echo '</script'; ?>
><?php }
}
