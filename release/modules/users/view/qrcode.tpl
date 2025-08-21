{extends "%THEME%/helper/wrapper/dialog/standard.tpl"}

{block "title"}{t}Мой QR-код{/t}{/block}
{block "body"}
    {t}Используйте этот QR-код для авторизации на другом устройстве.{/t}
    <div class="text-center">
        {$auth_key = $current_user->getRemoteAuthorizationKey()}
        {$auth_link = $auth_key->getAuthLink()}
        <a href="{$auth_link}" target="_blank">
            <img src="{$auth_key->getQrCodeImageLink(300, 300, $auth_link)}" alt="{t}QR-код для авторизации{/t}" width="300" height="300">
        </a>
        {$left_seconds = $auth_key->getExpireLeftSeconds()}
        {$left_time_minutes = sprintf("%02s", floor($left_seconds/60))}
        {$left_time_seconds = sprintf("%02s", $left_seconds % 60)}

        <p data-qr-left-seconds="{$left_seconds}" data-qr-expire-text="{t}Срок действия QR-кода истек. Обновите код.{/t}">{t}Срок действия кода:{/t}
            <span class="qr-left-minutes">{$left_time_minutes}</span>:<span class="qr-left-seconds">{$left_time_seconds}</span></p>

        <a href="{$router->getUrl('users-front-profile', ['Act' => 'qrCode'])}" class="{if $url->isAjax()}rs-in-dialog{/if} btn btn-primary">{t}Обновить{/t}</a>
    </div>
{/block}