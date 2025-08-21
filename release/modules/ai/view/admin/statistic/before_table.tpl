<div class="notice notice-info m-b-20">
    {t}Суммарные значения для всех элементов таблицы.{/t}
    <span class="m-r-15 text-nowrap">{t}Всего запросов:{/t} <strong>{$statistic_tokens.total_requests|format_price}</strong></span>
    <span class="m-r-15 text-nowrap">{t}Всего токенов в запросе:{/t} <strong>{$statistic_tokens.input_tokens_sum|format_price}</strong></span>
    <span class="m-r-15 text-nowrap">{t}Всего токенов в ответе:{/t} <strong>{$statistic_tokens.completion_tokens_sum|format_price}</strong></span>
    <span class="text-nowrap">{t}Всего токенов:{/t} <strong>{$statistic_tokens.total_tokens_sum|format_price}</strong></span>
</div>