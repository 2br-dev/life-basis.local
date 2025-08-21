<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:22:47
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\tinymce\textarea.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b17e80308_83540780',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ed22d469e3d000b4f2086c237ea3dd5169719e0f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\tinymce\\textarea.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b17e80308_83540780 (Smarty_Internal_Template $_smarty_tpl) {
?><textarea class="tinymce" id="tinymce-<?php echo $_smarty_tpl->tpl_vars['random']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['param']->value['name'];?>
" <?php if ((isset($_smarty_tpl->tpl_vars['param']->value['rows']))) {?>rows="<?php echo $_smarty_tpl->tpl_vars['param']->value['rows'];?>
"<?php }?> <?php if ((isset($_smarty_tpl->tpl_vars['param']->value['cols']))) {?>cols="<?php echo $_smarty_tpl->tpl_vars['param']->value['cols'];?>
"<?php }?> <?php echo $_smarty_tpl->tpl_vars['tinymce']->value->getAttributesAsString();?>
 <?php if ((isset($_smarty_tpl->tpl_vars['param']->value['style']))) {?>style="<?php echo $_smarty_tpl->tpl_vars['param']->value['style'];?>
"<?php }?>><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
<?php echo '<script'; ?>
>
    $LAB
        .script(<?php echo json_encode($_smarty_tpl->tpl_vars['param']->value['jquery_tinymce_path']);?>
)
        .wait(function(){
            var txtEditor = $('#tinymce-<?php echo $_smarty_tpl->tpl_vars['random']->value;?>
');
            var alreadyTiny = txtEditor.tinymce();
            if (alreadyTiny) {
                alreadyTiny.remove(); //Удаляем предыдущий экземпляр, чтобы переинициализировать заново
            }

            var initEditor = function() {
                var params = <?php echo (($tmp = json_encode($_smarty_tpl->tpl_vars['param']->value['tiny_options']) ?? null)===null||$tmp==='' ? '{}' ?? null : $tmp);?>
;
                $('#tinymce-<?php echo $_smarty_tpl->tpl_vars['random']->value;?>
:visible')
                    .tinymce($.extend(params, {
                        setup: function (editor) {
                            editor.on('init', function (args) {
                                $(editor.getElement()).parent().removeClass('tiny-loading');
                                $(editor.getElement()).trigger('tinymce-loaded', [{ editor: editor }]);
                            });
                        }
                    }))
                    .parent().addClass('tiny-loading');
            };

            $(function() {
                txtEditor.closest('.tab-pane').bind('on-tab-open', function() {
                    initEditor();
                });
                txtEditor.bind('became-visible', function() {
                    initEditor();
                });
                txtEditor.closest('form').on('beforeAjaxSubmit', function() {
                        $('#tinymce-<?php echo $_smarty_tpl->tpl_vars['random']->value;?>
:tinymce').each(function() {
                            $(this).tinymce().save();
                        });
                });
                txtEditor.closest('.dialog-window').on('dialogBeforeDestroy', function() {
                    var tiny_instance = txtEditor.tinymce();
                    if (tiny_instance) {
                        tiny_instance.remove();
                    }
                });

                setTimeout(function() {
                    initEditor();
                }, 10);
            });
        });
<?php echo '</script'; ?>
><?php }
}
