<div id="comments-block">
  <div *ngIf="inLoading" class="margin-48-bottom">
    <app-comments-skeleton></app-comments-skeleton>
  </div>

  <div *ngIf="!inLoading && commentList && commentList.length" class="margin-48-bottom">
    <div class="comments-head">
      <h3>{t}Недавние отзывы{/t}</h3>
    </div>
    <swiper
      [slidesPerView]="2.2"
      [spaceBetween]="16"
      [breakpoints]="{ '320': { slidesPerView: 1.2 }, '575': { slidesPerView: 2.2 },'868': { slidesPerView: 3.2 } }"
    >
      <ng-template swiperSlide *ngFor="let comment of commentList">
        <div class="comment">
          <div class="comment-head">
            <div class="comment-rating">
              <div class="comment-rating__stars">
                <div class="comment-rating__stars-active" style="width: { { comment.getRatingPercent() } }%"></div>
              </div>
            </div>
            <div class="comment-date">{ { formatDate(comment.dateof) } }</div>
          </div>
          <div class="comment-body">
            <div class="comment-body__product" (click)="navigateTo('/tabs/catalog/product/' + comment.aid)">
              <div class="comment-body__product-image" *ngIf="comment.productObject.getImages()">
                <ion-img [src]="comment.productObject.getImages()[0]['big_url']"></ion-img>
              </div>
              <div class="comment-body__product-title" >
                { { comment.productObject.title } }
              </div>
            </div>
            <div class="comment-body__message">
              { { comment.message } }
            </div>
          </div>
          <div class="comment-author">
            Автор: { { comment.userName } }
          </div>
        </div>
      </ng-template>
    </swiper>
  </div>
</div>
