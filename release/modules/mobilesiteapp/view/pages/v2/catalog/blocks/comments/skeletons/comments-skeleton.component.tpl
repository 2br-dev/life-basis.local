<div class="comments-head">
  <div>
    <ion-skeleton-text animated style="width: 220px; height: 28px;"></ion-skeleton-text>
  </div>
</div>
<div class="d-flex comments-list-skeletons">
  <div class="comment" *ngFor="let item of [].constructor(4)">
    <div class="comment-head">
      <div class="comment-rating">
        <div class="comment-rating__stars"></div>
      </div>
      <div class="comment-date">
        <ion-skeleton-text animated style="width: 60px; height: 21px;"></ion-skeleton-text>
      </div>
    </div>
    <div class="comment-body" style="margin-top: 4px">
      <div class="comment-body__product">
        <div class="comment-body__product-image">
          <ion-skeleton-text animated style="width: 40px; height: 40px;"></ion-skeleton-text>
        </div>
        <div class="comment-body__product-title" >
          <ion-skeleton-text animated style="width: 100%; height: 21px;"></ion-skeleton-text>
        </div>
      </div>
      <div class="comment-body__message">
        <ion-skeleton-text animated style="width: 100%; height: 21px;"></ion-skeleton-text>
        <ion-skeleton-text animated style="width: 100%; height: 21px;"></ion-skeleton-text>
      </div>
    </div>
    <div class="comment-author" style="margin-top: 12px; bottom: 8px; right: 8px;">
      <ion-skeleton-text animated style="width: 85px; height: 21px;"></ion-skeleton-text>
    </div>
  </div>
</div>
