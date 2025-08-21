<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:48:41
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\fileinput.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a32f39ba6be0_78358252',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '29ca65b04cf4620186dc8f669f2b1e6b3814173f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\fileinput.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a32f39ba6be0_78358252 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"fileinput/fileinput.min.js",'basepath'=>"common"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['field']->value) {?>
        <div class="fileinput fileinput-<?php if ($_smarty_tpl->tpl_vars['field']->value->get() != '') {?>exists<?php } else { ?>new<?php }?>" data-provides="fileinput">
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
        <span class="fileinput-filename">
            <?php if ($_smarty_tpl->tpl_vars['field']->value->get() != '') {?><a href="<?php echo $_smarty_tpl->tpl_vars['field']->value->getLink();?>
" target="_blank"><?php echo $_smarty_tpl->tpl_vars['field']->value->getFileName();?>
</a><?php }?>
        </span>

        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
    </div>
<?php } else { ?>
        <div class="fileinput fileinput-new" data-provides="fileinput">
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
            <input type="file" name="<?php echo $_smarty_tpl->tpl_vars['form_name']->value;?>
">
        </span>
        <span class="fileinput-filename"></span>
        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
    </div>
<?php }
}
}
