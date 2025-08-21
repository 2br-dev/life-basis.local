{addjs file="%shop%/jquery.lastorderslink.js"}

<div class="link-orders-block" data-dialog-options='{ "width":600, "height":720 }'>
    {$form_object->getForm()}
    <div class="links-select-order">
        <h3>{t}Недавние заказы{/t}</h3>
        <p>{t}Нажмите на один из представленных ниже заказов, чтобы установить с ним связь.{/t}</p>
        <ul class="list-unstyled" style="columns:2;">
            {foreach $last_orders as $item}
                {$status = $item->getStatus()}
                <li data-id="{$item.id}" class="m-b-10">
                    <a class="link-last-this"><span class="orderStatusColor" style="background-color:{$status->bgcolor}" title="{$status->title}"></span>
                        <span class="link-last-title">{t num={$item.order_num} date={$item.dateof|dateformat:"@date"}}Заказ №%num от %date{/t}</span></a>
                    <div>
                        {if $item->getUser()->is_company}
                            {$buyer = $item->getUser()->getFio()}
                        {else}
                            {$buyer = $item->getUser()->company}
                        {/if}
                        <small class="c-gray">{t sum={$item.totalcost|format_price} user=$buyer curr=$item.currency_stitle}%sum %curr %user{/t}</small>
                    </div>
                </li>
            {/foreach}
        </ul>
    </div>
</div>