<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:35
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\filter\type\select.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b0b297ef8_81769918',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a59937d096ae956e14b5d8a32696a6d12e71e952' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\filter\\type\\select.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b0b297ef8_81769918 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\vendor\\smarty\\smarty\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
?>
<div class="form-inline">
    <select name="<?php echo $_smarty_tpl->tpl_vars['fitem']->value->getName();?>
" <?php echo $_smarty_tpl->tpl_vars['fitem']->value->getAttrString();?>
>
    <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['fitem']->value->getList(),'selected'=>$_smarty_tpl->tpl_vars['fitem']->value->getValue()),$_smarty_tpl);?>

    </select>
</div><?php }
}
