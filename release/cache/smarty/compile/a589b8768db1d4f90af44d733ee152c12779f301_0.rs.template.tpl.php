<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:17:36
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\template.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a319e0a72477_98786776',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a589b8768db1d4f90af44d733ee152c12779f301' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\template.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/coreobject/type/form/block_error.tpl' => 1,
  ),
),false)) {
function content_68a319e0a72477_98786776 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
echo smarty_function_addjs(array('file'=>"%templates%/tplmanager.js",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%templates%/selecttemplate.js",'basepath'=>"root"),$_smarty_tpl);?>


<div class="input-group">
    <input name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
" value="<?php echo $_smarty_tpl->tpl_vars['field']->value->get();?>
" <?php if ($_smarty_tpl->tpl_vars['field']->value->getMaxLength() > 0) {?>maxlength="<?php echo $_smarty_tpl->tpl_vars['field']->value->getMaxLength();?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['field']->value->getAttr();?>
 /><!--
    --><span class="input-group-addon"><a class="zmdi zmdi-collection-text selectTemplate" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выбрать шаблон<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a></span>
</div>

<?php $_smarty_tpl->_subTemplateRender("rs:%system%/coreobject/type/form/block_error.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php echo '<script'; ?>
>
    $.allReady(function() {
        $('input[name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
"]').selectTemplate({
            dialogUrl: '<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttemplate",'do'=>false,'only_themes'=>$_smarty_tpl->tpl_vars['field']->value->getOnlyThemes()),$_smarty_tpl);?>
',
            handler: '.selectTemplate'
        })
    });
<?php echo '</script'; ?>
><?php }
}
