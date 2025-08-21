<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:26
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\form\menu\other.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318aaa0dc79_59695542',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ea4dc838a33380ad3ff8dd036f89bdaee9970c35' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\form\\menu\\other.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%menu%/form/menu/type_form.tpl' => 1,
  ),
),false)) {
function content_68a318aaa0dc79_59695542 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__typelink']->getOriginalTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__typelink']), 0, true);
echo '<script'; ?>
>
    $(function() { 
        /**
        * Обновляет тип формы
        */
        var updateTypeForm = function() {
            var type = $('select[name="typelink"]').val();
            $.ajaxQuery({
                url: '<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl("getMenuTypeForm");?>
',
                data: { type: type },
                success: function(response) {
                    try {
                        $('#menu-type-form .tinymce').tinymce().remove();
                    } catch(e) {}
                    $('#menu-type-form').html(response.html);
                }
            });
        }
        
        /**
        * Смена типа
        */
        $('select[name="typelink"]').change(function() {
            updateTypeForm();
        });
    });
<?php echo '</script'; ?>
>
</td></tr>
<tbody id="menu-type-form">
    <?php $_prefixVariable5 = $_smarty_tpl->tpl_vars['elem']->value->getTypeObject(false);
$_smarty_tpl->_assignInScope('type_object', $_prefixVariable5);
if ($_prefixVariable5) {?>
        <?php $_smarty_tpl->_subTemplateRender("rs:%menu%/form/menu/type_form.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    <?php }?>
</tbody>
<tr><td><?php }
}
