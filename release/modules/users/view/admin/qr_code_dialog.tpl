{addjs file="%users%/admin/jquery.qrcode.js"}
<div class="text-center">
    <p  data-dialog-options='{ "width":400, "height":550 }'>Используйте этот QR-код для авторизации на другом устройстве.</p>

    {$auth_key = $current_user->getRemoteAuthorizationKey()}
    {$auth_link = $auth_key->getAuthLink()}
    <a href="{$auth_link}" target="_blank">
        <img src="{$auth_key->getQrCodeImageLink(300, 300, $auth_link)}" alt="{t}QR-код для авторизации{/t}" width="300" class="img-inline-responsive">
    </a>
    {$left_seconds = $auth_key->getExpireLeftSeconds()}
    {$left_time_minutes = sprintf("%02s", floor($left_seconds/60))}
    {$left_time_seconds = sprintf("%02s", $left_seconds % 60)}

    <p data-admin-qr-left-seconds="{$left_seconds}" data-admin-qr-expire-text="{t}Срок действия QR-кода истек. Обновите код.{/t}">{t}Срок действия кода:{/t}
        <span class="admin-qr-left-minutes">{$left_time_minutes}</span>:<span class="admin-qr-left-seconds">{$left_time_seconds}</span></p>

    <a href="{adminUrl mod_controller="users-ctrl" do="myQrCode"}" class="crud-replace-dialog crud-edit btn btn-primary">{t}Обновить{/t}</a>
</div>