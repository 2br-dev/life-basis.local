<li class="offcanvas__lk">
    {if $is_auth}
        <a href="{$router->getUrl('users-front-profile')}" class="offcanvas__lk-item{if $users_config.auth_by_key_enable} with-qr{/if}">
            <svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M18.364 13.9275C17.3671 12.9306 16.1774 12.1969 14.8791 11.76C16.0764 10.8752 16.8543 9.45393 16.8543 7.85428C16.8543 5.17762 14.6767 3 12 3C9.32332 3 7.1457 5.17762 7.1457 7.85428C7.1457 9.45396 7.92364 10.8752 9.12089 11.76C7.82264 12.1968 6.63295 12.9306 5.63602 13.9275C3.93618 15.6274 3 17.8875 3 20.2915C3 20.6828 3.31722 21 3.70854 21H20.2915C20.6828 21 21 20.6828 21 20.2915C21 17.8875 20.0639 15.6274 18.364 13.9275ZM8.56285 7.85428C8.56285 5.959 10.1047 4.41712 12.0001 4.41712C13.8954 4.41712 15.4373 5.959 15.4373 7.85428C15.4373 9.74956 13.8954 11.2914 12.0001 11.2914C10.1047 11.2915 8.56285 9.74956 8.56285 7.85428ZM4.44995 19.5829C4.80834 15.7326 8.05769 12.7086 12 12.7086C15.9423 12.7086 19.1917 15.7326 19.5501 19.5829H4.44995Z"/>
            </svg>
            <span class="ms-2">{$current_user.name} {$current_user.surname}</span>
        </a>
        {if $users_config.auth_by_key_enable}
            <div class="ps-3 pe-3">
                <a href="{$router->getUrl('users-front-profile', ['Act' => 'qrCode'])}" class="rs-in-dialog" title="{t}Войти на другом устройстве{/t}">
                    <svg height="24" width="24" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 28v-2h2v2zM18 24v-2h2v2zM18 30h4v-2h-2v-2h-2v4zM26 26v-4h2v4zM28 26h2v4h-4v-2h2v-2zM26 20v-2h4v4h-2v-2h-2zM24 20h-2v4h-2v2h4v-6zM18 20v-2h4v2zM6 22h4v4H6z"/>
                        <path d="M14 30H2V18h12zM4 28h8v-8H4zM22 6h4v4h-4z"/>
                        <path d="M30 14H18V2h12zm-10-2h8V4h-8zM6 6h4v4H6z"/>
                        <path d="M14 14H2V2h12zM4 12h8V4H4z"/>
                        <path fill="none" d="M0 0h32v32H0z"/>
                    </svg>
                </a>
            </div>
        {/if}
    {else}
        {$referer = urlencode($url->server('REQUEST_URI'))}
        <a data-href="{$authorization_url}" class="rs-in-dialog offcanvas__lk-item">{t}Вход{/t}</a>
        <a data-href="{$router->getUrl('users-front-register', ['referer' => $referer])}" class="rs-in-dialog offcanvas__lk-item">{t}Регистрация{/t}</a>
    {/if}
</li>