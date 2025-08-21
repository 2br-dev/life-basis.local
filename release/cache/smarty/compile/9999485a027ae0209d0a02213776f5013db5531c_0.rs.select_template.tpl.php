<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:43
  from 'D:\Projects\Hosts\life-basis.local\release\modules\templates\view\admin\select_template.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318bb9c7027_07178144',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9999485a027ae0209d0a02213776f5013db5531c' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\templates\\view\\admin\\select_template.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318bb9c7027_07178144 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
if ($_smarty_tpl->tpl_vars['url']->value->request('dialogMode',(defined('TYPE_INTEGER') ? constant('TYPE_INTEGER') : null))) {?>
    <div class="contentbox no-bottom-toolbar">
            <div class="titlebox"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выберите шаблон<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
            <div class="middlebox crud-ajax-group">
            <div class="updatable select-product-box" data-url="<?php echo smarty_function_adminUrl(array('only_themes'=>$_smarty_tpl->tpl_vars['only_themes']->value),$_smarty_tpl);?>
">
<?php }?>
                    <div class="tmanager">
                        <div class="margvert10">
                            <div class="category-filter dropdown">
                                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <?php if ($_smarty_tpl->tpl_vars['list']->value['epath']['type'] == 'theme') {?>
                                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Тема<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:<?php echo $_smarty_tpl->tpl_vars['root_sections']->value['themes'][$_smarty_tpl->tpl_vars['list']->value['epath']['type_value']]['title'];?>

                                    <?php } else { ?>
                                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Модуль<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:<?php echo $_smarty_tpl->tpl_vars['root_sections']->value['modules'][$_smarty_tpl->tpl_vars['list']->value['epath']['type_value']]['title'];?>

                                    <?php }?>
                                    <span class="caret"></span></button>

                                <ul class="dropdown-menu" aria-labelledby="dropdownMenu2" style="max-height:400px; overflow:auto;">
                                    <li class="dropdown-header"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Темы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></li>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['root_sections']->value['themes'], 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                        <li><a class="call-update no-update-hash" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttemplate",'path'=>"theme:".((string)$_smarty_tpl->tpl_vars['key']->value),'only_themes'=>$_smarty_tpl->tpl_vars['only_themes']->value),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</a></li>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                                    <?php if (!empty($_smarty_tpl->tpl_vars['root_sections']->value['modules'])) {?>
                                        <li class="dropdown-header"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Модули<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></li>
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['root_sections']->value['modules'], 'item', false, 'key');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                            <li><a class="call-update no-update-hash" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttemplate",'path'=>"module:".((string)$_smarty_tpl->tpl_vars['key']->value),'only_themes'=>$_smarty_tpl->tpl_vars['only_themes']->value),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</a></li>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    <?php }?>
                                </ul>
                            </div>

                            <div class="folderpath">
                                <a class="root call-update no-update-hash" title="корневая папка" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttemplate",'path'=>((string)$_smarty_tpl->tpl_vars['list']->value['epath']['type']).":".((string)$_smarty_tpl->tpl_vars['list']->value['epath']['type_value'])."/",'only_themes'=>$_smarty_tpl->tpl_vars['only_themes']->value),$_smarty_tpl);?>
"></a>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value['epath']['sections'], 'section', false, 'key');
$_smarty_tpl->tpl_vars['section']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['section']->value) {
$_smarty_tpl->tpl_vars['section']->do_else = false;
?>
                                    <a class="call-update no-update-hash" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttemplate",'path'=>((string)$_smarty_tpl->tpl_vars['key']->value),'only_themes'=>$_smarty_tpl->tpl_vars['only_themes']->value),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['section']->value;?>
</a> /
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <span class="filetypes">*.<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value['allow_extension'], 'one_ext', false, NULL, 'extlist', array (
  'first' => true,
  'index' => true,
));
$_smarty_tpl->tpl_vars['one_ext']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['one_ext']->value) {
$_smarty_tpl->tpl_vars['one_ext']->do_else = false;
$_smarty_tpl->tpl_vars['__smarty_foreach_extlist']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_extlist']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_extlist']->value['index'];
if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_extlist']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_extlist']->value['first'] : null)) {?>,<?php }
echo $_smarty_tpl->tpl_vars['one_ext']->value;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></span>

                                <a class="rt makedir" data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-filemanager",'do'=>"makedir",'path'=>$_smarty_tpl->tpl_vars['list']->value['epath']['public_dir'],'file'=>"noname.tpl"),$_smarty_tpl);?>
" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }'>
                                    <i class="zmdi zmdi-folder visible-xs f-18" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Создать папку<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></i>
                                    <span class="hidden-xs""><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>папку<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                                </a>
                                <span class="rt">|</span>
                                <a class="rt crud-add maketpl" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-filemanager",'do'=>"add",'path'=>$_smarty_tpl->tpl_vars['list']->value['epath']['public_dir'],'file'=>"noname.tpl"),$_smarty_tpl);?>
" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }'>
                                    <i class="zmdi zmdi-file visible-xs f-18" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Создать шаблон<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></i>
                                    <span class="hidden-xs"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>создать шаблон<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                                </a>
                            </div>
                        </div>

                        <?php if ((isset($_smarty_tpl->tpl_vars['list']->value['items']))) {?> <?php $_smarty_tpl->_assignInScope('listitems_count', true);?> <?php } else { ?> <?php $_smarty_tpl->_assignInScope('listitems_count', false);?> <?php }?>
                        <?php if ((isset($_smarty_tpl->tpl_vars['list']->value['epath']['sections']))) {
$_smarty_tpl->_assignInScope('listepathsections_count', true);?> <?php } else { ?> <?php $_smarty_tpl->_assignInScope('listepathsections_count', false);?> <?php }?>

                        <?php if ($_smarty_tpl->tpl_vars['listitems_count']->value || $_smarty_tpl->tpl_vars['listepathsections_count']->value) {?>
                        <div class="file-list-container" data-current-folder="<?php echo $_smarty_tpl->tpl_vars['list']->value['epath']['public_dir'];?>
">
                            <ul class="file-list">
                                <?php if ($_smarty_tpl->tpl_vars['listepathsections_count']->value) {?>
                                    <li class="dir"><a class="call-update no-update-hash" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttemplate",'path'=>$_smarty_tpl->tpl_vars['list']->value['epath']['parent'],'only_themes'=>$_smarty_tpl->tpl_vars['only_themes']->value),$_smarty_tpl);?>
">..</a></li>
                                <?php }?>                            
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value['items'], 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                    <?php if ($_smarty_tpl->tpl_vars['item']->value['type'] == 'dir') {?>
                                        <li class="item dir" data-path="<?php echo $_smarty_tpl->tpl_vars['item']->value['link'];?>
" data-name="<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
">
                                            <a class="call-update no-update-hash" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-selecttemplate",'path'=>$_smarty_tpl->tpl_vars['item']->value['link'],'only_themes'=>$_smarty_tpl->tpl_vars['only_themes']->value),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</a>
                                            <span class="tools">
                                                <a class="rename" data-old-value="<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
" data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-filemanager",'do'=>"rename",'path'=>$_smarty_tpl->tpl_vars['item']->value['link']),$_smarty_tpl);?>
" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>переименовать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-comment-edit"></i></a>
                                                <a class="delete" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-filemanager",'do'=>"delete",'path'=>$_smarty_tpl->tpl_vars['item']->value['link']),$_smarty_tpl);?>
" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-delete"></i></a>
                                            </span>                                                                                        
                                        </li>
                                    <?php } else { ?>
                                         <li class="item file <?php echo $_smarty_tpl->tpl_vars['item']->value['ext'];
if (((string)$_smarty_tpl->tpl_vars['item']->value['name']).".".((string)$_smarty_tpl->tpl_vars['item']->value['ext']) == $_smarty_tpl->tpl_vars['start_struct']->value['filename']) {?> current<?php }?>" data-path="<?php echo $_smarty_tpl->tpl_vars['item']->value['link'];?>
" data-name="<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
.<?php echo $_smarty_tpl->tpl_vars['item']->value['ext'];?>
">
                                             <div class="name">
                                                <a class="canselect"><?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
.<span class="ext"><?php echo $_smarty_tpl->tpl_vars['item']->value['ext'];?>
</span></a>
                                             </div>
                                            <span class="tools">
                                                <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-filemanager",'do'=>"edit",'path'=>$_smarty_tpl->tpl_vars['item']->value['path'],'file'=>$_smarty_tpl->tpl_vars['item']->value['filename']),$_smarty_tpl);?>
" class="tool edit crud-edit" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }'><i class="zmdi zmdi-edit"></i></a>
                                                <a class="rename" data-old-value="<?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
.<?php echo $_smarty_tpl->tpl_vars['item']->value['ext'];?>
" data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-filemanager",'do'=>"rename"),$_smarty_tpl);?>
" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>переименовать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-comment-edit"></i></a>
                                                <a class="delete" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"templates-filemanager",'do'=>"delete",'path'=>$_smarty_tpl->tpl_vars['item']->value['link']),$_smarty_tpl);?>
" data-crud-options='{ "updateElement":".select-product-box", "ajaxParam": { "noUpdateHash": true } }' title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-delete"></i></a>
                                            </span>                                            
                                        </li>   
                                    <?php }?>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </ul>
                        </div>                       
                            <?php } else { ?>
                                <div class="empty-folder">
                                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Пустой каталог<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                                </div>
                            <?php }?>                        
                    </div>

<?php if ($_smarty_tpl->tpl_vars['url']->value->request('dialogMode',(defined('TYPE_INTEGER') ? constant('TYPE_INTEGER') : null))) {?>
        </div>
    <p>
        <br>
        <input type="checkbox" id="use-relative" checked> <label for="use-relative"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Не привязывать к конкретной теме<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></label>
    </p>
    </div>
</div>
<?php }
}
}
