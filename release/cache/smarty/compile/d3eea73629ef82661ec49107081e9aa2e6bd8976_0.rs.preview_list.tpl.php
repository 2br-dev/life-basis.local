<?php
/* Smarty version 4.3.1, created on 2025-08-18 15:23:52
  from 'D:\Projects\Hosts\life-basis.local\release\modules\article\view\preview_list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a31b588cbef5_46453528',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd3eea73629ef82661ec49107081e9aa2e6bd8976' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\article\\view\\preview_list.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
    'rs:%THEME%/paginator.tpl' => 1,
    'rs:%THEME%/helper/usertemplate/include/empty_list.tpl' => 1,
  ),
),false)) {
function content_68a31b588cbef5_46453528 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),1=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<h1><?php echo $_smarty_tpl->tpl_vars['dir']->value['title'];?>
</h1>
<?php if ($_smarty_tpl->tpl_vars['list']->value) {?>
    <div class="mt-5">
        <div class="row row-cols-xl-4 row-cols-md-3 row-cols-sm-2 g-md-6 g-3">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['list']->value, 'item');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                <div <?php echo $_smarty_tpl->tpl_vars['item']->value->getDebugAttributes();?>
>
                    <a class="news-card" href="<?php echo $_smarty_tpl->tpl_vars['item']->value->getUrl();?>
">
                        <div class="news-card__img">
                            <canvas width="356" height="200"></canvas>
                            <?php if ($_smarty_tpl->tpl_vars['item']->value['image']) {?>
                                <img src="<?php echo $_smarty_tpl->tpl_vars['item']->value['__image']->getUrl(356,200,'cxy');?>
"
                                     srcset="<?php echo $_smarty_tpl->tpl_vars['item']->value['__image']->getUrl(712,400,'cxy');?>
 2x"
                                     alt="<?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
">
                            <?php } else { ?>
                                <img src="<?php echo $_smarty_tpl->tpl_vars['THEME_IMG']->value;?>
/decorative/news-empty.svg" alt="">
                            <?php }?>
                        </div>
                        <div class="news-card__body">
                            <div class="news-card__date"><?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['item']->value['dateof'],"d.m.Y H:i");?>
</div>
                            <div class="news-card__title"><?php echo $_smarty_tpl->tpl_vars['item']->value['title'];?>
</div>
                        </div>
                    </a>
                </div>
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
    </div>
    <?php $_smarty_tpl->_subTemplateRender("rs:%THEME%/paginator.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
} else { ?>
    <?php ob_start();
$_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();
echo "Не найдено ни одной статьи";
$_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);
$_prefixVariable1=ob_get_clean();
$_smarty_tpl->_subTemplateRender("rs:%THEME%/helper/usertemplate/include/empty_list.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('reason'=>$_prefixVariable1), 0, false);
}
}
}
