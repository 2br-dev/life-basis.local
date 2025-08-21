<p>{t}Вы можете использовать следующий QR-код в качестве единого QR-кода для установки приложения на Android и iOS.
    В случае, если для операционной системы устройства, с которого происходит сканирование QR-кода,
        не будет подходящего мобильного приложения - произойдет переход на главную страницу вашего сайта.{/t}</p>
<div class="text-center">
    <div class="m-b-10">
        <img src="{$qr_img_url}" alt="">
    </div>
    <div class="m-b-10">
        <a href="{$app_install_url}" target="_blank">{$app_install_url}</a>
    </div>
    <div>
        <a href="{$qr_big_img_url}" target="_blank" class="btn btn-warning">{t}Открыть QR-код в новой вкладке{/t}</a>
    </div>
</div>