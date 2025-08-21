<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:27
  from 'D:\Projects\Hosts\life-basis.local\release\modules\crm\view\admin\ormtype\interactionblock.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e30b068d60_35221584',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a4f3100f3c1e43fae23cdddab89b33018f6adcd0' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\crm\\view\\admin\\ormtype\\interactionblock.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e30b068d60_35221584 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
if ($_smarty_tpl->tpl_vars['field']->value->isOnlyExists() && $_smarty_tpl->tpl_vars['elem']->value['id'] < 1) {?>
    <div class="notice"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Управление взаимосвязями возможно только в режиме редактирования объекта<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
<?php } else { ?>
    <?php echo smarty_function_moduleinsert(array('name'=>"\Crm\Controller\Admin\Block\InteractionBlock",'link_id'=>$_smarty_tpl->tpl_vars['elem']->value['id'],'link_type'=>$_smarty_tpl->tpl_vars['field']->value->getLinkType(),'from_call'=>$_smarty_tpl->tpl_vars['elem']->value['create_from_call']),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\modules\crm\view\admin\ormtype\interactionblock.tpl');?>

<?php }
}
}
