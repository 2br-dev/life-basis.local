<ion-header>
  <ion-toolbar>
    <div class="container">
      <div class="head">
        <div class="head__expand">
          <button class="head__button" type="button" (click)="navigateBack()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1862 19.7188C16.6046 19.3439 16.6046 18.7361 16.1862 18.3612L9.08666 12L16.1862 5.63882C16.6046 5.26392 16.6046 4.65608 16.1862 4.28118C15.7678 3.90627 15.0894 3.90627 14.671 4.28118L6.81381 11.3212C6.3954 11.6961 6.3954 12.3039 6.81381 12.6788L14.671 19.7188C15.0894 20.0937 15.7678 20.0937 16.1862 19.7188Z" fill="#1B1B1F"/>
            </svg>
          </button>
        </div>
        <div class="head__title">{t}Ожидание статуса{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div id="status" class="content_wrapper">
    <div class="global-section status-wrapper">
      <div class="container">
        <ion-grid>
          <ion-row class="ion-align-items-center" [class]="status">
            <ion-col size="12" class="status-wait">
              <div class="ion-text-center margin-32-top-mob margin-24-left-table">
                <div class="ion-text-center">
                  <svg width='128px' height='128px' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="uil-balls">
                    <rect x="0" y="0" width="100" height="100" fill="none" class="bk"></rect>
                    <g transform="rotate(0 50 50)">
                      <circle r="5" cx="30" cy="50">
                        <animateTransform attributeName="transform" type="translate" begin="0s" repeatCount="indefinite" dur="1s" values="0 0;19.999999999999996 -20" keyTimes="0;1"/>
                        <animate attributeName="fill" dur="1s" begin="0s" repeatCount="indefinite"  keyTimes="0;1" values="#fff;#999"/>
                      </circle>
                    </g>
                    <g transform="rotate(90 50 50)">
                      <circle r="5" cx="30" cy="50">
                        <animateTransform attributeName="transform" type="translate" begin="0s" repeatCount="indefinite" dur="1s" values="0 0;19.999999999999996 -20" keyTimes="0;1"/>
                        <animate attributeName="fill" dur="1s" begin="0s" repeatCount="indefinite"  keyTimes="0;1" values="#999;#000"/>
                      </circle>
                    </g>
                    <g transform="rotate(180 50 50)">
                      <circle r="5" cx="30" cy="50">
                        <animateTransform attributeName="transform" type="translate" begin="0s" repeatCount="indefinite" dur="1s" values="0 0;19.999999999999996 -20" keyTimes="0;1"/>
                        <animate attributeName="fill" dur="1s" begin="0s" repeatCount="indefinite"  keyTimes="0;1" values="#000;#fff"/>
                      </circle>
                    </g>
                    <g transform="rotate(270 50 50)">
                      <circle r="5" cx="30" cy="50">
                        <animateTransform attributeName="transform" type="translate" begin="0s" repeatCount="indefinite" dur="1s" values="0 0;19.999999999999996 -20" keyTimes="0;1"/>
                        <animate attributeName="fill" dur="1s" begin="0s" repeatCount="indefinite"  keyTimes="0;1" values="#fff;#999"/>
                      </circle>
                    </g>
                  </svg>
                </div>
                <div class="c-dark margin-24-bottom">
                  <p>{t}Ожидание получения статуса оплаты{/t}</p>
                </div>
                <button class="button button_primary button_medium" type="button" (click)="navigateTo('/tabs/catalog')">{t}Перейти в каталог{/t}</button>
              </div>
            </ion-col>
            <ion-col size="12" class="status-fail">
              <div class="ion-text-center">
                <svg width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <g clip-path="url(#clip0)">
                    <path d="M62.7436 46.8423L41.7547 6.90486C39.8443 3.26981 36.1064 1.01172 31.9999 1.01172C27.8934 1.01172 24.1558 3.26981 22.2452 6.90486L1.25646 46.8423C-0.527833 50.2375 -0.404093 54.3853 1.57895 57.6662C3.56157 60.9488 7.17588 62.9883 11.0112 62.9883H52.9891C56.8238 62.9883 60.4381 60.949 62.4209 57.667C64.404 54.3853 64.5277 50.2375 62.7436 46.8423ZM56.9332 54.3515C56.1038 55.7244 54.5924 56.5771 52.9889 56.5771H11.011C9.40704 56.5771 7.89588 55.7244 7.06646 54.3509C6.23725 52.979 6.18575 51.2448 6.93182 49.8251L27.9208 9.88722C28.7194 8.36729 30.2827 7.4229 31.9999 7.4229C33.7171 7.4229 35.2804 8.36708 36.0791 9.88722L57.068 49.8251C57.8141 51.2448 57.7626 52.979 56.9332 54.3515Z" fill="#FF2F2F"/>
                    <path d="M30.7831 43.0467H33.2177L35.5933 18.6543H28.4072L30.7831 43.0467Z" fill="#FF2F2F"/>
                    <path d="M31.9994 45.3115C30.0803 45.3115 28.5264 46.8656 28.5264 48.7848C28.5264 50.702 30.0803 52.2578 31.9994 52.2578C33.9184 52.2578 35.4725 50.702 35.4725 48.7848C35.4725 46.8659 33.9186 45.3115 31.9994 45.3115Z" fill="#FF2F2F"/>
                  </g>
                  <defs>
                    <clipPath id="clip0">
                      <rect width="64" height="64" fill="white"/>
                    </clipPath>
                  </defs>
                </svg>
              </div>
              <div class="error-block margin-24-bottom margin-24-top">
                <p>{t}Произошла ошибка при оплате заказа.{/t}</p>
                <p>{t}Оплатить заказ можно{/t} <a (click)="navigateTo('/tabs/user/account/orders')">{t}в личном кабинете{/t}</a>.</p>
              </div>
            </ion-col>
            <ion-col size="12" class="status-success">
              <div class="ion-text-center margin-32-top-mob margin-24-left-table">
                <div class="ion-text-center">
                  <svg width="160" height="160" viewBox="0 0 160 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="80" cy="80" r="80" fill="#F7F7F7"/>
                    <g clip-path="url(#clip0)">
                      <path d="M69.4626 113.621C68.6035 114.485 67.4314 114.968 66.2139 114.968C64.9963 114.968 63.8242 114.485 62.9651 113.621L37.0194 87.6715C34.3269 84.9789 34.3269 80.6127 37.0194 77.9252L40.2682 74.6755C42.9617 71.983 47.3228 71.983 50.0154 74.6755L66.2139 90.8748L109.984 47.1034C112.678 44.4108 117.043 44.4108 119.732 47.1034L122.98 50.353C125.673 53.0456 125.673 57.411 122.98 60.0994L69.4626 113.621Z" fill="#49B34C"/>
                    </g>
                    <defs>
                      <clipPath id="clip0">
                        <rect width="90" height="90" fill="white" transform="translate(35 35)"/>
                      </clipPath>
                    </defs>
                  </svg>
                </div>
                <div class="c-dark margin-24-bottom">
                  <p>{t}Оплата прошла успешно! Следить за статусом выполнения заказа вы можете в личном кабинете{/t}</p>
                </div>
                <button class="button button_primary button_medium" type="button" (click)="navigateTo('/tabs/catalog')">{t}Перейти в каталог{/t}</button>
              </div>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>
