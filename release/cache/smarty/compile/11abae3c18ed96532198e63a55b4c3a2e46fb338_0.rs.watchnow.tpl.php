<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:53
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\widget\watchnow.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34205050b22_01298469',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '11abae3c18ed96532198e63a55b4c3a2e46fb338' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\widget\\watchnow.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a34205050b22_01298469 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addcss(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_css']->value)."watchnow.css",'basepath'=>"root"),$_smarty_tpl);?>


<div class="last-watch">
    <?php $_smarty_tpl->_assignInScope('item', $_smarty_tpl->tpl_vars['list']->value[0]);?>
    <?php if ($_smarty_tpl->tpl_vars['total']->value) {?>
        <?php if ($_smarty_tpl->tpl_vars['offset']->value > 0) {?><a data-update-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-widget-watchnow",'offset'=>$_smarty_tpl->tpl_vars['offset']->value-1),$_smarty_tpl);?>
" class="prev call-update"><i class="zmdi zmdi-chevron-left"></i></a><?php }?>
        <?php if ($_smarty_tpl->tpl_vars['offset']->value+1 < $_smarty_tpl->tpl_vars['total']->value) {?><a data-update-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"catalog-widget-watchnow",'offset'=>$_smarty_tpl->tpl_vars['offset']->value+1),$_smarty_tpl);?>
" class="next call-update"><i class="zmdi zmdi-chevron-right"></i></a><?php }?>
        <p class="text-center">
            <a class="login" <?php if ($_smarty_tpl->tpl_vars['item']->value['user']['href']) {?>href="<?php echo $_smarty_tpl->tpl_vars['item']->value['user']['href'];?>
"<?php }?>><?php echo $_smarty_tpl->tpl_vars['item']->value['user']['name'];?>
</a><br>
            <span class="time"><?php echo $_smarty_tpl->tpl_vars['item']->value['eventDate'];?>
</span>
        </p>
        <div class="picture">
            <a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['editUrl'];?>
">
                <img src="<?php echo $_smarty_tpl->tpl_vars['item']->value['product']->getMainImage(160,150,'xy');?>
" class="p-photo">
            </a>
        </div>    
        <div class="description">
            <a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['editUrl'];?>
" class="title"><?php echo $_smarty_tpl->tpl_vars['item']->value['product']['title'];?>
</a><br>
            <span class="path"><a href="<?php echo $_smarty_tpl->tpl_vars['item']->value['path']['href'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['path']['line'];?>
</a></span><br>
        </div>
    <?php } else { ?>
        <div class="empty">
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Ни один товар не был просмотрен<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
        </div>
    <?php }?>
</div><?php }
}
