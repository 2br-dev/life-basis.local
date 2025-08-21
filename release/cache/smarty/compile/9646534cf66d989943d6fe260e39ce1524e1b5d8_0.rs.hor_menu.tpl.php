<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:48
  from 'D:\Projects\Hosts\life-basis.local\release\modules\menu\view\blocks\menu\hor_menu.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a3180cb072c5_05180212',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9646534cf66d989943d6fe260e39ce1524e1b5d8' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\menu\\view\\blocks\\menu\\hor_menu.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/helper/usertemplate/include/block_stub.tpl' => 1,
  ),
),false)) {
function content_68a3180cb072c5_05180212 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),));
if ($_smarty_tpl->tpl_vars['items']->value->count()) {?>
    <ul class="head-bar__menu">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
            <?php $_smarty_tpl->_assignInScope('hasChilds', $_smarty_tpl->tpl_vars['item']->value->getChildsCount());?>
            <li>
                <a class="head-bar__link" <?php if (!$_smarty_tpl->tpl_vars['hasChilds']->value) {?>href="<?php echo $_smarty_tpl->tpl_vars['item']->value['fields']->getHref();?>
"<?php } else { ?>href="#" data-bs-toggle="dropdown" data-bs-reference="parent"<?php }?> <?php if ($_smarty_tpl->tpl_vars['item']->value['fields']['target_blank']) {?>target="_blank"<?php }?>>
                    <span><?php echo $_smarty_tpl->tpl_vars['item']->value['fields']['title'];?>
</span>
                    <?php if ($_smarty_tpl->tpl_vars['hasChilds']->value) {?>
                        <svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M3.8193 6.14645C4.02237 5.95118 4.35162 5.95118 4.55469 6.14645L8.00033 9.45956L11.446 6.14645C11.649 5.95118 11.9783 5.95118 12.1814 6.14645C12.3844 6.34171 12.3844 6.65829 12.1814 6.85355L8.36802 10.5202C8.16495 10.7155 7.8357 10.7155 7.63263 10.5202L3.8193 6.85355C3.61622 6.65829 3.61622 6.34171 3.8193 6.14645Z"/>
                        </svg>
                    <?php }?>
                </a>
                <?php if ($_smarty_tpl->tpl_vars['hasChilds']->value) {?>
                    <ul class="dropdown-menu head-bar__dropdown">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['item']->value->getChilds(), 'subitem');
$_smarty_tpl->tpl_vars['subitem']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['subitem']->value) {
$_smarty_tpl->tpl_vars['subitem']->do_else = false;
?>
                            <li><a class="dropdown-item" href="<?php echo $_smarty_tpl->tpl_vars['subitem']->value['fields']->getHref();?>
"><?php echo $_smarty_tpl->tpl_vars['subitem']->value['fields']['title'];?>
</a></li>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </ul>
                <?php }?>
            </li>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </ul>
<?php } else { ?>
    <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Меню";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable1=ob_get_clean();
ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Добавить пункт меню";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable2=ob_get_clean();
ob_start();
echo smarty_function_adminUrl(array('do'=>false,'mod_controller'=>"menu-ctrl"),$_smarty_tpl);
$_prefixVariable3=ob_get_clean();
ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Настроить блок";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable4=ob_get_clean();
ob_start();
echo $_smarty_tpl->tpl_vars['this_controller']->value->getSettingUrl();
$_prefixVariable5 = ob_get_clean();
$_smarty_tpl->_subTemplateRender("rs:%THEME%/helper/usertemplate/include/block_stub.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('name'=>$_prefixVariable1,'skeleton'=>"skeleton-head-menu.svg",'do'=>array(array('title'=>$_prefixVariable2,'href'=>$_prefixVariable3),array('title'=>$_prefixVariable4,'href'=>$_prefixVariable5,'class'=>'crud-add'))), 0, false);
}
}
}
