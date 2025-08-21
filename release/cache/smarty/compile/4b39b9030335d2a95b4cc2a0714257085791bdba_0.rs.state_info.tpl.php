<?php
/* Smarty version 4.3.1, created on 2025-08-18 18:08:50
  from 'D:\Projects\Hosts\life-basis.local\release\modules\antivirus\view\widget\state_info.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a342028a7258_84314657',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4b39b9030335d2a95b4cc2a0714257085791bdba' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\antivirus\\view\\widget\\state_info.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a342028a7258_84314657 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.addcss.php','function'=>'smarty_function_addcss',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),2=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),3=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
echo smarty_function_addcss(array('file'=>((string)$_smarty_tpl->tpl_vars['mod_css']->value)."stateinfo.css",'basepath'=>"root"),$_smarty_tpl);?>

<div class="stateinfo"
     data-refresh-url="<?php echo $_smarty_tpl->tpl_vars['refresh_url']->value;?>
"
     data-intensive="<?php if ($_smarty_tpl->tpl_vars['integrity']->value['is_intensive'] || $_smarty_tpl->tpl_vars['antivirus']->value['is_intensive']) {?>1<?php } else { ?>0<?php }?>">
    <?php if ($_smarty_tpl->tpl_vars['is_cron_work']->value) {?>
        <div class="stateinfo-checksum">
            <?php if ($_smarty_tpl->tpl_vars['integrity']->value['is_intensive']) {?>
                <div class="scan">
                    <div class="progress" style="width:<?php echo $_smarty_tpl->tpl_vars['integrity']->value['progress'];?>
%;"></div>
                    <div class="actions">
                        <a href="<?php echo smarty_function_adminUrl(array('avdo'=>"disableIntegrityIntensiveMode",'mod_controller'=>"antivirus-widget-stateinfo"),$_smarty_tpl);?>
"
                           class="call-update no-update-hash stateinfo-button gray-fill"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Стоп<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                    </div>
                    <div class="info">
                        <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('current'=>$_smarty_tpl->tpl_vars['integrity']->value['global_position'],'total'=>$_smarty_tpl->tpl_vars['integrity']->value['total_files_count']));
$_block_repeat=true;
echo smarty_block_t(array('current'=>$_smarty_tpl->tpl_vars['integrity']->value['global_position'],'total'=>$_smarty_tpl->tpl_vars['integrity']->value['total_files_count']), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?><span class="big-text">Идет полная проверка файлов</span><span class="small-text">Проверено</span> <span>%current из %total</span><?php $_block_repeat=false;
echo smarty_block_t(array('current'=>$_smarty_tpl->tpl_vars['integrity']->value['global_position'],'total'=>$_smarty_tpl->tpl_vars['integrity']->value['total_files_count']), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                    </div>
                </div>
            <?php } else { ?>
                <?php if ($_smarty_tpl->tpl_vars['integrity']->value['unread_event_count']) {?>
                    <!-- Проблема -->
                    <div class="problem">
                        <div class="actions">
                            <a href="<?php echo $_smarty_tpl->tpl_vars['integrity']->value['event_list_url'];?>
" class="report" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>отчет<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                        </div>
                        <div class="problem-info">
                            <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Обнаружено измененных файлов:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo $_smarty_tpl->tpl_vars['integrity']->value['unread_event_count'];?>
</p>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['integrity']->value['event_list_url'];?>
" class="stateinfo-button white-fill"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Подробнее<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                            <a href="<?php echo smarty_function_adminUrl(array('avdo'=>"readIntegrityEvents",'mod_controller'=>"antivirus-widget-stateinfo"),$_smarty_tpl);?>
" class="call-update no-update-hash stateinfo-button white-border"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Скрыть<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                        </div>
                    </div>
                <?php } else { ?>
                    <!-- Информация -->
                    <div class="information">
                        <div class="state">
                            <i class="ok" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Целостность файлов в норме<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></i>
                        </div>
                        <div class="section">
                            <div class="title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Целостность файлов<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
                            <?php if ($_smarty_tpl->tpl_vars['integrity']->value['completed']) {?>
                                <div class="last-cycle">
                                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Последняя проверка<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['integrity']->value['completed'],"d.m.Y H:i");?>

                                </div>
                            <?php }?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo smarty_function_adminUrl(array('avdo'=>"enableIntegrityIntensiveMode",'mod_controller'=>"antivirus-widget-stateinfo"),$_smarty_tpl);?>
" class="run call-update no-update-hash" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Запустить полную проверку<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['integrity']->value['event_list_url'];?>
" class="report" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать отчет<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                        </div>
                    </div>
                <?php }?>
            <?php }?>
        </div>


        <div class="stateinfo-proactive">

            <?php if ($_smarty_tpl->tpl_vars['proactive']->value['unread_event_count']) {?>
                <!-- Проблема -->
                <div class="problem">
                    <div class="actions">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['proactive']->value['event_list_url'];?>
" class="report" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать отчет<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                    </div>
                    <div class="problem-info">
                        <p>
                            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('alias'=>"Зафиксированы атаки",'val'=>$_smarty_tpl->tpl_vars['proactive']->value['unread_event_count'],'ips'=>$_smarty_tpl->tpl_vars['ctrl']->value->getIpCount()));
$_block_repeat=true;
echo smarty_block_t(array('alias'=>"Зафиксированы атаки",'val'=>$_smarty_tpl->tpl_vars['proactive']->value['unread_event_count'],'ips'=>$_smarty_tpl->tpl_vars['ctrl']->value->getIpCount()), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
                                [plural:%val:Зафиксирована|Зафиксировано|Зафиксировано] %val [plural:%val:атака|атаки|атак]
                                c %ipsxIP [plural:%ips:адреса|адресов|адресов]
                            <?php $_block_repeat=false;
echo smarty_block_t(array('alias'=>"Зафиксированы атаки",'val'=>$_smarty_tpl->tpl_vars['proactive']->value['unread_event_count'],'ips'=>$_smarty_tpl->tpl_vars['ctrl']->value->getIpCount()), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                                                    </p>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['proactive']->value['event_list_url'];?>
" class="stateinfo-button white-fill"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Подробнее<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                        <a href="<?php echo smarty_function_adminUrl(array('avdo'=>"readProactiveEvents",'mod_controller'=>"antivirus-widget-stateinfo"),$_smarty_tpl);?>
" class="call-update no-update-hash stateinfo-button white-border"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Скрыть<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                    </div>
                </div>
            <?php } else { ?>
                <!-- Информация -->
                <div class="information">
                    <div class="state">
                        <i class="ok" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Атаки не обнаружены<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></i>
                    </div>
                    <div class="section">
                        <div class="title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Атаки на сайт<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
                    </div>
                    <div class="actions">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['proactive']->value['event_list_url'];?>
" class="report" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать отчет<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                    </div>
                </div>
            <?php }?>

        </div>


        <div class="stateinfo-antivirus">
            <?php if ($_smarty_tpl->tpl_vars['antivirus']->value['unread_event_count']) {?>
                <!-- Проблема -->
                <div class="problem">
                    <div class="actions">
                        <a class="report" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать отчет<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                    </div>
                    <div class="problem-info">
                        <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Обнаружено зараженных файлов:<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo $_smarty_tpl->tpl_vars['antivirus']->value['unread_event_count'];?>
</p>
                        <a href="<?php echo $_smarty_tpl->tpl_vars['antivirus']->value['event_list_url'];?>
" class="stateinfo-button white-fill"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Подробнее<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                        <a href="<?php echo smarty_function_adminUrl(array('avdo'=>"readAntivirusEvents",'mod_controller'=>"antivirus-widget-stateinfo"),$_smarty_tpl);?>
" class="call-update no-update-hash stateinfo-button white-border"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Скрыть<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                    </div>
                </div>
            <?php } else { ?>
                <?php if ($_smarty_tpl->tpl_vars['antivirus']->value['is_intensive']) {?>
                    <!-- Идет проверка -->
                    <div class="scan">
                        <div class="progress" style="width:<?php echo $_smarty_tpl->tpl_vars['antivirus']->value['progress'];?>
%;"></div>
                        <div class="actions">
                            <a href="<?php echo smarty_function_adminUrl(array('avdo'=>"disableAntivirusIntensiveMode",'mod_controller'=>"antivirus-widget-stateinfo"),$_smarty_tpl);?>
"
                               class="call-update no-update-hash stateinfo-button gray-fill"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Стоп<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
                        </div>
                        <div class="info"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('current'=>$_smarty_tpl->tpl_vars['antivirus']->value['global_position'],'total'=>$_smarty_tpl->tpl_vars['antivirus']->value['total_files_count']));
$_block_repeat=true;
echo smarty_block_t(array('current'=>$_smarty_tpl->tpl_vars['antivirus']->value['global_position'],'total'=>$_smarty_tpl->tpl_vars['antivirus']->value['total_files_count']), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?><span class="big-text">Идет полная проверка файлов</span> <span class="small-text">Проверено</span> <span>%current из %total</span><?php $_block_repeat=false;
echo smarty_block_t(array('current'=>$_smarty_tpl->tpl_vars['antivirus']->value['global_position'],'total'=>$_smarty_tpl->tpl_vars['antivirus']->value['total_files_count']), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
                    </div>
                <?php } else { ?>
                    <div class="information">
                        <div class="state">
                            <i class="ok" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Вирусы не обнаружены<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></i>
                        </div>
                        <div class="section">
                            <div class="title"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Вирусы<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></div>
                            <?php if ($_smarty_tpl->tpl_vars['antivirus']->value['completed']) {?>
                                <div class="last-cycle">
                                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Последняя проверка<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['antivirus']->value['completed'],"d.m.Y H:i");?>

                                </div>
                            <?php }?>
                        </div>
                        <div class="actions">
                            <a href="<?php echo smarty_function_adminUrl(array('avdo'=>"enableAntivirusIntensiveMode",'mod_controller'=>"antivirus-widget-stateinfo"),$_smarty_tpl);?>
" class="run call-update no-update-hash" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Запустить полную проверку<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                            <a href="<?php echo $_smarty_tpl->tpl_vars['antivirus']->value['event_list_url'];?>
" class="report" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Показать отчет<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
                        </div>
                    </div>
                <?php }?>
            <?php }?>
        </div>

        <div class="stateinfo-footer">
            <img src="<?php echo $_smarty_tpl->tpl_vars['mod_img']->value;?>
scan.gif" class="protect-img">
            <span class="protect big-text"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>защита включена<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></span>
            <a href="<?php echo smarty_function_adminUrl(array('do'=>"edit",'mod_controller'=>"modcontrol-control",'mod'=>"antivirus"),$_smarty_tpl);?>
" class="settings" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки модуля<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
            <a href="<?php echo $_smarty_tpl->tpl_vars['excluded_list_url']->value;?>
" class="trustzone" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Исключения<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></a>
        </div>

    <?php } else { ?>
        <!-- Есть ошибка -->
        <div class="trouble">
            <p><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Не зафиксирован запуск фонового модуля антивируса. Настройте запуск внутреннего планировщика cron.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></p>
            <a href="http://readyscript.ru/manual/cron.html" class="stateinfo-button white-fill"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Подробнее<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
        </div>
    <?php }?>
</div><?php }
}
