<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:44
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\adminblocks\offerblock\offer_all.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b74685925_59627910',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c8714efb9f648f2b9de0d75a26bf575a72f976bf' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\adminblocks\\offerblock\\offer_all.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/adminblocks/offerblock/offer_main.tpl' => 1,
    'rs:%catalog%/adminblocks/offerblock/offer_ext.tpl' => 1,
  ),
),false)) {
function content_68a58b74685925_59627910 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<div class="offer-container">
    <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/adminblocks/offerblock/offer_main.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
</div>

<div id="external-offers">
    <h3><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Дополнительные комплектации<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></h3>
    <div id="ext-offers">
        <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/adminblocks/offerblock/offer_ext.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    </div>
</div><?php }
}
