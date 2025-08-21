<?php
/* Smarty version 4.3.1, created on 2025-08-20 18:03:54
  from 'D:\Projects\Hosts\life-basis.local\release\modules\crm\view\admin\blocks\interaction\interaction_list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a5e3da9b3461_47023429',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '40eb95898728d3580bfa7f824cd50948f28060df' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\crm\\view\\admin\\blocks\\interaction\\interaction_list.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a5e3da9b3461_47023429 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addjs.php','function'=>'smarty_function_addjs',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\modifier.dateformat.php','function'=>'smarty_modifier_dateformat',),));
echo smarty_function_addjs(array('file'=>"%crm%/jquery.rs.blockcrm.js"),$_smarty_tpl);?>


<?php if ($_smarty_tpl->tpl_vars['rights']->value === null) {?>
    <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['rights']) ? $_smarty_tpl->tpl_vars['rights']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['interaction_update'] = true;
$_smarty_tpl->_assignInScope('rights', $_tmp_array);?>
    <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['rights']) ? $_smarty_tpl->tpl_vars['rights']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['interaction_read'] = true;
$_smarty_tpl->_assignInScope('rights', $_tmp_array);?>
    <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['rights']) ? $_smarty_tpl->tpl_vars['rights']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['interaction_create'] = true;
$_smarty_tpl->_assignInScope('rights', $_tmp_array);?>
    <?php $_tmp_array = isset($_smarty_tpl->tpl_vars['rights']) ? $_smarty_tpl->tpl_vars['rights']->value : array();
if (!(is_array($_tmp_array) || $_tmp_array instanceof ArrayAccess)) {
settype($_tmp_array, 'array');
}
$_tmp_array['interaction_delete'] = true;
$_smarty_tpl->_assignInScope('rights', $_tmp_array);
}?>

<?php if ($_smarty_tpl->tpl_vars['link_type']->value == 'crm-linktypeuser') {?>
    <?php $_smarty_tpl->_assignInScope('link_suffix', '-user');
}?>
<div class="crm-block-interaction<?php echo $_smarty_tpl->tpl_vars['link_suffix']->value;?>
" data-refresh-url="<?php echo $_smarty_tpl->tpl_vars['this_controller']->value->makeUrl();?>
"
                                   data-remove-url="<?php echo smarty_function_adminUrl(array('do'=>false,'intdo'=>"remove",'link_type'=>$_smarty_tpl->tpl_vars['link_type']->value,'link_id'=>$_smarty_tpl->tpl_vars['link_id']->value,'mod_controller'=>"crm-block-interactionblock"),$_smarty_tpl);?>
" >
    <div class="notice notice-yellow">
        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Создавайте документ `взаимодействие` после каждого звонка или встречи с клиентом, фиксируйте результат.
        Так у вас сохранится вся история взаимодействия с клиентом.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>

    <div class="tools-top">
        <?php if ($_smarty_tpl->tpl_vars['rights']->value['interaction_create']) {?>
            <a class="btn btn-success add-interaction va-m-c" data-url="<?php echo smarty_function_adminUrl(array('do'=>'add','link_type'=>$_smarty_tpl->tpl_vars['link_type']->value,'link_id'=>$_smarty_tpl->tpl_vars['link_id']->value,'from_call'=>$_smarty_tpl->tpl_vars['from_call']->value,'mod_controller'=>"crm-interactionctrl"),$_smarty_tpl);?>
">
                <i class="zmdi zmdi-plus m-r-5 f-18"></i>
                <span><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавить взаимодействие<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
            </a>
        <?php }?>
    </div>

    <div class="table-mobile-wrapper">
        <table class="rs-table values-list localform">
            <thead>
                <tr>
                    <th class="chk" style="width:26px">
                        <div class="chkhead-block">
                            <input type="checkbox" data-name="interaction[]" class="chk_head select-page" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отметить элементы на этой странице<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
                            <div class="onover">
                                <input type="checkbox" class="select-all" value="on" name="selectAll" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отметить элементы на всех страницах<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>">
                            </div>
                        </div>
                    </th>

                    <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Краткое содержание";
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
echo "Создано";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable2=ob_get_clean();
ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Создатель";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable3=ob_get_clean();
$_smarty_tpl->_assignInScope('columns', array('title'=>array('sort'=>true,'name'=>$_prefixVariable1),'date_of_create'=>array('sort'=>true,'name'=>$_prefixVariable2),'creator_user_id'=>array('sort'=>true,'name'=>$_prefixVariable3)));?>

                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['columns']->value, 'column', false, 'key');
$_smarty_tpl->tpl_vars['column']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['column']->value) {
$_smarty_tpl->tpl_vars['column']->do_else = false;
?>
                        <th>
                            <?php if ($_smarty_tpl->tpl_vars['column']->value['sort']) {?>
                                <a data-url="<?php ob_start();
echo $_smarty_tpl->tpl_vars['key']->value;
$_prefixVariable4 = ob_get_clean();
echo $_smarty_tpl->tpl_vars['this_controller']->value->makeUrl(array('sort'=>$_prefixVariable4,'nsort'=>$_smarty_tpl->tpl_vars['default_n_sort']->value[$_smarty_tpl->tpl_vars['key']->value]));?>
" class="refresh sortable <?php if ($_smarty_tpl->tpl_vars['cur_sort']->value == $_smarty_tpl->tpl_vars['key']->value) {
echo $_smarty_tpl->tpl_vars['cur_n_sort']->value;
}?>"><?php echo $_smarty_tpl->tpl_vars['column']->value['name'];?>
</a>
                            <?php } else { ?>
                                <?php echo $_smarty_tpl->tpl_vars['column']->value['name'];?>

                            <?php }?>
                        </th>
                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

                    <th class="actions"></th>
                </tr>
            </thead>
            <tbody <?php if (!$_smarty_tpl->tpl_vars['rights']->value['interaction_read']) {?>hidden<?php }?>>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['interactions']->value, 'interaction');
$_smarty_tpl->tpl_vars['interaction']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['interaction']->value) {
$_smarty_tpl->tpl_vars['interaction']->do_else = false;
?>
                    <tr data-id="<?php echo $_smarty_tpl->tpl_vars['interaction']->value['id'];?>
">
                        <td class="chk"><input type="checkbox" name="interaction[]" value="<?php echo $_smarty_tpl->tpl_vars['interaction']->value['id'];?>
"></td>
                        <td><a class="interaction-edit" data-url="<?php ob_start();
echo $_smarty_tpl->tpl_vars['interaction']->value['id'];
$_prefixVariable5 = ob_get_clean();
echo smarty_function_adminUrl(array('do'=>'edit','id'=>$_prefixVariable5,'mod_controller'=>"crm-interactionctrl"),$_smarty_tpl);?>
"><?php echo $_smarty_tpl->tpl_vars['interaction']->value['title'];?>
</a></td>
                        <td><?php echo smarty_modifier_dateformat($_smarty_tpl->tpl_vars['interaction']->value['date_of_create'],"@date @time:@sec");?>
</td>
                        <td>
                            <?php $_smarty_tpl->_assignInScope('user', $_smarty_tpl->tpl_vars['interaction']->value->getCreatorUser());?>
                            <?php if ($_smarty_tpl->tpl_vars['user']->value->id > 0) {?>
                                <?php if ($_smarty_tpl->tpl_vars['current_user']->value['id'] == $_smarty_tpl->tpl_vars['user']->value->id) {
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Вы, <?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
}?>
                                <?php echo $_smarty_tpl->tpl_vars['user']->value->getFio();?>
 (<?php echo $_smarty_tpl->tpl_vars['user']->value->id;?>
)
                            <?php } else { ?>
                                <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Не назначен<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                            <?php }?>
                        </td>
                        <td class="actions">
                            <div class="inline-tools">
                                <?php if ($_smarty_tpl->tpl_vars['rights']->value['interaction_update']) {?>
                                    <a data-url="<?php ob_start();
echo $_smarty_tpl->tpl_vars['interaction']->value['id'];
$_prefixVariable6 = ob_get_clean();
echo smarty_function_adminUrl(array('do'=>'edit','id'=>$_prefixVariable6,'mod_controller'=>"crm-interactionctrl"),$_smarty_tpl);?>
" class="tool interaction-edit" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Редактировать<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-edit"></i></a>
                                <?php }?>
                                <?php if ($_smarty_tpl->tpl_vars['rights']->value['interaction_delete']) {?>
                                    <a class="tool interaction-del" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"><i class="zmdi zmdi-delete c-red"></i></a>
                                <?php }?>
                            </div>
                        </td>
                    </tr>
                <?php
}
if ($_smarty_tpl->tpl_vars['interaction']->do_else) {
?>
                    <tr>
                        <td colspan="5"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Пока нет ни одного взаимодействия<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></td>
                    </tr>
                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
            </tbody>
        </table>
    </div>

    <div class="tools-bottom">
        <div class="paginator virtual-form" data-action="<?php echo $_smarty_tpl->tpl_vars['this_controller']->value->makeUrl(array('int_page'=>null,'int_page_size'=>null));?>
">
            <?php echo $_smarty_tpl->tpl_vars['paginator']->value->getView(array('is_virtual'=>true));?>

        </div>
    </div>

    <?php if ($_smarty_tpl->tpl_vars['rights']->value['interaction_delete']) {?>
        <div class="group-toolbar">
            <span class="checked-offers"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Отмеченные<br> значения<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>:</span>
            <a class="btn btn-danger delete"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
        </div>
    <?php }?>
</div>

<?php echo '<script'; ?>
>
    $.allReady(function() {
        $('.crm-block-interaction<?php echo $_smarty_tpl->tpl_vars['link_suffix']->value;?>
').blockCrm({
            <?php if ($_smarty_tpl->tpl_vars['link_suffix']->value) {?>
            counterElement: '.counter.crm-interaction<?php echo $_smarty_tpl->tpl_vars['link_suffix']->value;?>
'
            <?php }?>
        });
    });
<?php echo '</script'; ?>
><?php }
}
