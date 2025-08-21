<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:46
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\photo\form_onepic_product.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b7688e723_94628341',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '687eaa778e2ef8b8f563b630b70f4a69190929d5' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\photo\\form_onepic_product.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b7688e723_94628341 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['photo_list']->value, 'photo', false, 'key');
$_smarty_tpl->tpl_vars['photo']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['photo']->value) {
$_smarty_tpl->tpl_vars['photo']->do_else = false;
?>
    <li class="photo-one" data-id="<?php echo $_smarty_tpl->tpl_vars['photo']->value['id'];?>
">
        <div class="chk"><input type="checkbox" name="photos[]" value="<?php echo $_smarty_tpl->tpl_vars['photo']->value['id'];?>
"></div>
        <div class="image" data-small-image="<?php echo $_smarty_tpl->tpl_vars['photo']->value->getUrl(30,30,'xy');?>
">
            <a href="<?php ob_start();
echo $_smarty_tpl->tpl_vars['photo']->value['id'];
$_prefixVariable7 = ob_get_clean();
echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'do'=>false,'pdo'=>"delphoto",'photos'=>array($_prefixVariable7)),$_smarty_tpl);?>
" class="delete confirm-delete" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить фото<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
            <a href="<?php echo $_smarty_tpl->tpl_vars['photo']->value->getUrl(800,600,'xy');?>
" rel="lightbox-tour" class="bigview"><img src="<?php echo $_smarty_tpl->tpl_vars['photo']->value->getUrl(148,148,'cxy');?>
"></a>
        </div>
        <div class="title">
            <div class="short" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нажмите, чтобы редактировать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['photo']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
</div>
            <div class="more">...</div>
            <textarea class="edit_title"><?php echo $_smarty_tpl->tpl_vars['photo']->value['title'];?>
</textarea>
        </div>
        <div class="move">
            <a class="rotate ccw" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Повернуть против часовой стрелки<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'do'=>false,'pdo'=>"rotate",'photoid'=>$_smarty_tpl->tpl_vars['photo']->value['id'],'direction'=>"ccw"),$_smarty_tpl);?>
"></a>
            <a class="flip horizontal" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отразить по горизонтали<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'do'=>false,'pdo'=>"flip",'photoid'=>$_smarty_tpl->tpl_vars['photo']->value['id'],'direction'=>"horizontal"),$_smarty_tpl);?>
"></a>
            <div class="handle"></div>
            <a class="rotate cw" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Повернуть по часовой стрелке<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'do'=>false,'pdo'=>"rotate",'photoid'=>$_smarty_tpl->tpl_vars['photo']->value['id'],'direction'=>"cw"),$_smarty_tpl);?>
"></a>
            <a class="flip vertical" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отразить по вертикали<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"photo-blockphotos",'do'=>false,'pdo'=>"flip",'photoid'=>$_smarty_tpl->tpl_vars['photo']->value['id'],'direction'=>"vertical"),$_smarty_tpl);?>
"></a>
        </div>
        <div class="complekts add-offer-link" data-id="<?php echo $_smarty_tpl->tpl_vars['photo']->value['id'];?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Назначить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
    </li>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
