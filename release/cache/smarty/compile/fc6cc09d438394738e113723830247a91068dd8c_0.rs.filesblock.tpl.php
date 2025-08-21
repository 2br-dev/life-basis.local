<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:47
  from 'D:\Projects\Hosts\life-basis.local\release\modules\files\view\adminblocks\files\filesblock.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b773995e8_75896345',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fc6cc09d438394738e113723830247a91068dd8c' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\files\\view\\adminblocks\\files\\filesblock.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%files%/adminblocks/files/one_file.tpl' => 1,
  ),
),false)) {
function content_68a58b773995e8_75896345 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.format_filesize.php','function'=>'smarty_modifier_format_filesize',),4=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"fileupload/jquery.iframe-transport.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"fileupload/jquery.fileupload.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.tablednd/jquery.tablednd.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%files%/files.js"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"%files%/files.css "),$_smarty_tpl);?>

<div class="files-block" data-urls='{ "fileUpload": "<?php echo smarty_function_adminUrl(array('files_do'=>"Upload",'mod_controller'=>"files-block-files",'link_type'=>$_smarty_tpl->tpl_vars['link_type']->value,'link_id'=>$_smarty_tpl->tpl_vars['link_id']->value),$_smarty_tpl);?>
",
                                      "fileDelete": "<?php echo smarty_function_adminUrl(array('files_do'=>"Delete",'mod_controller'=>"files-block-files",'link_type'=>$_smarty_tpl->tpl_vars['link_type']->value,'link_id'=>$_smarty_tpl->tpl_vars['link_id']->value),$_smarty_tpl);?>
",
                                      "fileEdit": "<?php echo smarty_function_adminUrl(array('files_do'=>"Edit",'mod_controller'=>"files-block-files",'link_type'=>$_smarty_tpl->tpl_vars['link_type']->value,'link_id'=>$_smarty_tpl->tpl_vars['link_id']->value),$_smarty_tpl);?>
",
                                      "fileChangeAccess": "<?php echo smarty_function_adminUrl(array('files_do'=>"changeAccess",'mod_controller'=>"files-block-files",'link_type'=>$_smarty_tpl->tpl_vars['link_type']->value,'link_id'=>$_smarty_tpl->tpl_vars['link_id']->value),$_smarty_tpl);?>
" }'>
    <table class="upload-block">
        <tr>
            <td class="dragzone-block">
                <div class="dragzone">
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('size'=>smarty_modifier_format_filesize($_smarty_tpl->tpl_vars['max_upload_size']->value)));
$_block_repeat=true;
echo smarty_block_t(array('size'=>smarty_modifier_format_filesize($_smarty_tpl->tpl_vars['max_upload_size']->value)), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Чтобы начать загрузку, перетащите сюда файлы. Максимальный размер файлов для загрузки -<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> %size<?php $_block_repeat=false;
echo smarty_block_t(array('size'=>smarty_modifier_format_filesize($_smarty_tpl->tpl_vars['max_upload_size']->value)), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </div>
            </td>
            <td>
                <span class="add"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>добавить файлы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                    <input type="file" class="fileinput" multiple="" name="files[]">
                </span>
            </td>
        </tr>
    </table>
    <div class="files-container<?php if (!$_smarty_tpl->tpl_vars['files']->value) {?> hidden<?php }?>">
        <br>
        <table data-sort-request="<?php echo smarty_function_adminUrl(array('files_do'=>"Move",'mod_controller'=>"files-block-files",'link_type'=>$_smarty_tpl->tpl_vars['link_type']->value,'link_id'=>$_smarty_tpl->tpl_vars['link_id']->value),$_smarty_tpl);?>
" class="rs-table editable-table files-list virtual-form">
            <thead>
                <tr>
                    <th style="width:26px" class="chk">
                        <div class="chkhead-block">
                            <input type="checkbox" class="chk_head select-page" data-name="files[]" alt="">                        
                        </div>
                    </th>
                    <th width="20" class="drag"><span class="sortable sortdot asc"><span></span></span></th>                        
                    <th class="title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Имя файла<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                    <th class="description"><span class="hidden-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Описание<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></th>
                    <th class="size"><span class="hidden-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Размер<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></th>
                    <th class="access"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Доступ<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody> 
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['files']->value, 'file');
$_smarty_tpl->tpl_vars['file']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['file']->value) {
$_smarty_tpl->tpl_vars['file']->do_else = false;
?>
                    <?php $_smarty_tpl->_subTemplateRender("rs:%files%/adminblocks/files/one_file.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('linked_file'=>$_smarty_tpl->tpl_vars['file']->value), 0, true);
?>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </tbody>
        </table>
        <div class="group-toolbar">
            <span class="checked-offers"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('alias'=>"отмеченные файлы"));
$_block_repeat=true;
echo smarty_block_t(array('alias'=>"отмеченные файлы"), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отмеченные<br> файлы<?php $_block_repeat=false;
echo smarty_block_t(array('alias'=>"отмеченные файлы"), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span> <a class="btn btn-danger delete"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
        </div>
    </div>
</div><?php }
}
