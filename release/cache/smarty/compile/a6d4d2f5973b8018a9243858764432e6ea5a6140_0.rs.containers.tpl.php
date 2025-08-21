<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:09:40
  from 'D:\Projects\Hosts\life-basis.local\release\modules\templates\view\gs\containers.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31804d22859_45469933',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a6d4d2f5973b8018a9243858764432e6ea5a6140' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\templates\\view\\gs\\containers.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a31804d22859_45469933 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\function.adminUrl.php','function'=>'smarty_function_adminUrl',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
if (!empty($_smarty_tpl->tpl_vars['currentPage']->value['template'])) {?>
    <div class="pageview-text">
        <?php ob_start();
echo smarty_function_adminUrl(array('do'=>"editPage",'id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id']),$_smarty_tpl);
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('tpl'=>$_smarty_tpl->tpl_vars['currentPage']->value['template'],'link'=>$_prefixVariable1,'alias'=>"Для текущей страницы задан шаблон.."));
$_block_repeat=true;
echo smarty_block_t(array('tpl'=>$_smarty_tpl->tpl_vars['currentPage']->value['template'],'link'=>$_prefixVariable1,'alias'=>"Для текущей страницы задан шаблон.."), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Для <a href="%link" class="crud-edit uline">текущей страницы</a> задан шаблон `<strong>%tpl</strong>`. Сборка элементов по сетке в этом случае невозможна.
        Все необходимые для данной странице модули должны быть указаны вручную в данном шаблоне.<?php $_block_repeat=false;
echo smarty_block_t(array('tpl'=>$_smarty_tpl->tpl_vars['currentPage']->value['template'],'link'=>$_prefixVariable1,'alias'=>"Для текущей страницы задан шаблон.."), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
    </div>
<?php } else { ?>
    <?php if ($_smarty_tpl->tpl_vars['grid_system']->value == 'none') {?>
        <div class="pageview-text">
            <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Тема оформления не использует сетку. Все необходимые для страницы модули должны быть указаны вручную в шаблоне, установленном в <a class="crud-edit uline" href="<?php echo smarty_function_adminUrl(array('do'=>"editPage",'id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id']),$_smarty_tpl);?>
">настройках страницы</a>.<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
        </div>
    <?php } else { ?>
        <?php $_smarty_tpl->_assignInScope('containers', $_smarty_tpl->tpl_vars['currentPage']->value->getContainers());?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['containers']->value, 'container', true);
$_smarty_tpl->tpl_vars['container']->iteration = 0;
$_smarty_tpl->tpl_vars['container']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['container']->value) {
$_smarty_tpl->tpl_vars['container']->do_else = false;
$_smarty_tpl->tpl_vars['container']->iteration++;
$_smarty_tpl->tpl_vars['container']->last = $_smarty_tpl->tpl_vars['container']->iteration === $_smarty_tpl->tpl_vars['container']->total;
$__foreach_container_3_saved = $_smarty_tpl->tpl_vars['container'];
?>
        <?php if (!$_smarty_tpl->tpl_vars['container']->value['object']) {?>
            <div class="inherit">
                <?php if (empty($_smarty_tpl->tpl_vars['defaultPage']->value['template']) && $_smarty_tpl->tpl_vars['container']->value['defaultObject']) {?>
                    <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('container'=>$_smarty_tpl->tpl_vars['container']->value['defaultObject']->getTitle()));
$_block_repeat=true;
echo smarty_block_t(array('container'=>$_smarty_tpl->tpl_vars['container']->value['defaultObject']->getTitle()), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Контейнер "%container" используется со страницы по умолчанию.<?php $_block_repeat=false;
echo smarty_block_t(array('container'=>$_smarty_tpl->tpl_vars['container']->value['defaultObject']->getTitle()), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                    <?php ob_start();
echo smarty_function_adminUrl(array('do'=>"addContainer",'page_id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id'],'type'=>$_smarty_tpl->tpl_vars['container']->value['type'],'context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);
$_prefixVariable2 = ob_get_clean();
ob_start();
echo smarty_function_adminUrl(array('do'=>"copyContainer",'page_id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id'],'type'=>$_smarty_tpl->tpl_vars['container']->value['type'],'context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);
$_prefixVariable3 = ob_get_clean();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('alias'=>"Если Вы хотите придать другой вид этой части страницы..",'link'=>$_prefixVariable2,'data_url'=>$_prefixVariable3));
$_block_repeat=true;
echo smarty_block_t(array('alias'=>"Если Вы хотите придать другой вид этой части страницы..",'link'=>$_prefixVariable2,'data_url'=>$_prefixVariable3), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>
                    Если Вы хотите придать другой вид этой части страницы, <a class="crud-add make-container" href="%link">создайте новый контейнер</a> или
                    <a data-url="%data_url" class="crud-add make-container">скопируйте контейнер</a>, чтобы затем изменить его.<?php $_block_repeat=false;
echo smarty_block_t(array('alias'=>"Если Вы хотите придать другой вид этой части страницы..",'link'=>$_prefixVariable2,'data_url'=>$_prefixVariable3), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                <?php } else { ?>
                    <?php ob_start();
echo smarty_function_adminUrl(array('do'=>"addContainer",'page_id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id'],'type'=>$_smarty_tpl->tpl_vars['container']->value['type'],'context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);
$_prefixVariable4 = ob_get_clean();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array('link'=>$_prefixVariable4,'alias'=>"Контейнер будет исключен для данной страницы.."));
$_block_repeat=true;
echo smarty_block_t(array('link'=>$_prefixVariable4,'alias'=>"Контейнер будет исключен для данной страницы.."), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Контейнер будет исключен для данной сраницы.
                    Если Вы хотите его использовать, <a class="crud-add make-container" href="%link">создайте контейнер</a>.<?php $_block_repeat=false;
echo smarty_block_t(array('link'=>$_prefixVariable4,'alias'=>"Контейнер будет исключен для данной страницы.."), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>
                <?php }?>
            </div>
        <?php } else { ?>
            <div class="<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_35659659068a31804cb2813_35891821', "container_class");
?>
 gs-manager <?php if ($_COOKIE["page-constructor-disabled-".((string)$_smarty_tpl->tpl_vars['container']->value['object']['id'])]) {?>grid-disabled<?php }?> <?php if ($_COOKIE["page-visible-disabled-".((string)$_smarty_tpl->tpl_vars['container']->value['object']['id'])]) {?>visible-disabled<?php }?>" data-container-id="<?php echo $_smarty_tpl->tpl_vars['container']->value['object']['id'];?>
" data-section-id="-<?php echo $_smarty_tpl->tpl_vars['container']->value['object']['type'];?>
">
                <div class="commontools">
                    <?php echo $_smarty_tpl->tpl_vars['container']->value['object']->getTitle();?>


                    <div class="container-tools">
                        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_177473768768a31804cd4f65_24024965', "container_tools");
?>

                        <a class="isettings itool crud-edit" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Настройки<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" href="<?php echo smarty_function_adminUrl(array('do'=>'editContainer','id'=>$_smarty_tpl->tpl_vars['container']->value['object']['id'],'page_id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id'],'type'=>$_smarty_tpl->tpl_vars['container']->value['object']['type']),$_smarty_tpl);?>
">
                            <i class="zmdi zmdi-settings"><!----></i>

                        </a>
                        <?php if ($_smarty_tpl->tpl_vars['currentPage']->value['route_id'] != 'default' || $_smarty_tpl->tpl_vars['container']->last) {?>
                            <a class="iremove itool crud-remove-one" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить контейнер<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>" href="<?php echo smarty_function_adminUrl(array('do'=>'removeContainer','id'=>$_smarty_tpl->tpl_vars['container']->value['object']['id']),$_smarty_tpl);?>
">
                                <i class="zmdi zmdi-delete"><!----></i>
                            </a>
                        <?php }?>
                    </div>

                    <div class="drag-handler"></div>
                    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_212496029668a31804cea644_98978301', "container_switchers");
?>

                    <div class="zmdi grid-switcher<?php if ($_COOKIE["page-constructor-disabled-".((string)$_smarty_tpl->tpl_vars['container']->value['object']['id'])]) {?> off<?php }?>" title="<?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Включить/Выключить сетку<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?>"></div>
                </div>
                <div class="workarea sort-sections <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_118322042768a31804cf5528_52140592', "container_workarea_class");
?>
"> <!-- Рабочая область контейнера -->
                        <?php $_smarty_tpl->_subTemplateRender($_smarty_tpl->tpl_vars['section_tpl']->value, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('item'=>$_smarty_tpl->tpl_vars['container']->value['object']->getSections()), 0, true);
?>
                </div> <!-- Конец рабочей области контейнера -->
            </div>
        <?php }?>        
        <div class="gs-sep"></div>
        <?php
$_smarty_tpl->tpl_vars['container'] = $__foreach_container_3_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <br>
        <div class="bottom-container-tools">
            <a class="crud-add make-container btn btn-success" href="<?php echo smarty_function_adminUrl(array('do'=>"addContainer",'page_id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id'],'type'=>$_smarty_tpl->tpl_vars['currentPage']->value->max_container_type+1),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>добавить контейнер<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
            <a class="crud-add make-container btn btn-default" data-url="<?php echo smarty_function_adminUrl(array('do'=>"copyContainer",'page_id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id'],'type'=>$_smarty_tpl->tpl_vars['currentPage']->value->max_container_type+1,'context'=>$_smarty_tpl->tpl_vars['context']->value),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Добавить контейнер клонированием<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
            <?php if (count($_smarty_tpl->tpl_vars['containers']->value)) {?>
                <a class="crud-remove-one btn btn-danger" href="<?php echo smarty_function_adminUrl(array('do'=>'removeLastContainer','page_id'=>$_smarty_tpl->tpl_vars['currentPage']->value['id']),$_smarty_tpl);?>
"><?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>Удалить нижний контейнер<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?></a>
            <?php }?>
        </div>
    <?php }
}
}
/* {block "container_class"} */
class Block_35659659068a31804cb2813_35891821 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'container_class' => 
  array (
    0 => 'Block_35659659068a31804cb2813_35891821',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block "container_class"} */
/* {block "container_tools"} */
class Block_177473768768a31804cd4f65_24024965 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'container_tools' => 
  array (
    0 => 'Block_177473768768a31804cd4f65_24024965',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block "container_tools"} */
/* {block "container_switchers"} */
class Block_212496029668a31804cea644_98978301 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'container_switchers' => 
  array (
    0 => 'Block_212496029668a31804cea644_98978301',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block "container_switchers"} */
/* {block "container_workarea_class"} */
class Block_118322042768a31804cf5528_52140592 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'container_workarea_class' => 
  array (
    0 => 'Block_118322042768a31804cf5528_52140592',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block "container_workarea_class"} */
}
