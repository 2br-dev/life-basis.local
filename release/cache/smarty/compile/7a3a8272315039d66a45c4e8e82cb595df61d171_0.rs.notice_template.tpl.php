<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:02:03
  from 'D:\Projects\Hosts\life-basis.local\release\modules\alerts\view\notice_template.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e36b5da2b8_34394099',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7a3a8272315039d66a45c4e8e82cb595df61d171' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\alerts\\view\\notice_template.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e36b5da2b8_34394099 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-type" content="text/html; Charset=utf-8" >
    <meta name="viewport" content="width=device-width, initial-scale=1.0" >
</head>
<body>
    <style>
        #table-wrapper {
            line-height:150%;
        }

        h1,h2,h3,h4,h5,h6 {
            line-height:normal;
        }

        @media (max-width:500px) {
            #table-wrapper {
                font-size: 12px;
            }
            table {
                font-size:inherit;
            }
        }
    </style>
    <table border="0" cellspacing="0" cellpadding="0" bgcolor="#eeeeee" style="font-family:Arial, sans-serif; border-collapse: collapse; width: 100%; height: 100%; line-height:150%; font-size:14px;" id="table-wrapper">
        <tbody>
        <tr>
            <td>
                <div style="padding:0px 15px 15px;">
                    <table align="center" border="0" cellspacing="0" cellpadding="0" style="max-width: 640px; padding:40px 0;">
                    <tbody>
                    <tr>
                        <td>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%" style="min-width:400px; border-collapse:collapse;margin-bottom: 15px;">
                                <tr>
                                    <td width="40"></td>
                                    <td>
                                        <a style="display: inline-block;" href="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
" target="_blank">
                                            <img src="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value->__logo->getUrl(400,50,'xy',true);?>
" alt="" style="border: none;">
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <table border="0" cellspacing="0" cellpadding="0" width="100%" style="min-width:400px; padding:40px; background: #fff;">
                                <tbody>
                                <tr>
                                    <td>
                                        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_125780506068a5e36b563403_48753374', "content");
?>

                                        <p>
                                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>С наилучшими пожеланиями<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>,<br />
                                            <a href="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
" target="_blank">
                                                <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['firm_name_for_notice']) {
echo $_smarty_tpl->tpl_vars['CONFIG']->value['firm_name_for_notice'];
} else {
echo $_smarty_tpl->tpl_vars['SITE']->value->getMainDomain(true);
}?>
                                            </a>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: block; text-align: center; padding-bottom: 15px">
                                        <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/spacer.gif" width="100%" height="1" style="background: #eeeeee;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: block; text-align: center; line-height: 10px; padding: 15px 0">
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['facebook_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['facebook_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/facebook.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['twitter_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['twitter_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/twitter.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['instagram_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['instagram_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/instagram.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['vkontakte_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['vkontakte_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/vk.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['youtube_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['youtube_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/youtube.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['viber_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['viber_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/viber.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['telegram_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['telegram_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/telegram.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['CONFIG']->value['whatsapp_group']) {?>
                                            <a style="display: inline-block; padding: 0 5px;" href="<?php echo $_smarty_tpl->tpl_vars['CONFIG']->value['whatsapp_group'];?>
" target="_blank">
                                                <img src="<?php echo $_smarty_tpl->tpl_vars['SITE']->value->getRootUrl(true);?>
modules/alerts/view/img/whatsapp.png" width="32" style="border: none;"/>
                                            </a>
                                        <?php }?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="display: block; text-align: center;">
                                        <p style="font-size: 70%; color: #B3B3B3; margin: 0; font-family: Tahoma;"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Это автоматическая рассылка, на это письмо отвечать нет необходимости.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</body>
</html><?php }
/* {block "content"} */
class Block_125780506068a5e36b563403_48753374 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_125780506068a5e36b563403_48753374',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block "content"} */
}
