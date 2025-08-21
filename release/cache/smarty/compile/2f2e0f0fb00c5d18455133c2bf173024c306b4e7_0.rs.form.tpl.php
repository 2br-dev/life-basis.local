<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:49
  from 'D:\Projects\Hosts\life-basis.local\release\modules\photo\view\admin\form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b19d65cc0_91031145',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2f2e0f0fb00c5d18455133c2bf173024c306b4e7' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\photo\\view\\admin\\form.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b19d65cc0_91031145 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),4=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),));
echo smarty_function_addcss(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_css']->value)."photoblock.css",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/lightgallery/css/lightgallery.min.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"lightgallery/lightgallery-all.min.js",'basepath'=>"common"),$_smarty_tpl);?>


<?php echo smarty_function_addjs(array('file'=>"fileupload/jquery.iframe-transport.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"fileupload/jquery.fileupload.js",'basepath'=>"common"),$_smarty_tpl);?>


<?php echo smarty_function_addjs(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_js']->value)."photo.js",'basepath'=>"root"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['param']->value['linkid'] == 0) {?>
    <div class="cant_adding">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавление фото возможно только в режиме редактирования.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php } else { ?>
    <div class="photo_block" method="POST" enctype="multipart/form-data" action="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'pdo'=>"addphoto",'linkid'=>$_smarty_tpl->tpl_vars['param']->value['linkid'],'type'=>$_smarty_tpl->tpl_vars['param']->value['type']),$_smarty_tpl);?>
">
        <input type="hidden" name="redirect" value="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
">
        <table class="upload-block <?php if (empty($_smarty_tpl->tpl_vars['photo_list_html']->value)) {?>no-photos<?php }?>">
            <tr>
                <td class="dragzone-block"><div class="dragzone"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Чтобы начать загрузку, перетащите сюда фотографии<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div></td>
                <td><button class="delete-list" type="button" formaction="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'pdo'=>"delphoto"),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></button></td>
                <td><span class="check-all"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>выбрать все<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></td>
                <td><span class="add"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>добавить фото<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?><input type="file" name="files[]" multiple class="fileinput"></span></td>
            </tr>
        </table>
        <ul class="photo-list overable" data-sort-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'pdo'=>"movephoto",'do'=>false),$_smarty_tpl);?>
" data-edit-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'do'=>false,'pdo'=>"editphoto"),$_smarty_tpl);?>
">
            <?php echo $_smarty_tpl->tpl_vars['photo_list_html']->value;?>


        </ul>
        <div class="clear"></div>
    </div>
<?php }
}
}
