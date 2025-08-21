<div id="banners">
  <div *ngIf="inLoading">
    <app-banners-skeleton></app-banners-skeleton>
  </div>
  <div *ngIf="!inLoading && bannerList && bannerList.length">
    <h2 class="margin-24-bottom-table">{t}Каталог{/t}</h2>
    <div class="margin-32-bottom">
      <swiper
        [slidesPerView]="1.2"
        [spaceBetween]="16"
        [breakpoints]="{ '575': { slidesPerView: 1.5},'768': { slidesPerView: 2.3}}"
      >
        <ng-template swiperSlide *ngFor="let banner of bannerList">
          <a class="index-banner" (click)="openBanner(banner)" *ngIf="banner.getImage()">
            <img [src]="banner.getImage()['original_url']" alt="">
          </a>
        </ng-template>
      </swiper>
    </div>
  </div>
</div>
