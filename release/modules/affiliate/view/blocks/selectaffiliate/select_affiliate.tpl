{* Отображает текущий филиал в шапке. Позволяет открыть диалоговое окно выбора филиала. *}
{addjs file="%affiliate%/rscomponent/affiliate.js"}

<a class="head-bar__link rs-in-dialog"
   data-href="{$router->getUrl('affiliate-front-affiliates', ['referer' => $referrer])}"
    {if $current_affiliate.id}{$current_affiliate->getDebugAttributes()}{/if}>
    {include "%THEME%/helper/svg/geopoint.tpl"}
    <span class="ms-2">{$current_affiliate.title|default:"{t}Выбрать город{/t}"}</span>
</a>

{if $need_recheck}
    <!-- Окно подтверждения города -->
    <template id="affiliate-confirm-template">
        <div class="affilliate-confirm">
            <div class="container">
                <div class="affilliate-confirm__inner">
                    <button type="button" class="btn-close modal-close" aria-label="Close">
                        <img src="{$THEME_IMG}/icons/close.svg" width="24" height="24" alt="">
                    </button>
                    <div class="row g-3 align-items-center">
                        <div class="col affilliate-confirm__quest">
                            {t city={$current_affiliate.title|default:"Неизвестно"}}Ваше местоположение: %city?{/t}
                        </div>
                        <div class="col-sm-auto">
                            <div class="d-flex">
                                <button type="button" class="btn btn-primary w-100 modal-close">{t}Да{/t}</button>
                                <a data-href="{$router->getUrl('affiliate-front-affiliates', ['referer' => $referrer])}"
                                   class="rs-in-dialog no-wrap ms-2 btn btn-outline-primary w-100 modal-close ">{t}Выбрать другое{/t}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
{/if}