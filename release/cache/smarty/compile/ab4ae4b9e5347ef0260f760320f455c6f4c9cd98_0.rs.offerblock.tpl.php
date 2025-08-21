<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:43
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\adminblocks\offerblock\offerblock.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b738ec664_22593974',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ab4ae4b9e5347ef0260f760320f455c6f4c9cd98' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\adminblocks\\offerblock\\offerblock.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%catalog%/adminblocks/offerblock/multioffers.tpl' => 1,
    'rs:%catalog%/adminblocks/offerblock/offer_all.tpl' => 1,
  ),
),false)) {
function content_68a58b738ec664_22593974 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
echo smarty_function_addjs(array('file'=>"jquery.tablednd/jquery.tablednd.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"%catalog%/offer.css?v=6",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%catalog%/offer.js?v=4",'basepath'=>"root"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"tmpl/tmpl.min.js",'basepath'=>"common"),$_smarty_tpl);?>


<div id="offers" data-urls='{ "offerEdit": "<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'offerEdit','product_id'=>$_smarty_tpl->tpl_vars['elem']->value['id']),'catalog-block-offerblock');?>
",
                              "offerChangeWithMain": "<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'offerChangeWithMain','product_id'=>$_smarty_tpl->tpl_vars['elem']->value['id']),'catalog-block-offerblock');?>
",
                              "offerDelete": "<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'offerdelete','product_id'=>$_smarty_tpl->tpl_vars['elem']->value['id']),'catalog-block-offerblock');?>
",
                              "offerMultiEdit": "<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'offermultiedit','product_id'=>$_smarty_tpl->tpl_vars['elem']->value['id']),'catalog-block-offerblock');?>
",
                              "offerMakeFromMultioffer": "<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'OfferMakeFromMultioffers','product_id'=>$_smarty_tpl->tpl_vars['elem']->value['id']),'catalog-block-offerblock');?>
",
                              "offerLinkPhoto": "<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'OfferLinkPhoto','product_id'=>$_smarty_tpl->tpl_vars['elem']->value['id']),'catalog-block-offerblock');?>
",
                              "offerLinkPhotoSave": "<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array('odo'=>'OfferLinkPhotoSave','product_id'=>$_smarty_tpl->tpl_vars['elem']->value['id']),'catalog-block-offerblock');?>
" }'>
    <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/adminblocks/offerblock/multioffers.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    
    <div class="offer-block">
        <table class="otable">
           <tbody>
               <tr>
                    <td class="title" width="200"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Подпись к комплектациям<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:</td>
                    <td><?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['elem']->value['__offer_caption']->getRenderTemplate(), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('field'=>$_smarty_tpl->tpl_vars['elem']->value['__offer_caption']), 0, true);
?></td>
               </tr>
           </tbody>
        </table>
        
        <div id="all-offers">
            <?php $_smarty_tpl->_subTemplateRender("rs:%catalog%/adminblocks/offerblock/offer_all.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>        
        </div>
    </div>
</div>

<?php echo '<script'; ?>
>
    $.allReady(function() {
        $('#offers').offer();
    });
<?php echo '</script'; ?>
><?php }
}
