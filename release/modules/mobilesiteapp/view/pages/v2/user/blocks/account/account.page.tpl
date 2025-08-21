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
        <div class="head__title">{t}Мои данные{/t}</div>
        <div class="head__expand_right">
          <app-phone-button *ngIf="mobilePhone" [phone]="mobilePhone"></app-phone-button>
          <app-search-button></app-search-button>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div id="account">
    {if $client_version >= 2.8}
      <ion-refresher *ngIf="useBonuses && isAuth && !isMobile" slot="fixed" (ionRefresh)="doRefresh($event)">
        <ion-refresher-content refreshingSpinner="circles">
        </ion-refresher-content>
      </ion-refresher>
    {/if}
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
              <div class="margin-16-left-table">
                <ul class="my-menu">
                  <li class="my-menu__item">
                    <a class="my-menu__link" (click)="navigateTo('/tabs/user/account/edit')">
                      <div class="my-menu__link-title">{t}Личные данные{/t}</div>
                      <div class="my-menu__link-desc">{t}Имя, дата рождения, e-mail{/t}</div>
                    </a>
                  </li>
                  <li class="my-menu__item" {if $client_version >= 3.1} *ngIf="!env.telegram" {/if}>
                    <a class="my-menu__link" (click)="navigateTo('/tabs/user/settings')">
                      <div class="my-menu__link-title">{t}Настройка уведомлений{/t}</div>
                      <div class="my-menu__link-desc">{t}Получение уведомлений от приложения{/t}</div>
                    </a>
                  </li>
                  <li class="my-menu__item" *ngIf="recurringShowMethodsMenu">
                    <a class="my-menu__link" (click)="navigateTo('/tabs/user/account/payment-methods')">
                      <div class="my-menu__link-title">{t}Платежные карты{/t}</div>
                      <div class="my-menu__link-desc">{t}Ваши карты для оплаты товаров{/t}</div>
                    </a>
                  </li>
                  <li class="my-menu__item" *ngIf="showProfileManagementLink">
                    <a class="my-menu__link" (click)="navigateTo('/tabs/user/account/profile-management')">
                      <div class="my-menu__link-title">{t}Управление профилем{/t}</div>
                      <div class="my-menu__link-desc">{t}Управление профилем пользователя{/t}</div>
                    </a>
                  </li>
                </ul>
              </div>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>