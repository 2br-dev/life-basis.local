<div id="brands">
  <div *ngIf="inLoading">
    <app-brands-skeleton></app-brands-skeleton>
  </div>

  <div *ngIf="!inLoading && brandList && brandList.length">
    <div class="brands-head">
      <h3>{t}Бренды{/t}</h3>
      <button type="button" class="brands-btn" (click)="navigateTo('/tabs/catalog/brands')">
        <span class="margin-4-right">{t}Смотреть все{/t}</span>
        <svg width="16" height="20" viewBox="0 0 16 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M5.21967 15.7891C4.92678 15.5079 4.92678 15.0521 5.21967 14.7709L10.1893 10L5.21967 5.22912C4.92678 4.94794 4.92678 4.49206 5.21967 4.21088C5.51256 3.92971 5.98744 3.92971 6.28033 4.21088L11.7803 9.49088C12.0732 9.77206 12.0732 10.2279 11.7803 10.5091L6.28033 15.7891C5.98744 16.0703 5.51256 16.0703 5.21967 15.7891Z"/>
        </svg>
      </button>
    </div>
    <swiper
      [slidesPerView]="2.2"
      [spaceBetween]="16"
      [breakpoints]="{ '575': { slidesPerView: 2.5},'768': { slidesPerView: 3.5}, '1200': { slidesPerView: 4}}"
    >
      <ng-template swiperSlide *ngFor="let brand of brandList">
        <a (click)="navigateTo('/tabs/catalog/brands/' + brand.id)" class="brand-item">
          <div class="brand-item__img">
            <ion-img *ngIf="brand.getIcon()" [src]="brand.getIcon()['middle_url']" alt=""></ion-img>
          </div>
          <div class="brand-item__title"> { { brand.title } }</div>
        </a>
      </ng-template>
    </swiper>
  </div>
</div>
