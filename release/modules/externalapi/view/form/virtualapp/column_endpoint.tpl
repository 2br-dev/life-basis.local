{if $cell->getRow()->use_vapp_endpoint}
    {t}Адрес API{/t}: <strong>{$router->getUrl('externalapi-front-apigate', ['api_key' => $cell->getRow()->vapp_endpoint_api_key], true)}</strong>
    {if $cell->getRow()->vapp_endpoint_enable_api_help}
        <br>{t}Справка{/t}: <strong>{$router->getUrl('externalapi-front-apigate-help', ['api_key' => $cell->getRow()->vapp_endpoint_api_key], true)}</strong>
    {/if}
{else}
    {$config = ConfigLoader::byModule('externalapi')}
    {t}Адрес API{/t}: <strong>{$router->getUrl('externalapi-front-apigate', ['api_key' => $config['api_key']], true)}</strong>
    {if $cell->getRow()->enable_api_help}
        <br>{t}Справка{/t}: <strong>{$router->getUrl('externalapi-front-apigate-help', ['api_key' => $config['api_key']], true)}</strong>
    {/if}
{/if}