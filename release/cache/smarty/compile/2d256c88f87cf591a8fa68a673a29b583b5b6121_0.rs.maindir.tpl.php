<?php
/* Smarty version 4.3.1, created on 2025-08-20 11:46:41
  from 'D:\Projects\Hosts\life-basis.local\release\modules\catalog\view\form\product\maindir.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_68a58b71875a91_37258765',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2d256c88f87cf591a8fa68a673a29b583b5b6121' => 
    array (
      0 => 'D:\\Projects\\Hosts\\life-basis.local\\release\\modules\\catalog\\view\\form\\product\\maindir.tpl',
      1 => 1754647726,
      2 => 'rs',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_68a58b71875a91_37258765 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'D:\\Projects\\Hosts\\life-basis.local\\release\\core\\system\\smarty\\rsplugins\\block.t.php','function'=>'smarty_block_t',),));
?>
<select name="maindir" id="maindir" data-selected="<?php echo $_smarty_tpl->tpl_vars['elem']->value['maindir'];?>
">
    <option value="">-- <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>не выбрано<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> --</option>
</select>

<?php echo '<script'; ?>
>
$(".tree-select[data-form-name='xdir[]']").on('treeSelectChange', onDirChange);

function onDirChange(e, firstRun )
{
    var xdir = $(".tree-select[data-form-name='xdir[]']");
    var maindir = $('#maindir');
    
    maindir.html('');
    var selected = $(".tree-select_selected-value-item", xdir);
    if (selected.length == 0) {
        maindir.append('<option value="">-- <?php $_smarty_tpl->smarty->_cache['_tag_stack'][] = array('t', array());
$_block_repeat=true;
echo smarty_block_t(array(), null, $_smarty_tpl, $_block_repeat);
while ($_block_repeat) {
ob_start();?>не выбрано<?php $_block_repeat=false;
echo smarty_block_t(array(), ob_get_clean(), $_smarty_tpl, $_block_repeat);
}
array_pop($_smarty_tpl->smarty->_cache['_tag_stack']);?> --</option>');
    }
    selected.each(function() {
        let cur = $(this);
        let fulloption = '';
        $('.tree-select_selected-value-item_title-path-part', cur).each(function () {
            fulloption += $(this).html() + ' > ';
        });
        fulloption += $('.tree-select_selected-value-item_title-end-part', cur).html();

        maindir.append('<option value="'+cur.data('id')+'">' + fulloption + '</option>');
    });
    var main_selected = (firstRun) ?  maindir.attr('data-selected') : $('#maindir option:first').val();
    maindir.val(main_selected);
}

onDirChange(null, true);

<?php echo '</script'; ?>
><?php }
}
