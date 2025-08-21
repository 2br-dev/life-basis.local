<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:12:21
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\body.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a318a5b52c78_14886114',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a7b297527b162db354970508702ff4110f0ea042' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\body.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a318a5b52c78_14886114 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addmeta.php','function'=>'smarty_function_addmeta',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),4=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),5=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.meter.php','function'=>'smarty_function_meter',),6=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.moduleinsert.php','function'=>'smarty_function_moduleinsert',),7=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.modulegetvars.php','function'=>'smarty_function_modulegetvars',),8=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.teaser.php','function'=>'smarty_modifier_teaser',),));
echo smarty_function_addjs(array('file'=>"jquery.rs.autotranslit.js"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.messenger.js"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jstour/jquery.tour.engine.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jstour/jquery.tour.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%main%/jquery.rsnews.js"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.admindebug.js"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"jquery.rs.barcode.js"),$_smarty_tpl);?>


<?php echo smarty_function_addcss(array('file'=>"flatadmin/readyscript.ui/jquery-ui.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"flatadmin/app.css?v=4",'basepath'=>"common",'no_compress'=>true),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"flatadmin/iconic-font/css/material-design-iconic-font.min.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/tour.css",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addcss(array('file'=>"common/animate.css",'basepath'=>"common"),$_smarty_tpl);?>



<?php echo smarty_function_addjs(array('file'=>"jquery.min.js",'name'=>"jquery",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"dialog-options/jquery.dialogoptions.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"bootstrap/bootstrap.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js",'basepath'=>"common"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"webpjs/rs.webpcheck.js"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%crm%/jquery.rs.telephony.js"),$_smarty_tpl);?>


<?php if (strstr($_SERVER['HTTP_USER_AGENT'],'iPad')) {?>
    <?php echo smarty_function_addmeta(array('name'=>"viewport",'content'=>"width=device-width, initial-scale=1, maximum-scale=1"),$_smarty_tpl);?>

<?php } else { ?>
    <?php echo smarty_function_addmeta(array('name'=>"viewport",'content'=>"width=device-width, initial-scale=0.75, maximum-scale=0.75"),$_smarty_tpl);?>

<?php }?>

<?php echo $_smarty_tpl->tpl_vars['app']->value->setBodyClass('admin-body admin-style');?>

<header id="header" class="clearfix" data-spy="affix" data-offset-top="65">
    <ul class="header-inner">
        <li class="rs-logo">
            <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>false,'do'=>false),$_smarty_tpl);?>
" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>на&nbsp;главную<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" data-placement="right"></a>
            <div id="menu-trigger"><i class="zmdi zmdi-menu"></i></div>
        </li>
        
        <li class="header-panel">
            <div class="viewport">
                <div class="fixed-tools">
                    <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>false,'do'=>false),$_smarty_tpl);?>
" class="to-main">
                        <i class="rs-icon rs-black-logo"></i><br>
                        <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>главная<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                    </a>

                    <a href="<?php echo $_smarty_tpl->tpl_vars['site_root_url']->value;?>
" class="to-site">
                        <i class="rs-icon rs-icon-view"></i><br>
                        <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>на сайт<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                    </a>

                    <?php if ($_smarty_tpl->tpl_vars['has_cache_clean_right']->value) {?>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array("Act"=>"cleanCache"));?>
" class="rs-clean-cache">
                            <i class="rs-icon rs-icon-refresh"><!----></i><br>
                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>кэш<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                        </a>
                    <?php }?>
                </div>

                <div class="float-tools">
                    <div class="dropdown rs-meter-group">
                        <a class="toggle visible-xs-inline-block" data-toggle="dropdown" id="floatTools" aria-haspopup="true">
                            <i class="zmdi zmdi-more-vert"><?php echo smarty_function_meter(array(),$_smarty_tpl);?>
