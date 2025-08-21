<?php
/* Smarty version 4.3.1, created on 2025-08-18 16:48:39
  from 'D:\Projects\Hosts\life-basis.local\release\modules\site\view\form\site_model_orm_config_2780033412.auto.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a32f378d5b93_91885161',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '95883830a6af227e7cd84a0e52ed03d43c06e14b' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\site\\view\\form\\site_model_orm_config_2780033412.auto.tpl',
      1 => 1755005756,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a32f378d5b93_91885161 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.urlmake.php','function'=>'smarty_function_urlmake',),));
?>
<div class="formbox" >
                <div class="rs-tabs" role="tabpanel">
        <ul class="tab-nav" role="tablist">
                    <li class=" active"><a data-target="#site-config-tab0" data-toggle="tab" role="tab"><?php echo $_smarty_tpl->tpl_vars['elem']->value->getPropertyIterator()->getGroupName(0);?>
</a></li>
                    <li class=""><a data-target="#site-config-tab1" data-toggle="tab" role="tab"><?php echo $_smarty_tpl->tpl_vars['elem']->value->getPropertyIterator()->getGroupName(1);?>
</a></li>
                    <li class=""><a data-target="#site-config-tab2" data-toggle="tab" role="tab"><?php echo $_smarty_tpl->tpl_vars['elem']->value->getPropertyIterator()->getGroupName(2);?>
</a></li>
                    <li class=""><a data-target="#site-config-tab3" data-toggle="tab" role="tab"><?php echo $_smarty_tpl->tpl_vars['elem']->value->getPropertyIterator()->getGroupName(3);?>
</a></li>
                    <li class=""><a data-target="#site-config-tab4" data-toggle="tab" role="tab"><?php echo $_smarty_tpl->tpl_vars['elem']->value->getPropertyIterator()->getGroupName(4);?>
</a></li>
                    <li class=""><a data-target="#site-config-tab5" data-toggle="tab" role="tab"><?php echo $_smarty_tpl->tpl_vars['elem']->value->getPropertyIterator()->getGroupName(5);?>
</a></li>
                </ul>
        <form method="POST" action="<?php echo smarty_function_urlmake(array(),$_smarty_tpl);?>
" enctype="multipart/form-data" class="tab-content crud-form">
            <input type="submit" value="" style="display:none"/>
                        <div class="tab-pane active" id="site-config-tab0" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__admin_email']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__admin_email']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__admin_email']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__admin_email']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__admin_email']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__admin_phone']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__admin_phone']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__admin_phone']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__admin_phone']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__admin_phone']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__theme']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__theme']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__theme']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__theme']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__theme']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__favicon']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__favicon']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__favicon']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__favicon']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__favicon']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__favicon_svg']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__favicon_svg']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__favicon_svg']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__favicon_svg']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__favicon_svg']), 0, true);
?></td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab1" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__logo']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__logo']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__logo']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__logo']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__logo']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__logo_inverse']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__logo_inverse']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__logo_inverse']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__logo_inverse']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__logo_inverse']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__logo_sm']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__logo_sm']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__logo_sm']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__logo_sm']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__logo_sm']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__logo_xs']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__logo_xs']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__logo_xs']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__logo_xs']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__logo_xs']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__slogan']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__slogan']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__slogan']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__slogan']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__slogan']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_name']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_name']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_name']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_name']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_name']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_inn']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_inn']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_inn']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_inn']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_inn']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_kpp']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_kpp']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_kpp']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_kpp']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_kpp']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_ogrn']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_ogrn']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_ogrn']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_ogrn']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_ogrn']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_bank']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_bank']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_bank']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_bank']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_bank']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_bik']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_bik']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_bik']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_bik']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_bik']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_rs']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_rs']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_rs']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_rs']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_rs']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_ks']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_ks']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_ks']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_ks']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_ks']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_director']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_director']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_director']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_director']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_director']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_accountant']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_accountant']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_accountant']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_accountant']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_accountant']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_v_lice']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_v_lice']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_v_lice']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_v_lice']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_v_lice']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_deistvuet']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_deistvuet']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_deistvuet']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_deistvuet']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_deistvuet']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_address']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_address']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_address']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_address']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_address']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_legal_address']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_legal_address']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_legal_address']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_legal_address']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_legal_address']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_email']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_email']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_email']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_email']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_email']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__firm_name_for_notice']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__firm_name_for_notice']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__firm_name_for_notice']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__firm_name_for_notice']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__firm_name_for_notice']), 0, true);
?></td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab2" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__notice_from']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__notice_from']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__notice_from']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__notice_from']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__notice_from']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__notice_reply']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__notice_reply']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__notice_reply']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__notice_reply']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__notice_reply']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__smtp_is_use']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__smtp_is_use']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__smtp_is_use']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_is_use']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_is_use']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__smtp_host']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__smtp_host']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__smtp_host']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_host']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_host']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__smtp_port']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__smtp_port']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__smtp_port']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_port']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_port']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__smtp_secure']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__smtp_secure']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__smtp_secure']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_secure']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_secure']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__smtp_auth']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__smtp_auth']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__smtp_auth']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_auth']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_auth']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__smtp_username']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__smtp_username']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__smtp_username']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_username']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_username']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__smtp_password']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__smtp_password']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__smtp_password']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__smtp_password']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__smtp_password']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__dkim_is_use']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__dkim_is_use']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__dkim_is_use']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__dkim_is_use']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__dkim_is_use']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__dkim_domain']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__dkim_domain']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__dkim_domain']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__dkim_domain']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__dkim_domain']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__dkim_private']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__dkim_private']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__dkim_private']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__dkim_private']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__dkim_private']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__dkim_selector']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__dkim_selector']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__dkim_selector']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__dkim_selector']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__dkim_selector']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__dkim_passphrase']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__dkim_passphrase']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__dkim_passphrase']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__dkim_passphrase']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__dkim_passphrase']), 0, true);
?></td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab3" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__facebook_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__facebook_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__facebook_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__facebook_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__facebook_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__vkontakte_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__vkontakte_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__vkontakte_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__vkontakte_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__vkontakte_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__twitter_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__twitter_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__twitter_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__twitter_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__twitter_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__instagram_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__instagram_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__instagram_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__instagram_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__instagram_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__youtube_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__youtube_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__youtube_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__youtube_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__youtube_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__viber_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__viber_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__viber_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__viber_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__viber_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__telegram_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__telegram_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__telegram_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__telegram_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__telegram_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__whatsapp_group']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__whatsapp_group']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__whatsapp_group']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__whatsapp_group']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__whatsapp_group']), 0, true);
?></td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab4" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                        <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__policy_personal_data']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__policy_personal_data']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__policy_personal_data']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__policy_personal_data']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__policy_personal_data']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__agreement_cookie']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__agreement_cookie']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__agreement_cookie']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__agreement_cookie']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__agreement_cookie']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__enable_agreement_personal_data']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__enable_agreement_personal_data']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__enable_agreement_personal_data']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__enable_agreement_personal_data']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__enable_agreement_personal_data']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data_phrase']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data_phrase']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data_phrase']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data_phrase']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__agreement_personal_data_phrase']), 0, true);
?></td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                        <div class="tab-pane" id="site-config-tab5" role="tabpanel">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <table class="otable">
                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__manifest_name']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__manifest_name']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__manifest_name']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__manifest_name']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__manifest_name']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__manifest_short_name']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__manifest_short_name']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__manifest_short_name']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__manifest_short_name']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__manifest_short_name']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__manifest_icon']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__manifest_icon']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__manifest_icon']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__manifest_icon']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__manifest_icon']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__manifest_display']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__manifest_display']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__manifest_display']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__manifest_display']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__manifest_display']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__manifest_background_color']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__manifest_background_color']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__manifest_background_color']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__manifest_background_color']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__manifest_background_color']), 0, true);
?></td>
                                </tr>
                                
                                                                                                                            
                                <tr>
                                    <td class="otitle"><?php echo $_smarty_tpl->tpl_vars['elem']->value['__manifest_theme_color']->getTitle();?>
&nbsp;&nbsp;<?php if ($_smarty_tpl->tpl_vars['elem']->value['__manifest_theme_color']->getHint() != '') {?><a class="help-icon" data-placement="right" title="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['elem']->value['__manifest_theme_color']->getHint(), ENT_QUOTES, 'UTF-8', true);?>
">?</a><?php }?>
                                    </td>
                                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__manifest_theme_color']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__manifest_theme_color']), 0, true);
?></td>
                                </tr>
                                
                                                                                    </table>
                                                </div>
                    </form>
    </div>
    </div><?php }
}
