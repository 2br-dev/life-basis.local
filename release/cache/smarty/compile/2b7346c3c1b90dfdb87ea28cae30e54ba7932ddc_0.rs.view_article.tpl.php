<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:51:08
  from 'D:\Projects\Hosts\life-basis.local\release\modules\article\view\view_article.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a321bce689a8_16666892',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2b7346c3c1b90dfdb87ea28cae30e54ba7932ddc' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\article\\view\\view_article.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a321bce689a8_16666892 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.dateformat.php','function'=>'smarty_modifier_dateformat',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),));
?>
<h1><?php echo $_smarty_tpl->tpl_vars['article']->value['title'];?>
</h1>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center">
        <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/icons/time.svg" alt="">
        <span class="ms-2 text-gray fs-5"><?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['article']->value['dateof'],"@date @time");?>
</span>
    </div>
    <div class="d-flex align-items-center">
        <div class="d-none d-sm-block me-2"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Поделиться<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:</div>
        <?php echo '<script'; ?>
 src="https://yastatic.net/es5-shims/0.0.2/es5-shims.min.js"><?php echo '</script'; ?>
>
        <?php echo '<script'; ?>
 src="https://yastatic.net/share2/share.js"><?php echo '</script'; ?>
>
        <div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,moimir"></div>
    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['article']->value['image']) {?>
    <div class="mb-md-5 mb-4 text-center">
        <img src="<?php echo $_smarty_tpl->tpl_vars['article']->value['__image']->getUrl(992,559,'xy');?>
" alt="<?php echo $_smarty_tpl->tpl_vars['article']->value['title'];?>
" loading="lazy">
    </div>
<?php }?>
<article <?php echo $_smarty_tpl->tpl_vars['article']->value->getDebugAttributes();?>
>
    <?php echo $_smarty_tpl->tpl_vars['article']->value['content'];?>

</article>

<?php echo smarty_function_moduleinsert(array('name'=>"\Photo\Controller\Block\PhotoList",'type'=>"article",'route_id_param'=>"article_id"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\modules\article\view\view_article.tpl');?>


<?php echo smarty_function_moduleinsert(array('name'=>"\Article\Controller\Block\ArticleProducts",'article_id'=>$_smarty_tpl->tpl_vars['article']->value['id']),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\modules\article\view\view_article.tpl');?>


<div class="mt-md-6 mt-5 d-grid d-sm-block">
    <a href="<?php echo $_smarty_tpl->tpl_vars['article']->value->getCategory()->getUrl();?>
" class="btn btn-outline-primary col col-sm-auto"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Вернуться к списку<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
</div>
<?php }
}
