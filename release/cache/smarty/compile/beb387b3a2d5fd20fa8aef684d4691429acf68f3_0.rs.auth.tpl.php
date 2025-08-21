<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:02:11
  from 'D:\Projects\Hosts\life-basis.local\release\templates\system\admin\auth.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e373156a36_93584691',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'beb387b3a2d5fd20fa8aef684d4691429acf68f3' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\templates\\system\\admin\\auth.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e373156a36_93584691 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addmeta.php','function'=>'smarty_function_addmeta',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),4=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
echo smarty_function_addcss(array('file'=>"flatadmin/iconic-font/css/material-design-iconic-font.min.css",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addcss(array('file'=>"flatadmin/app.css?v=2",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"jquery.min.js",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addjs(array('file'=>"bootstrap/bootstrap.min.js",'basepath'=>"common"),$_smarty_tpl);
echo smarty_function_addmeta(array('name'=>"viewport",'content'=>"width=device-width, initial-scale=1, maximum-scale=1"),$_smarty_tpl);
echo $_smarty_tpl->tpl_vars['app']->value->setBodyClass('admin-style login-content');
if ($_smarty_tpl->tpl_vars['alternative_background_url']->value) {
echo $_smarty_tpl->tpl_vars['app']->value->setBodyAttr('style',"background-image:url(".((string)$_smarty_tpl->tpl_vars['alternative_background_url']->value).")");
}
if ($_smarty_tpl->tpl_vars['js']->value) {
echo smarty_function_addjs(array('file'=>"jquery.rs.auth.js",'basepath'=>"common"),$_smarty_tpl);
}?>

<div class="lc-block auth-win">
    <div class="rs-loading"></div>

    <div class="caption-line">

        <div class="logo-line">
            <img class="rs-auth-logo" src="<?php echo $_smarty_tpl->tpl_vars['Setup']->value['IMG_PATH'];?>
/adminstyle/flatadmin/auth/rs-logo.svg" alt="ReadyScript lab.">
        </div>

        <div class="select-lang dropdown">
            <span class="gray-around" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Выбор языка<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" data-toggle="dropdown"><?php echo $_smarty_tpl->tpl_vars['locale_list']->value[$_smarty_tpl->tpl_vars['current_lang']->value];?>
</span>
            <?php if (count($_smarty_tpl->tpl_vars['locale_list']->value) > 1) {?>
                <ul class="dropdown-menu pull-right" style="min-width:50px;">
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['locale_list']->value, 'locale', false, 'locale_key');
$_smarty_tpl->tpl_vars['locale']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['locale_key']->value => $_smarty_tpl->tpl_vars['locale']->value) {
$_smarty_tpl->tpl_vars['locale']->do_else = false;
?>
                        <?php if ($_smarty_tpl->tpl_vars['current_lang']->value != $_smarty_tpl->tpl_vars['locale_key']->value) {?>
                            <li><a href="<?php echo smarty_function_adminUrl(array('do'=>false,'mod_controller'=>false,'Act'=>'changeLang','lang'=>$_smarty_tpl->tpl_vars['locale_key']->value,'referer'=>$_smarty_tpl->tpl_vars['url']->value->getSelfUrl()),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['locale']->value;?>
</a></li>
                        <?php }?>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                </ul>
            <?php }?>
        </div>
    </div>

    <div class="error-message"><?php echo $_smarty_tpl->tpl_vars['err']->value;?>
</div>
    <div class="success-message"><?php echo $_smarty_tpl->tpl_vars['data']->value['successText'];?>
</div>

    <div class="form-box">
        <form method="POST" id="auth" action="<?php echo smarty_function_adminUrl(array('mod_controller'=>false,'do'=>false,'Act'=>"auth"),$_smarty_tpl);?>
" class="body-box" <?php if ($_smarty_tpl->tpl_vars['data']->value['do'] == 'recover') {?> style="display:none"<?php }?>>
            <input type="hidden" name="lang" value="<?php echo $_smarty_tpl->tpl_vars['current_lang']->value;?>
">
            <input type="hidden" name="referer" value="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['referer']->value, ENT_QUOTES, 'UTF-8', true);?>
">
            <input type="hidden" name="remember" value="1">

            <div class="field-zone">
                <input type="text" class="login" name="login" placeholder="<?php echo $_smarty_tpl->tpl_vars['login_placeholder']->value;?>
" value="<?php echo (($tmp = $_smarty_tpl->tpl_vars['data']->value['login'] ?? null)===null||$tmp==='' ? $_smarty_tpl->tpl_vars['Setup']->value['DEFAULT_DEMO_LOGIN'] ?? null : $tmp);?>
">
                <input type="password" class="pass" name="pass" placeholder="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Пароль<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" value="<?php echo $_smarty_tpl->tpl_vars['Setup']->value['DEFAULT_DEMO_PASS'];?>
">
            </div>

            <p class="buttons">
                <button type="submit" class="btn btn-primary btn-lg ok va-m-c"><i class="zmdi zmdi-check m-r-5"></i> <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>войти<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></button>
                <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>false,'do'=>"recover",'Act'=>"auth"),$_smarty_tpl);?>
" class="text-nowrap btn-lg forget-pass to-recover"><span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Забыли пароль?<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></a>
            </p>
        </form>

        <form method="POST" id="recover" action="<?php echo smarty_function_adminUrl(array('mod_controller'=>false,'do'=>"recover",'Act'=>"auth"),$_smarty_tpl);?>
" class="body-box recover" <?php if ($_smarty_tpl->tpl_vars['data']->value['do'] != 'recover') {?>style="display:none"<?php }?>>
            <div class="field-zone">
                <input type="text" id="login" class="login" name="login" placeholder="<?php echo $_smarty_tpl->tpl_vars['recover_login_placeholder']->value;?>
" value="<?php echo htmlspecialchars((string)$_smarty_tpl->tpl_vars['data']->value['login'], ENT_QUOTES, 'UTF-8', true);?>
">
                <p class="help">
                    <i class="corner"></i>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>На E-mail будет отправлено письмо с дальнейшими инструкциями по восстановленю пароля<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </p>
            </div>
            <p class="buttons">
                <button type="submit" class="btn btn-primary btn-lg ok va-m-c"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>отправить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></button>
                <a href="<?php echo smarty_function_adminUrl(array('mod_controller'=>false,'Act'=>"auth",'do'=>false),$_smarty_tpl);?>
" class="btn-lg forget-pass back-to-auth"><span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>авторизация<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span></a>
            </p>
        </form>
    </div>
</div><?php }
}
