{$phrase = {$CONFIG.agreement_personal_data_phrase|default:'Нажимая кнопку "%send" я даю согласие на <a href="%policy_agreement" target="_blank">обработку персональных данных</a>.'|unescape}}
{if $CONFIG.enable_agreement_personal_data == 2}
<div class="policy-agreement policy-agreement-wrapper d-flex gap-3">
    <div class="policy-agreement-checkbox">
        <input type="checkbox" required name="agree_with_data_policy" value="1" {if $smarty.post.agree_with_data_policy}checked{/if}>
    </div>
    <div>
        {t alias = "Кнопка соглашения" send = {$button_title|default:"Отправить"} policy_agreement = $router->getUrl('site-front-policy-agreement')}{$phrase}{/t}
    </div>
</div>
{else}
<p class="policy-agreement">{t alias = "Кнопка соглашения" send = {$button_title|default:"Отправить"} policy_agreement = $router->getUrl('site-front-policy-agreement')}{$phrase}{/t}</p>
{/if}