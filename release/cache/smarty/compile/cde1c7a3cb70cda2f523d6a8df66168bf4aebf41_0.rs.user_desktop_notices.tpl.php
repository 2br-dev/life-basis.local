<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:25
  from 'D:\Projects\Hosts\life-basis.local\release\modules\alerts\view\form\user\user_desktop_notices.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3098f4923_76633575',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cde1c7a3cb70cda2f523d6a8df66168bf4aebf41' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\alerts\\view\\form\\user\\user_desktop_notices.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e3098f4923_76633575 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('notice_types', $_smarty_tpl->tpl_vars['elem']->value['__desktop_notice_locks']->alerts_api->getDesktopNoticeTypes());
$_smarty_tpl->_assignInScope('locks', $_smarty_tpl->tpl_vars['elem']->value['__desktop_notice_locks']->alerts_api->getAllLockedUserDesktopNotices($_smarty_tpl->tpl_vars['elem']->value['id']));
$_smarty_tpl->_assignInScope('sites', $_smarty_tpl->tpl_vars['elem']->value['__desktop_notice_locks']->sites);?>

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
$__foreach_site_5_saved = $_smarty_tpl->tpl_vars['site'];
?>
                <li <?php if ($_smarty_tpl->tpl_vars['site']->first) {?>class="active"<?php }?>>
                    <a class="" data-target="#tab_noticelock_site_<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
" role="tab" data-toggle="tab"><?php echo $_smarty_tpl->tpl_vars['site']->value['title'];?>
</a>
                </li>
            <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_5_saved;
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
$__foreach_site_6_saved = $_smarty_tpl->tpl_vars['site'];
?>
            <div class="tab-pane <?php if ($_smarty_tpl->tpl_vars['site']->first) {?>active<?php }?>" id="tab_noticelock_site_<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
" role="tabpanel">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['notice_types']->value, 'data', false, 'key');
$_smarty_tpl->tpl_vars['data']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->do_else = false;
?>
                    <label><input type="checkbox" name="desktop_notice_locks[<?php echo $_smarty_tpl->tpl_vars['site']->value['id'];?>
][]" value="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" <?php if ((isset($_smarty_tpl->tpl_vars['locks']->value[$_smarty_tpl->tpl_vars['site']->value['id']])) && in_array($_smarty_tpl->tpl_vars['key']->value,$_smarty_tpl->tpl_vars['locks']->value[$_smarty_tpl->tpl_vars['site']->value['id']])) {?>checked<?php }?>> <?php echo $_smarty_tpl->tpl_vars['data']->value['title'];?>
</label><br>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </div>
        <?php
$_smarty_tpl->tpl_vars['site'] = $__foreach_site_6_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
</div><?php }
}
