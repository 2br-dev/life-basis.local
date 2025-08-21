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
        <div class="head__title" *ngIf="!inLoading && brand" [innerHTML]="brand.title"></div>
        <div class="head__title" *ngIf="inLoading"><ion-skeleton-text animated style="height: 32px; width: 200px;"></ion-skeleton-text></div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content>
  <div id="brand">
    <div class="brand-section" *ngIf="inLoading">
      <app-brand-skeleton></app-brand-skeleton>
    </div>
    <div class="brand-section" *ngIf="!inLoading && brand">
      <div class="container">
        <ion-grid>
          <ion-row>
            <ion-col size-md="6" size="12">
              <div class="brand-image" *ngIf="brand.getImage()">
                <img [src]="brand.getImage()['big_url']" width="474" height="319" alt="">
              </div>
            </ion-col>
            <ion-col size-md="6" size="12">
              <div class="description" *ngIf="brand.description">
                <div [innerHTML]="brand.description"></div>
              </div>
              <button
                class="button button_primary margin-24-top w-100"
                type="button"
                (click)="openBrandProducts(brand.id)"
              >{t}Товары бренда{/t}</button>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>
