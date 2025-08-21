<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:33
  from 'D:\Projects\Hosts\life-basis.local\release\modules\pushsender\view\admin\user_push_lock.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3111292a1_81995776',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '34f9c91d1eab18d9ba8715fce8dd2a70ad270551' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\pushsender\\view\\admin\\user_push_lock.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e3111292a1_81995776 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_assignInScope('locks', $_smarty_tpl->tpl_vars['elem']->value['__push_lock']->lockApi->getUserLocks($_smarty_tpl->tpl_vars['elem']->value['id']));
$_smarty_tpl->_assignInScope('sites', $_smarty_tpl->tpl_vars['elem']->value['__push_lock']->sites);?>

<div>
    <?php if (count($_smarty_tpl->tpl_vars['sites']->value) > 1) {?>
        <ul class="tab-nav" role="tablist">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sites']->value, 'site');
$_smarty_tpl->tpl_vars['site']->index = -1;
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
$_smarty_tpl->tpl_vars['site']->index++;
$_smarty_tpl->tpl_vars['site']->first = !$_smarty_tpl->tpl_vars['site']->index;
$__foreach_site_17_saved = $_smarty_tpl->tpl_vars['site'];
?>
            <li <?php if ($_smarty_tpl->tpl_vars['site']->first) {?>class="active"<?php }?>>
                <a class="" data-target="#tab_site_<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
" role="tab" data-toggle="tab"><?php echo $_smarty_tpl->tpl_vars['site']->value['title'];?>
</a>
            </li>
        <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_17_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
    <?php }?>
    <div class="tab-content">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['sites']->value, 'site');
$_smarty_tpl->tpl_vars['site']->index = -1;
$_smarty_tpl->tpl_vars['site']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['site']->value) {
$_smarty_tpl->tpl_vars['site']->do_else = false;
$_smarty_tpl->tpl_vars['site']->index++;
$_smarty_tpl->tpl_vars['site']->first = !$_smarty_tpl->tpl_vars['site']->index;
$__foreach_site_18_saved = $_smarty_tpl->tpl_vars['site'];
?>
        <div class="tab-pane <?php if ($_smarty_tpl->tpl_vars['site']->first) {?>active<?php }?>" id="tab_site_<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
" role="tabpanel">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['elem']->value['__push_lock']->lockApi->getPushNotices(false), 'data');
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
            <div class="app-push-group m-b-20">
                <b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('appname'=>$_smarty_tpl->tpl_vars['data']->value['title']));
$_block_repeat=true;
echo smarty_block_t(array('appname'=>$_smarty_tpl->tpl_vars['data']->value['title']), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Приложение: %appname<?php $_block_repeat=false;
echo smarty_block_t(array('appname'=>$_smarty_tpl->tpl_vars['data']->value['title']), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></b><br><br>
                <label><input type="checkbox" name="push_lock[<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
][<?php echo $_smarty_tpl->tpl_vars['data']->value['app'];?>
][]" value="all" <?php if ((isset($_smarty_tpl->tpl_vars['locks']->value[$_smarty_tpl->tpl_vars['site']->value['id']][$_smarty_tpl->tpl_vars['data']->value['app']])) && in_array('all',$_smarty_tpl->tpl_vars['locks']->value[$_smarty_tpl->tpl_vars['site']->value['id']][$_smarty_tpl->tpl_vars['data']->value['app']])) {?>checked<?php }?>> Все</label><br>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['data']->value['notices'], 'notice', false, 'key');
$_smarty_tpl->tpl_vars['notice']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['notice']->value) {
$_smarty_tpl->tpl_vars['notice']->do_else = false;
?>
                <label><input type="checkbox" name="push_lock[<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
][<?php echo $_smarty_tpl->tpl_vars['data']->value['app'];?>
][]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if ((isset($_smarty_tpl->tpl_vars['locks']->value[$_smarty_tpl->tpl_vars['site']->value['id']][$_smarty_tpl->tpl_vars['data']->value['app']])) && in_array($_smarty_tpl->tpl_vars['key']->value,$_smarty_tpl->tpl_vars['locks']->value[$_smarty_tpl->tpl_vars['site']->value['id']][$_smarty_tpl->tpl_vars['data']->value['app']])) {?>checked<?php }?>> <?php echo $_smarty_tpl->tpl_vars['notice']->value;?>
</label><br>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </div>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_18_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
</div><?php }
}
