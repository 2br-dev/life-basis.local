<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:48:42
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\coreobject\type\form\image.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a32f3a03a841_04407557',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6614f15e1376a2fc0b9f38d83884331a6265bc98' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\coreobject\\type\\form\\image.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a32f3a03a841_04407557 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"fileinput/fileinput.min.js",'basepath'=>"common"),$_smarty_tpl);?>


<div class="fileinput fileinput-<?php if ($_smarty_tpl->tpl_vars['field']->value->get() != '') {?>exists<?php } else { ?>new<?php }?>" data-provides="fileinput">
    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width:<?php echo $_smarty_tpl->tpl_vars['field']->value->preview_width;?>
px; height:<?php echo $_smarty_tpl->tpl_vars['field']->value->preview_height;?>
px; line-height:<?php echo $_smarty_tpl->tpl_vars['field']->value->preview_height;?>
px">
        <?php if ($_smarty_tpl->tpl_vars['field']->value->get() != '') {?>
            <?php if ($_smarty_tpl->tpl_vars['field']->value->getExtension() == 'svg') {?>
                <img src="<?php echo $_smarty_tpl->tpl_vars['field']->value->getLink();?>
" alt="" style="max-width: <?php echo $_smarty_tpl->tpl_vars['field']->value->preview_width;?>
px; max-height:<?php echo $_smarty_tpl->tpl_vars['field']->value->preview_height;?>
px">
            <?php } else { ?>
                <img src="<?php echo $_smarty_tpl->tpl_vars['field']->value->getUrl($_smarty_tpl->tpl_vars['field']->value->preview_width,$_smarty_tpl->tpl_vars['field']->value->preview_height,$_smarty_tpl->tpl_vars['field']->value->preview_resize_type);?>
" alt="">
            <?php }?>
        <?php }?>
    </div>
    <div>
        <span class="btn btn-default btn-file">
            <span class="fileinput-new"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выберите файл<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
            <span class="fileinput-exists"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Изменить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
            <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['field']->value->getFormName();?>
">
            <input type="hidden" value="0" name="del_<?php echo $_smarty_tpl->tpl_vars['field']->value->getName();?>
" class="remove">
        </span>

        <a href="#" class="btn btn-danger fileinput-exists" data-dismiss="fileinput"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
    </div>
</div><?php }
}
