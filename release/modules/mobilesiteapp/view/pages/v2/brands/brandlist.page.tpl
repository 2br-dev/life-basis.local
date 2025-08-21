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
        <div class="head__title" *ngIf="!inLoading && brandList && brandList.length">{t}Наши бренды{/t}</div>
        <div class="head__title" *ngIf="inLoading"><ion-skeleton-text animated style="height: 32px; width: 200px;"></ion-skeleton-text></div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div id="brandlist">
    <ion-refresher {if $client_version >= 3.2}*ngIf="canRefreshPage"{/if} slot="fixed" (ionRefresh)="doRefresh($event)">
      <ion-refresher-content refreshingSpinner="circles">
      </ion-refresher-content>
    </ion-refresher>

    <div class="section" *ngIf="inLoading">
      <app-brandlist-skeleton></app-brandlist-skeleton>
    </div>

    <div class="section" *ngIf="!inLoading && brandList && brandList.length">
      <div class="container">
        <div class="brands-info c-dark">
          {t}Здесь представлены основные бренды нашего каталога.  Вы можете ознакомиться с каждым брендом и его продукцией подробнее, перейдя в карточку бренда.{/t}
        </div>
        <div class="brands-wrap">
          <ion-grid>
            <ion-row>
              <ion-col size-md="3" size-sm="4" size="6" *ngFor="let brand of brandList">
                <a (click)="navigateTo('/tabs/catalog/brands/' + brand.id)" class="brand-item">
                  <div class="brand-item__img">
                    <ion-img *ngIf="brand.getIcon()" [src]="brand.getIcon()['big_url']" alt=""></ion-img>
                  </div>
                  <div class="brand-item__title" [innerHTML]="brand.title"></div>
                </a>
              </ion-col>
            </ion-row>
          </ion-grid>
        </div>
      </div>
    </div>

    <ion-infinite-scroll threshold="100px" *ngIf="showMoreButton" (ionInfinite)="showMore($event)">
      <ion-infinite-scroll-content>
      </ion-infinite-scroll-content>
    </ion-infinite-scroll>
  </div>
</ion-content>
