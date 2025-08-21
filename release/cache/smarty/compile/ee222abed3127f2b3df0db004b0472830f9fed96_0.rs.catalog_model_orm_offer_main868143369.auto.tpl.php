<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:43
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\catalog_model_orm_offer_main868143369.auto.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b7370d287_41097454',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ee222abed3127f2b3df0db004b0472830f9fed96' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\catalog_model_orm_offer_main868143369.auto.tpl',
      1 => 1755005986,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b7370d287_41097454 (Smarty_Internal_Template $_smarty_tpl) {
?>                    
        <p class="label"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__weight']->getTitle();?>
</p>
        <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__weight']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__weight']), 0, true);
?><br>
        
                            
        <p class="label"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__xml_id']->getTitle();?>
</p>
        <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__xml_id']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__xml_id']), 0, true);
?><br>
        
                                
        <p class="label"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__market_sku']->getTitle();?>
</p>
        <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__market_sku']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__market_sku']), 0, true);
?><br>
        
            <?php }
}
