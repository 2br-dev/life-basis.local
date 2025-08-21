{addcss file="%crm%/checklist.css"}
{addjs file="%crm%/jquery.rs.checklist.js?v=1"}

{addcss file="%catalog%/selectproduct.css" basepath="root"}
{addjs file="%catalog%/selectproduct.js" basepath="root"}

{$checklist_can_edit = $elem->canChecklistUpdate()}
<div
        class="checklist-wrapper hide-group-cb hide-product-cb"
        id="checklistContainer"
        data-checklist-can-edit = '{if $checklist_can_edit}true{else}false{/if}'
        data-product-url='{$router->getAdminUrl('', ['do' => 'edit'], 'catalog-ctrl')}'
        data-urls='{
        "getChild": "{adminUrl mod_controller="catalog-dialog" do="getChildCategory"}",
        "getProducts": "{adminUrl mod_controller="catalog-dialog" do="getProducts"}",
        "getDialog": "{adminUrl mod_controller="catalog-dialog" do=false}"
    }'
>
    <div class="checklist-progress">
        <div class="progress-label">0%</div>
        <div class="progress-bar-background">
            <div class="progress-bar-fill"></div>
        </div>
    </div>

    {foreach $elem.checklist as $g_index => $group}
        <div class="checklist-group" data-editing="false" data-uniq="{$g_index}">
            <div class="checklist-header">
                <span class="group-title">
                    {$group.title} <i class="zmdi zmdi-edit edit-icon"></i>
                </span>
                <button type="button" class="remove-btn remove-group-btn" title="Удалить группу">
                    <i class="zmdi zmdi-close"></i>
                </button>
            </div>

            <div class="checklist-items ui-sortable">
                {foreach $group.items as $i_index => $item}
                    <div class="checklist-item"
                         data-editing="false"
                         data-uniq="{$i_index}"
                         {if $item.entity_type == 'product'}data-has-product="true"{/if}
                            {if $item.type}data-type="{$item.type}"{/if}
                            {if $item.entity_type}data-entity-type="{$item.entity_type}"{/if}
                            {if $item.entity_id}data-entity-id="{$item.entity_id}"{/if}
                    >
                        <i class="zmdi zmdi-unfold-more sort-handle"></i>
                        <input type="checkbox" {if $item.is_done}checked{/if}>
                        <span class="item-label">
                            {if $item.entity_type == 'product'}
                                <a
                                    href="{$router->getAdminUrl('', ['do' => 'edit', 'id' => {$item.entity_id}], 'catalog-ctrl')}"
                                    class="crud-edit"
                                    data-crud-dialog-width="90%"
                                    data-crud-dialog-height="90%"
                                >{$item.title}</a>
                                <button type="button" class="remove-btn remove-item-btn" title="Удалить товар">
                                    <i class="zmdi zmdi-close"></i>
                                </button>
                            {else}
                                {$item.title|default:"Новая задача"} <i class="zmdi zmdi-edit edit-icon"></i>
                            {/if}
                        </span>
                        <a class="btn btn-success select-product-btn">
                            <i class="zmdi zmdi-plus"></i>
                            <span>Указать товар</span>
                        </a>
                        <button type="button" class="remove-btn remove-item-btn" title="Удалить пункт">
                            <i class="zmdi zmdi-close"></i>
                        </button>
                    </div>
                {/foreach}
            </div>

            {if $checklist_can_edit}
                <a href="#" class="add-item-btn">+ добавить пункт</a>
            {/if}
        </div>
    {/foreach}
</div>

{if $checklist_can_edit}
    <a class="button m-t-10" id="addGroupBtn">Добавить чек-лист</a>
{/if}

<script>
    $.allReady(function () {
        $('#checklistContainer').checklists().selectProduct();
    });
</script>

