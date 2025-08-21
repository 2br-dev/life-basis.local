<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:52
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\form\menu_model_menutype_link_815748819.auto.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318c4355694_73768328',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4545dfe3d2d33137e84f8d7645a0036ffbaeb7e1' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\form\\menu_model_menutype_link_815748819.auto.tpl',
      1 => 1755519172,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318c4355694_73768328 (Smarty_Internal_Template $_smarty_tpl) {
?>                                            
                                            
    
                                    
            <tr>
                <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__link']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__link']->getHint() != '') {?><a class="help-icon" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__link']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                </td>
                <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__link']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__link']), 0, true);
?></td>
            </tr>
                                            
            <tr>
                <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__target_blank']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__target_blank']->getHint() != '') {?><a class="help-icon" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__target_blank']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                </td>
                <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__target_blank']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__target_blank']), 0, true);
?></td>
            </tr>
                        <?php }
}