</i>
                        </a>
                        <ul class="ft-dropdown-menu" aria-labelledby="floatTools">
                            <?php echo smarty_function_moduleinsert(array('name'=>"\Main\Controller\Admin\Block\HeaderPanel",'indexTemplate'=>"%main%/adminblocks/headerpanel/header_panel_items.tpl"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\body.tpl');?>

                            <?php echo smarty_function_moduleinsert(array('name'=>"\Main\Controller\Admin\Block\RsAlerts"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\body.tpl');?>

                            <?php echo smarty_function_moduleinsert(array('name'=>"\Main\Controller\Admin\Block\RsNews"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\body.tpl');?>


                            <li class="ft-hover-node">
                                <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"users-ctrl",'do'=>"edit",'id'=>$_smarty_tpl->tpl_vars['current_user']->value['id']),$_smarty_tpl);?>
">
                                    <i class="rs-icon rs-icon-user"></i>
                                    <span><?php echo $_smarty_tpl->tpl_vars['current_user']->value->getFio();?>
</span>
                                </a>

                                <ul class="ft-sub">
                                    <li>
                                        <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>"users-ctrl",'do'=>"myQrCode"),$_smarty_tpl);?>
" class="crud-edit crud-sm-dialog">
                                            <i class="rs-icon zmdi zmdi-fullscreen"></i>
                                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>QR-код<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array('Act'=>'logout'));?>
">
                                            <i class="rs-icon zmdi zmdi-power"></i>
                                            <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выход<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</header>

<aside id="sidebar">
    <?php if ($_COOKIE['rsAdminSideMenu']) {
echo $_smarty_tpl->tpl_vars['app']->value->setBodyClass('closed',true);
}?>
    <?php echo smarty_function_modulegetvars(array('name'=>"\Site\Controller\Admin\BlockSelectSite",'var'=>"sites"),$_smarty_tpl);?>


    <ul class="side-menu rs-site-manager">
        <li class="sm-node">
            <a class="current">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sites']->value['sites'], 'site');
$_smarty_tpl->tpl_vars['site']->iteration = 0;
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
$_smarty_tpl->tpl_vars['site']->iteration++;
$__foreach_site_7_saved = $_smarty_tpl->tpl_vars['site'];
?>
                    <?php if ($_smarty_tpl->tpl_vars['site']->value['id'] == $_smarty_tpl->tpl_vars['sites']->value['current']['id']) {?>
                        <span class="number"><?php echo $_smarty_tpl->tpl_vars['site']->iteration;?>
</span>
                        <span class="domain"><?php echo smarty_modifier_teaser($_smarty_tpl->tpl_vars['sites']->value['current']['title'],"27");?>
</span>
                    <?php }?>
                <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_7_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                <span class="caret"></span>
            </a>
            <div class="sm">
                <div class="sm-head">
                    <a class="menu-close"><i class="zmdi zmdi-close"></i></a>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выберите сайт<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </div>
                <div class="sm-body">
                    <ul>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sites']->value['sites'], 'site');
$_smarty_tpl->tpl_vars['site']->iteration = 0;
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
$_smarty_tpl->tpl_vars['site']->iteration++;
$__foreach_site_8_saved = $_smarty_tpl->tpl_vars['site'];
?>
                        <li>
                            <li <?php if ($_smarty_tpl->tpl_vars['sites']->value['current']['id'] == $_smarty_tpl->tpl_vars['site']->value['id']) {?>class="active"<?php }?>>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getUrl('main.admin',array('Act'=>'changeSite','site'=>$_smarty_tpl->tpl_vars['site']->value['id']));?>
"><?php echo $_smarty_tpl->tpl_vars['site']->iteration;?>
. <?php echo $_smarty_tpl->tpl_vars['site']->value['title'];?>
</a>
                            </li>
                        </li>
                        <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_8_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </ul>
                </div>
            </div>
        </li>
    </ul>
    
    <div class="side-scroll">
        <?php echo smarty_function_moduleinsert(array('name'=>"\Menu\Controller\Admin\View"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\body.tpl');?>


        <?php if (\RS\Module\Manager::staticModuleExists('marketplace') && $_smarty_tpl->tpl_vars['has_marketplace_right']->value) {?>
            <ul class="side-menu side-utilites">
                <li>
                    <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl(false,array(),'marketplace-ctrl');?>
">
                        <i class="rs-icon rs-icon-marketplace"></i>
                        <span class="title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('alias'=>"маркетплейс"));
$_block_repeat=true;
echo smarty_block_t(array('alias'=>"маркетплейс"), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Маркет<span class="visible-open">плейс</span><?php $_block_repeat=false;
echo smarty_block_t(array('alias'=>"маркетплейс"), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
                    </a>
                </li>
            </ul>
        <?php }?>
    </div>

    <a class="side-collapse" data-toggle-class="closed" data-target="body" data-toggle-cookie="rsAdminSideMenu">
        <i class="rs-icon rs-icon-back"></i>
        <span class="text"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Свернуть меню<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
    </a>
</aside>

<section id="content">
    <?php echo smarty_function_moduleinsert(array('name'=>"\Main\Controller\Admin\Block\RsVisibleAlerts"),$_smarty_tpl,'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\body.tpl');?>

    <?php echo $_smarty_tpl->tpl_vars['app']->value->blocks->getMainContent();?>

</section><?php }
}
