<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:00:33
  from 'D:\Projects\Hosts\life-basis.local\release\modules\statistic\view\form\user\source_id.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e311aae878_29271205',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c77f1542c14850f3e0871edce3fc629a1185e434' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\statistic\\view\\form\\user\\source_id.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e311aae878_29271205 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
echo smarty_function_addcss(array('file'=>"%statistic%/statistic_source.css"),$_smarty_tpl);?>

<?php echo smarty_function_addjs(array('file'=>"%statistic%/statistic_source.js"),$_smarty_tpl);?>

<?php $_smarty_tpl->_assignInScope('source', $_smarty_tpl->tpl_vars['elem']->value->getSource());
$_smarty_tpl->_assignInScope('type', $_smarty_tpl->tpl_vars['source']->value->getType());
if ($_smarty_tpl->tpl_vars['source']->value['id']) {?>
    <table class="otable sourceTable">
        <tbody>
            <tr>
                <td class="otitle">
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>№<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </td>
                <td>
                    <?php echo $_smarty_tpl->tpl_vars['source']->value['id'];?>

                </td>
            </tr>
            <tr>
                <td>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Тип источника<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </td>
                <td>
                    <?php if ($_smarty_tpl->tpl_vars['type']->value['id']) {?>
                        <b><?php echo $_smarty_tpl->tpl_vars['type']->value['title'];?>
</b>
                    <?php } else { ?>
                        <b><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Не определен<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></b> <a href="<?php echo $_smarty_tpl->tpl_vars['router']->value->getAdminUrl('',null,'statistic-sourcetypesctrl');?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Посмотреть список<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                    <?php }?>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Сайт источника<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </td>
                <td>
                    <b><?php echo $_smarty_tpl->tpl_vars['source']->value['referer_site'];?>
</b>
                </td>
            </tr>
            <tr>
                <td>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Источник<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </td>
                <td>
                    <a href="#" class="openSourceDetail" data-target="#sourceDetail"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                    <div id="sourceDetail" class="sourceDetail" style="display: none">
                        <b><?php echo $_smarty_tpl->tpl_vars['source']->value['referer_source'];?>
</b>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="otitle">
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Страница посещения<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </td>
                <td>
                    <a href="#" class="openSourceDetail" data-target="#landingDetail"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                    <div id="landingDetail" class="sourceDetail" style="display: none">
                        <b><?php echo $_smarty_tpl->tpl_vars['source']->value['landing_page'];?>
</b>
                    </div>
                </td>
            </tr>
            <?php if (!empty($_smarty_tpl->tpl_vars['source']->value['utm_source'])) {?>
                <tr>
                    <td>
                        UTM Source
                    </td>
                    <td>
                        <b><?php echo $_smarty_tpl->tpl_vars['source']->value['utm_source'];?>
</b>
                    </td>
                </tr>
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['source']->value['utm_medium'])) {?>
                <tr>
                    <td>
                        UTM Medium
                    </td>
                    <td>
                        <b><?php echo $_smarty_tpl->tpl_vars['source']->value['utm_medium'];?>
</b>
                    </td>
                </tr>
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['source']->value['utm_campaign'])) {?>
                <tr>
                    <td>
                        UTM Campaign
                    </td>
                    <td>
                        <b><?php echo $_smarty_tpl->tpl_vars['source']->value['utm_campaign'];?>
</b>
                    </td>
                </tr>
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['source']->value['utm_term'])) {?>
                <tr>
                    <td>
                        UTM term
                    </td>
                    <td>
                        <b><?php echo $_smarty_tpl->tpl_vars['source']->value['utm_term'];?>
</b>
                    </td>
                </tr>
            <?php }?>
            <?php if (!empty($_smarty_tpl->tpl_vars['source']->value['utm_content'])) {?>
                <tr>
                    <td>
                        UTM Content
                    </td>
                    <td>
                        <b><?php echo $_smarty_tpl->tpl_vars['source']->value['utm_content'];?>
</b>
                    </td>
                </tr>
            <?php }?>
            <tr>
                <td>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Дата<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                </td>
                <td>
                    <b><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['source']->value['dateof'],"d.m.Y H:i:s");?>
</b>
                </td>
            </tr>
        </tbody>
    </table>
<?php } else { ?>
    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Не определен<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}
}
}
