<div id="mainBanner">
  <div *ngIf="inLoading">
    <app-mainbanner-skeleton></app-mainbanner-skeleton>
  </div>

  <div *ngIf="!inLoading && bannerList && bannerList.length">
    <div class="margin-16-bottom">
      <swiper [slidesPerView]="1" [spaceBetween]="16">
        <ng-template swiperSlide *ngFor="let banner of bannerList">
          <a class="index-banner" (click)="openBanner(banner)" *ngIf="banner.getImage()">
            <img [src]="banner.getImage()['original_url']" alt="">
          </a>
        </ng-template>
      </swiper>
    </div>
  </div>
</div>
