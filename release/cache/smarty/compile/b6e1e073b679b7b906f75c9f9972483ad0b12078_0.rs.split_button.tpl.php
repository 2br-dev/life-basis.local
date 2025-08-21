<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:27
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\html_elements\toolbar\button\split_button.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318ab535562_90748641',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b6e1e073b679b7b906f75c9f9972483ad0b12078' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\html_elements\\toolbar\\button\\split_button.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%system%/admin/html_elements/toolbar/button/button.tpl' => 3,
  ),
),false)) {
function content_68a318ab535562_90748641 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['button']->value->getSplitButton()) {?>
    <div class="btn-group rs-split-button">
        <?php $_smarty_tpl->_subTemplateRender("rs:%system%/admin/html_elements/toolbar/button/button.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
        <?php $_smarty_tpl->_subTemplateRender("rs:%system%/admin/html_elements/toolbar/button/button.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('button'=>$_smarty_tpl->tpl_vars['button']->value->getSplitButton()), 0, true);
?>
    </div>
<?php } else { ?>
    <?php $_smarty_tpl->_subTemplateRender("rs:%system%/admin/html_elements/toolbar/button/button.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
}
}
}
