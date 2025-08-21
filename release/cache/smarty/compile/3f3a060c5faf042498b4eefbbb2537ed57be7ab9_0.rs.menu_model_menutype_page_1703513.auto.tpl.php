<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:27
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\form\menu_model_menutype_page_1703513.auto.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318ab092547_05447476',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3f3a060c5faf042498b4eefbbb2537ed57be7ab9' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\form\\menu_model_menutype_page_1703513.auto.tpl',
      1 => 1755075722,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318ab092547_05447476 (Smarty_Internal_Template $_smarty_tpl) {
?>                                            
    
                                    
            <tr>
                <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__link_template']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__link_template']->getHint() != '') {?><a class="help-icon" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__link_template']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                </td>
                <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__link_template']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__link_template']), 0, true);
?></td>
            </tr>
                        <?php }
}
