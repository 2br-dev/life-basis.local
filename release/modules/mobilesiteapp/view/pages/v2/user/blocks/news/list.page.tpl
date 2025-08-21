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
        <div class="head__title" *ngIf="newsCategory" [innerHTML]="newsCategory.title"></div>
        <div class="head__expand_right">
          <app-phone-button *ngIf="mobilePhone" [phone]="mobilePhone"></app-phone-button>
          <app-search-button></app-search-button>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div id="news">
    <ion-refresher {if $client_version >= 3.2}*ngIf="canRefreshPage"{/if} slot="fixed" (ionRefresh)="doRefresh($event)">
      <ion-refresher-content refreshingSpinner="circles">
      </ion-refresher-content>
    </ion-refresher>
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
                <app-news-skeleton></app-news-skeleton>
              </div>
              <div class="margin-16-left-table" *ngIf="!inLoading">
                <ul class="info-menu" *ngIf="newsList && newsList.length">
                  <li class="info-menu__item" *ngFor="let item of newsList">
                    <a class="info-menu__link" (click)="navigateTo('/tabs/user/news/' + item.id)" [innerHTML]="item.title"></a>
                  </li>
                </ul>
                <ion-infinite-scroll threshold="100px" *ngIf="showMoreButton" (ionInfinite)="showMore($event)">
                  <ion-infinite-scroll-content>
                    <app-news-skeleton></app-news-skeleton>
                  </ion-infinite-scroll-content>
                </ion-infinite-scroll>
                <p *ngIf="!newsList">
                  {t}Ничего не найдено{/t}
                </p>
              </div>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
  </div>
  </div>
</ion-content>
