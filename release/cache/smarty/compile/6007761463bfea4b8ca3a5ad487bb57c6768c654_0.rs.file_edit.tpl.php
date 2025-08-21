<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:18:36
  from 'D:\Projects\Hosts\life-basis.local\release\modules\templates\view\form\file_edit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31a1c04fab4_28160843',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6007761463bfea4b8ca3a5ad487bb57c6768c654' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\templates\\view\\form\\file_edit.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31a1c04fab4_28160843 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"ace-master/ace/ace.js",'basepath'=>"common",'no_compress'=>true),$_smarty_tpl);?>

<div class="formbox">        
    <form method="POST" action="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" enctype="multipart/form-data" class="crud-form" id="template-edit-form" data-dialog-options='{ "dialogClass": "template-edit-win" }'>
        <input type="hidden" name="basepath" value="<?php echo $_smarty_tpl->tpl_vars['epath']->value['type'];?>
:<?php echo $_smarty_tpl->tpl_vars['epath']->value['type_value'];?>
/">
        <input type="hidden" name="ext" value="<?php echo $_smarty_tpl->tpl_vars['ext']->value;?>
">
        <div class="notabs">
            <table class="otable no-td-width" width="100%">
            <tr>
                <td class="otitle" style="width:150px"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Имя файла<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                <td><div class="file-container-text"><?php if ($_smarty_tpl->tpl_vars['epath']->value['type'] == 'theme') {?>
                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Тема<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:<?php echo $_smarty_tpl->tpl_vars['root_sections']->value['themes'][$_smarty_tpl->tpl_vars['epath']->value['type_value']]['title'];?>

                    <?php } else { ?>
                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Модуль<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:<?php echo $_smarty_tpl->tpl_vars['root_sections']->value['modules'][$_smarty_tpl->tpl_vars['epath']->value['type_value']]['title'];?>

                    <?php }?></div>
                    <input style="width:500px" type="text" name="filename" value="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['filename'], ENT_QUOTES, 'UTF-8', true);?>
"><span class="field-error" data-field="filename"></span><br>
                    </td>
            </tr>
            <tr>
                <td class="otitle"></td>
                <td><input type="checkbox" id="overwrite" name="overwrite" value="1" <?php if ($_smarty_tpl->tpl_vars['data']->value['overwrite']) {?>checked<?php }?>> 
                <label for="overwrite" class="fieldhelp"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Перезаписывать файл, если таковой уже существует<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></label></td>
            </tr>
            <tr>
                <td class="otitle"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Содержание файла<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                <td>
                    <div style="position:relative">
                        <?php $_smarty_tpl->_assignInScope('editor_modes', array('css'=>'css','tpl'=>'html','js'=>'javascript'));?>
                        <textarea data-editor-mode="<?php echo $_smarty_tpl->tpl_vars['editor_modes']->value[$_smarty_tpl->tpl_vars['ext']->value];?>
" id="code_source_editor" name="content" style="width:100%; height:300px"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['content'], ENT_QUOTES, 'UTF-8', true);?>
</textarea>
                        <div id="code_editor" style="display:none"></div>
                        <span class="field-error" data-field="overwrite"></span>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="checkbox" id="switchSyntaxHL"> <label for="switchSyntaxHL" class="fieldhelp"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Включить подсветку синтаксиса<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></label></td>
            </tr>
                                                                            </table>
        </div>
    </form>
</div>

<style type="text/css" media="screen">
    #code_editor { 
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
</style>

<?php echo '<script'; ?>
>
$.allReady(function() {
    var editor;
    var $editor_textarea = $('#code_source_editor');    
    
    var setSyntaxHL = function() {
        var $editor_div = $('#code_editor');
        if ($('#switchSyntaxHL').is(':checked')) {
            if ($editor_textarea.is(':visible')) {
                $editor_textarea = $('#code_source_editor').css('visibility', 'hidden');
                if (!editor) {
                    editor = ace.edit($editor_div.get(0));
                    editor.getSession().setUseWorker(false);
                    var mode = $editor_textarea.data('editorMode');
                    console.log('mode', mode);
                    editor.getSession().setMode("ace/mode/" + mode);
                }
                editor.getSession().setValue( $editor_textarea.val() );                
                editor.resize();
                $editor_div.show();
                $.cookie('tmanager-use-editor', 1);
            }
        } else {
            if ($editor_div.is(':visible')) {
                $editor_div.hide();
                $editor_textarea.val( editor.getSession().getValue() );
                $editor_textarea.css('visibility', 'visible');
                $.cookie('tmanager-use-editor', null);
            }
        }
    }
    
    $('#switchSyntaxHL').change(setSyntaxHL);
    
    if ($.cookie('tmanager-use-editor') == 1) {
        $('#switchSyntaxHL').get(0).checked = true;
        setTimeout(function() {
            $('#switchSyntaxHL').trigger('change');
        }, 250);
    }
    
    $('#template-edit-form').bind('beforeAjaxSubmit', function() {
        if ($('#switchSyntaxHL').is(':checked')) {
            $editor_textarea.val( editor.getSession().getValue() );
        }
    });
})
<?php echo '</script'; ?>
><?php }
}
