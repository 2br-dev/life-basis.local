<div id="stories">
  <div *ngIf="!inLoading && storiesGroups && storiesGroups.length">
    {*<app-stories-skeleton *ngIf="inLoading"></app-stories-skeleton>*}
    <div class="margin-16-bottom" >
      <swiper #storiesSwiper [slidesPerView]="'auto'" [spaceBetween]="8">
        <ng-template swiperSlide *ngFor="let group of storiesGroups; index as i">
          <div class="story-slide" [ngClass]="!group[i].isViewed ? 'newStory' : ''" (click)="openStories(group[i])">
            <img *ngIf="group[i].getImage()" [src]="group[i].getImage()['middle_url']" [alt]="group[i].title">
          </div>
        </ng-template>
      </swiper>
    </div>
  </div>
</div>
