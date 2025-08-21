<ion-content [fullscreen]="true">
  <div class="global-section">
    <div class="container">
      <ion-grid>
        <ion-row class="ion-align-items-center">
          <ion-col size-md="6" size="12">
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
                <p>{t}Способ оплаты выбран! Следить за статусом выполнения заказа вы можете в личном кабинете{/t}</p>
              </div>
              <button class="button button_primary button_medium" type="button" (click)="navigateTo('/tabs/catalog')">{t}Перейти в каталог{/t}</button>
            </div>
          </ion-col>
        </ion-row>
      </ion-grid>
    </div>
  </div>
</ion-content>
