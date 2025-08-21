<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:23:26
  from 'D:\Projects\Hosts\life-basis.local\release\modules\photo\view\blocks\photolist\photolist.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b3e3e1839_61122707',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'db53a37499f77dbd6017110edba0224a8b2ae99f' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\photo\\view\\blocks\\photolist\\photolist.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31b3e3e1839_61122707 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
if (!empty($_smarty_tpl->tpl_vars['photos']->value)) {?>
    <div class="mt-5">
        <h2><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Фото<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></h2>
        <ul class="gallery">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['photos']->value, 'photo');
$_smarty_tpl->tpl_vars['photo']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['photo']->value) {
$_smarty_tpl->tpl_vars['photo']->do_else = false;
?>
                <li>
                    <a rel="lightbox" href="<?php echo $_smarty_tpl->tpl_vars['photo']->value->getUrl(1200,900);?>
" title="<?php echo $_smarty_tpl->tpl_vars['photo']->value['title'];?>
">
                        <img src="<?php echo $_smarty_tpl->tpl_vars['photo']->value->getUrl(280,210,'cxy');?>
">
                    </a>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
    </div>
<?php }
}
}
