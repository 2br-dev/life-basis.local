<?php
/* Smarty version 4.3.1, created on 2025-08-19 12:15:17
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\form\menu_model_menutype_article_2829380943.auto.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a440a58ffe37_93525109',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f39de4d1513105394384844cd719c8cc1b6e15df' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\form\\menu_model_menutype_article_2829380943.auto.tpl',
      1 => 1755075713,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a440a58ffe37_93525109 (Smarty_Internal_Template $_smarty_tpl) {
?>                                            
    
                                    
            <tr>
                <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__content']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__content']->getHint() != '') {?><a class="help-icon" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__content']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                </td>
                <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__content']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__content']), 0, true);
?></td>
            </tr>
                        <?php }
}
