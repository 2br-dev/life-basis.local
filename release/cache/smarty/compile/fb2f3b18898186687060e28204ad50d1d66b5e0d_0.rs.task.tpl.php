<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:53
  from 'D:\Projects\Hosts\life-basis.local\release\modules\crm\view\admin\widget\task.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a34205d84c82_78862360',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fb2f3b18898186687060e28204ad50d1d66b5e0d' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\crm\\view\\admin\\widget\\task.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%SYSTEM%/admin/widget/paginator.tpl' => 1,
  ),
),false)) {
function content_68a34205d84c82_78862360 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.dateformat.php','function'=>'smarty_modifier_dateformat',),));
if ($_smarty_tpl->tpl_vars['no_rights']->value) {?>
    <div class="empty-widget">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Недостаточно прав на просмотр задач<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php } else { ?>
<div class="widget-filters">
    <div class="dropdown">
        <a id="task-filter-switcher" data-toggle="dropdown" class="widget-dropdown-handle"><?php echo $_smarty_tpl->tpl_vars['task_filters']->value[$_smarty_tpl->tpl_vars['task_active_filter']->value];?>
 <i class="zmdi zmdi-chevron-down"></i></a>
        <ul class="dropdown-menu" aria-labelledby="task-filter-switcher">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['task_filters']->value, 'filter_title', false, 'filter_key');
$_smarty_tpl->tpl_vars['filter_title']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['filter_key']->value => $_smarty_tpl->tpl_vars['filter_title']->value) {
$_smarty_tpl->tpl_vars['filter_title']->do_else = false;
?>
                <li<?php if ($_smarty_tpl->tpl_vars['task_active_filter']->value == $_smarty_tpl->tpl_vars['filter_key']->value) {?> class="act"<?php }?>>
                    <a data-update-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"crm-widget-task",'task_active_filter'=>$_smarty_tpl->tpl_vars['filter_key']->value),$_smarty_tpl);?>
" class="call-update"><?php echo $_smarty_tpl->tpl_vars['filter_title']->value;?>
</a>
                </li>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </ul>
    </div>
</div>

<?php if (count($_smarty_tpl->tpl_vars['tasks']->value)) {?>
    <div class="m-l-20 m-r-20 no-space m-b-20">
        <table border="0" class="wtable overable">
            <thead>
            <tr>
                <th class="l-w-space"></th>
                <th></th>
                <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Номер<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Суть<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Создано<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Срок<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></th>
                <th class="r-w-space"></th>
            </tr>
            </thead>
            <tbody>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tasks']->value, 'task');
$_smarty_tpl->tpl_vars['task']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['task']->value) {
$_smarty_tpl->tpl_vars['task']->do_else = false;
?>
                <?php $_smarty_tpl->_assignInScope('status', $_smarty_tpl->tpl_vars['task']->value->getStatus());?>

                <tr data-url="<?php echo smarty_function_adminUrl(array('mod_controller'=>"crm-taskctrl",'do'=>"edit",'id'=>$_smarty_tpl->tpl_vars['task']->value['id'],'context'=>"widget"),$_smarty_tpl);?>
" data-crud-options='{ "updateThis": true }' class="clickable crud-edit">
                    <td class="l-w-space"></td>
                    <td><span style="background:<?php echo $_smarty_tpl->tpl_vars['status']->value->color;?>
" title="<?php echo $_smarty_tpl->tpl_vars['status']->value->title;?>
" class="w-point"></span></td>
                    <td><?php echo $_smarty_tpl->tpl_vars['task']->value['task_num'];?>
</td>
                    <td><?php echo $_smarty_tpl->tpl_vars['task']->value['title'];?>
</td>
                    <td class="w-date"><?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['task']->value['date_of_create'],"@date @time");?>
</td>
                    <td class="w-date"><?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "нет";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable3=ob_get_clean();
echo (($tmp = smarty_modifier_dateformat($_smarty_tpl->tpl_vars['task']->value['date_of_planned_end'],"@date @time") ?? null)===null||$tmp==='' ? $_prefixVariable3 ?? null : $tmp);?>
</td>
                    <td class="r-w-space"></td>
                </tr>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </tbody>
        </table>
    </div>
<?php } else { ?>
    <div class="empty-widget">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Нет ни одной задачи<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php }?>

<?php $_smarty_tpl->_subTemplateRender("rs:%SYSTEM%/admin/widget/paginator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('paginatorClass'=>"with-top-line"), 0, false);
}
}
}
