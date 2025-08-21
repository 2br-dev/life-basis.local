<div data-scan-root
     {if $scan_request.id}
     data-refresh-url="{adminUrl do="refresh" id=$scan_request.id}"
     data-status="{$scan_request.status}"
     {/if}>
    <div data-status="wait" {if $scan_request.status != 'waiting'}class="hidden"{/if}>
        <p>{t}На ваше устройство с приложением <a href="{$Setup.RS_SERVER_PROTOCOL}://{$Setup.RS_SERVER_DOMAIN}/mobile-manager-app/" target="_blank">ReadyScript <i class="zmdi zmdi-open-in-new"></i></a> отправлено Push-уведомление.{/t}</p>
        <p>{t}Нажмите на Push-уведомление и отсканируйте необходимый код. Не закрывайте данное окно до завершения сканирования.{/t}</p>

        <div class="d-flex m-t-30" style="justify-content: center;
                                          align-items:center;
                                          gap:16px;
                                          border:1px solid #ddd;
                                          border-width:1px 0;
                                          padding:20px;">
            <img src="{$Setup.IMG_PATH}/adminstyle/ajax-loader.gif" width="31" height="31" alt="">
            <span>{t}Ожидание сканирования...{/t}</span>
        </div>
    </div>
    <div data-status="fail" {if $scan_request.status != 'fail'}class="hidden"{/if}>
        <div class="notice notice-danger">{$scan_request.fail_reason}</div>
    </div>
    {if $scan_request.id}
        <div class="text-center m-t-30">
            <a class="btn btn-default scan-resend" data-resend-url="{adminUrl do="resend" id=$scan_request.id}"
               data-sending-text="{t}Идет отправка...{/t}">{t}Отправить заново{/t}</a>
        </div>
    {/if}
</div>