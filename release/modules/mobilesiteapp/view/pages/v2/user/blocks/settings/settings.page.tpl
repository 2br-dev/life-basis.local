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
        <div class="head__title">{t}Настройка уведомлений{/t}</div>
        <div class="head__expand_right">
          <app-phone-button *ngIf="mobilePhone" [phone]="mobilePhone"></app-phone-button>
          <app-search-button></app-search-button>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div id="settings">
    <div class="section">
      <div class="container">
        <ion-grid>
          <ion-row>
            <ion-col size-lg="5" size-md="6" class="ion-hide-md-down">
              <div class="menu">
                <app-lk-menu></app-lk-menu>
              </div>
            </ion-col>
            <ion-col size-lg="5" size-md="6" offset-lg="1" size="12">
              <div *ngIf="inLoading">
                <app-settings-skeleton></app-settings-skeleton>
              </div>
              <div class="margin-16-left-table" *ngIf="!inLoading">
                <div class="fz-14 c-dark">
                  <h4>{t}Информационные рассылки{/t}</h4>
                  <p>{t}Информационные расслыки позволят нам совевременно уведолмять вас о лучших предложениях и поступлениях нужных вам товаров{/t}</p>
                </div>
                <div *ngIf="pushItems && pushItems.length">
                  <div class="notice-item margin-16-top" *ngFor="let item of pushItems">
                    <div class="margin-16-right" [innerHTML]="item.title"></div>
                    <div class="notice-checkbox">
                      <input [id]="item.id"
                        type="checkbox"
                        [(ngModel)]="item.enable"
                        [checked]="item.enable"
                        (change)="changePushItemSettings(item)"
                      >
                      <label [for]="item.id"></label>
                    </div>
                  </div>
                </div>
                <button
                  class="button button_primary w-100 margin-40-top"
                  [attr.disabled]="formIsLoading ? '' : null"
                  (click)="saveSettings()"
                >
                  {t}Сохранить{/t}
                  <span *ngIf="formIsLoading" class="formIsLoading">
                    <svg width="44" height="44" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="var(--rs-color-primary)">
                      <g fill="none" fill-rule="evenodd" stroke-width="2">
                        <circle cx="22" cy="22" r="1">
                          <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                          <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
                        </circle>
                        <circle cx="22" cy="22" r="1">
                          <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                          <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
                        </circle>
                      </g>
                    </svg>
                  </span>
                </button>
              </div>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>
