<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:51
  from 'D:\Projects\Hosts\life-basis.local\release\modules\tags\view\admin\form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b1b0255f5_14500480',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1c982ba680f1383fd7af4c60e41a9b6d8cdc8e40' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\tags\\view\\admin\\form.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b1b0255f5_14500480 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addcss(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_css']->value)."mtagsblock.css",'basepath'=>"root"),$_smarty_tpl);?>


<?php echo '<script'; ?>
>
$LAB
.script("<?php echo $_smarty_tpl->tpl_vars['mod_js']->value;?>
blocktags.jquery.js")
.wait(function() {
    $(function() {
        $('.tags').blocktags({
            getWordsUrl: '<?php echo smarty_function_adminUrl(array('mod_controller'=>"tags-blocktags",'tdo'=>"getWords",'do'=>false),$_smarty_tpl);?>
',
            delWordUrl: '<?php echo smarty_function_adminUrl(array('mod_controller'=>"tags-blocktags",'tdo'=>"del",'do'=>false),$_smarty_tpl);?>
',
            getHelpListUrl: '<?php echo smarty_function_adminUrl(array('mod_controller'=>"tags-blocktags",'tdo'=>"getHelpList",'do'=>false),$_smarty_tpl);?>
'
        });
    });
});
<?php echo '</script'; ?>
>

<div class="tags" data-type="<?php echo $_smarty_tpl->tpl_vars['param']->value['type'];?>
" data-linkid="<?php echo $_smarty_tpl->tpl_vars['param']->value['linkid'];?>
">
    <?php if ($_smarty_tpl->tpl_vars['param']->value['linkid'] == 0) {?>
        <div class="notags">
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавлени тегов возможно только в режиме редактирования<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
        </div>    
    <?php } else { ?>
        <div class="grayblock">
                <div data-action="<?php echo smarty_function_adminUrl(array('mod_controller'=>"tags-blocktags",'do'=>false,'tdo'=>"addWords"),$_smarty_tpl);?>
" class="tag_form">
                    <input type="hidden" name="link_id" value="<?php echo $_smarty_tpl->tpl_vars['param']->value['linkid'];?>
">
                    <input type="hidden" name="type" value="<?php echo $_smarty_tpl->tpl_vars['param']->value['type'];?>
">
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Ключевые слова<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                    <span class="help-icon" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Введите ключевые слова через запятую. <br>Например: книги,классическая литература,чтение <br>Минимальная длина ключевого слова должна составлять 2 знака<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">?</span>
                    <input type="text" name="keywords" style="width:270px;" class="autocomplete">
                    <input type="button" value="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" class="btn btn-default add-btn m-5">
                </div>
        </div>
        <div class="word_container">
            <?php echo $_smarty_tpl->tpl_vars['word_list_html']->value;?>

        </div>        
    <?php }?>
</div><?php }
}
