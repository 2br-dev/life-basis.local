<div class="link-last-objects m-t-30">
    <h3>{t}Недавние результаты форм обратной связи{/t}</h3>
    <p>{t}Нажмите на одну из представленных ниже результатов форм обратной связи, чтобы установить с ней связь.{/t}</p>
    <ul class="list-unstyled" style="columns:2;">
        {foreach $last_objects as $item}
            {static_call var=forms callback=['\Feedback\Model\FormApi','staticSelectList']}
            {$statuses = $item.__status->getList()}

            <li data-id="{$item.id}" class="m-b-10">
                <a class="link-last-this">
                    <span class="link-last-title">{t title={$item.title|default:$item.id} date={$item.dateof|dateformat:"@date"}}%title от %date{/t}</span>
                </a>
                <div>
                    <small class="c-gray">{t status=$statuses[$item.status]}Статус: %status{/t}</small>
                </div>
                <div>
                    <small class="c-gray">{t form=$forms[$item.form_id]}Форма: %form{/t}</small>
                </div>
            </li>
        {/foreach}
    </ul>
</div>