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
        <div class="head__title">{t}Комментарии{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div id="comments">
    <div *ngIf="inLoading">
      <app-product-skeleton></app-product-skeleton>
    </div>
    <div class="section" *ngIf="!inLoading">
      <div class="container">
        <ion-grid>
          <ion-row>
            <ion-col size-lg="5" size-md="6" size="12">
              <div class="review-aside">
                <div class="product__wrapper d-flex margin-24-right margin-24-bottom" *ngIf="productIsLoading">
                  <ion-skeleton-text animated style="width: 100%;max-width: 70px;height: 70px;--border-radius: 4px;margin: 0;"></ion-skeleton-text>
                  <div style="width: 100%;">
                    <ion-skeleton-text animated style="width: 100%;max-width: 200px; height: 16px; margin:0 0 0 16px"></ion-skeleton-text>
                    <ion-skeleton-text animated style="width: 100%;max-width: 160px; height: 16px; margin:8px 0 0 16px"></ion-skeleton-text>
                  </div>
                </div>

                <div class="product__wrapper d-flex margin-24-right margin-24-bottom" *ngIf="!productIsLoading && product">
                  <div class="item-card__img" *ngIf="product.getMainImage()">
                    <img [src]="product.getMainImage()['middle_url']" alt="">
                  </div>
                  <div class="item-card__title">{ { product.title } }</div>
                </div>

                <div class="review-aside__total" *ngIf="commentsTotal">
                  <div [ngPlural]="commentsTotal">
                    <ng-template ngPluralCase="=0">{t}Нет оценок{/t}</ng-template>
                    <ng-template ngPluralCase="=1">{ { commentsTotal } } {t}оценка{/t}</ng-template>
                    <ng-template ngPluralCase="other">{ { commentsTotal } } {t}оценки{/t}</ng-template>
                  </div>
                </div>
                <div class="review-aside__content">
                  <div>
                    <div class="review-aside__title">{t}Средний рейтинг{/t}</div>
                    <div class="review-aside__ball">{ { commentsRating } }</div>
                    <div class="review-aside__rating">
                      <div class="review-aside__rating-active" style="width: { { getRatingPercent() } }%"></div>
                    </div>
                  </div>
                  <div class="margin-40-bottom-table margin-24-top-table margin-24-left-mob" *ngIf="commentsMarkMatrix">
                    <div class="review-aside__ball-stars review-aside__ball-stars_{ { item.key } }" *ngFor="let item of commentsMarkMatrix">
                      <div class="rating-stars">
                        <div class="rating-stars__active"></div>
                      </div>
                      <div class="margin-16-left">{ { item.value } }</div>
                    </div>
                  </div>
                </div>
                <a
                  *ngIf="productId && !needAuth"
                  (click)="navigateTo('/tabs/catalog/product/' + productId + '/comments/add')"
                  class="w-100 button button_default button_small"
                >
                  {t}Оставить отзыв{/t}
                </a>
                <a
                  *ngIf="needAuth"
                  (click)="navigateTo('/tabs/user/auth')"
                  class="w-100 button button_default button_small"
                >
                  {t}Авторизуйтесь,чтобы оставить отзыв{/t}
                </a>
              </div>
            </ion-col>
            <ion-col size-lg="7" size-md="6" size="12" *ngIf="commentList && commentList.length">
              <div class="review-item" *ngFor="let comment of commentList">
                <div class="review-item__head">
                  <div class="review-item__title">{ { comment.userName } }</div>
                  <div>{ { comment.dateof | date:'dd.MM.YYYY' } }</div>
                </div>
                <div class="rating-stars">
                  <div class="rating-stars__active" style="width: { { comment.getRatingPercent() } }%"></div>
                </div>
                <div class="review-item__text">
                  { { comment.message } }
                </div>
                <div class="review-item__answer-wrapper" *ngIf="comment.answerMessage">
                  <div class="review-item__answer">
                    <div class="review-item__answer-header">
                      <div class="review-item__answer-title">{t}Ответ магазина{/t}</div>
                      <div class="review-item__answer-date">{ { comment.answerDateof | date:'dd.MM.YYYY' } }</div>
                    </div>
                    <div class="review-item__answer-message" [innerHTML]="comment.answerMessage"></div>
                  </div>
                </div>
              </div>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>