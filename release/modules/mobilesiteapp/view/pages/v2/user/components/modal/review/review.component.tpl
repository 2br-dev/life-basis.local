<div id="modal-review">
  <ion-header>
    <div class="container">
      <div class="modal-review-head">
        <div class="modal-review-head__flex-1">
          <button type="button" class="modal-close" (click)="dismissModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <div class="modal-review-head__title">
          {t}Оставить отзыв{/t}
        </div>
        <div class="modal-review-head__flex-1"></div>
      </div>
    </div>
  </ion-header>
  <div class="inner-content">
    <div class="container">
      <div *ngIf="reviewExist">
        <div>{t}Спасибо за отзыв! Ваше мнение помогает нам становиться лучше.{/t}</div>
      </div>
      <div *ngIf="!reviewExist">
        <div class="margin-16-bottom" *ngIf="errors && errors.length">
          <div class="form__error" *ngFor="let error of errors">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
              <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
              <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
            </svg>
            <span class="margin-8-left">
              { { error } }
            </span>
          </div>
        </div>
        <div class="margin-16-bottom">
          <label for="message" class="form-label">{t}Текст отзыва{/t}</label>
          <textarea class="form-control" name="message" id="message" cols="20" rows="3" required [(ngModel)]="review.message"></textarea>
        </div>
        <div class="margin-16-bottom">
          <label for="rateStars" class="form-label">{t}Оценка{/t}</label>
          <input type="hidden" class="form-control" id="rateStars" >
          <ul class="rateStars">
            <li
              class="rateStars"
              [ngClass]="review.rate >= star ? 'rateStars__active' : ''"
              (click)="setRate(star)"
              *ngFor="let star of [1,2,3,4,5]"
            ></li>
          </ul>
        </div>
        <div class="checkbox d-flex margin-16-bottom">
          <input type="checkbox" id="rateProducts" name="rateProducts" [(ngModel)]="rateProducts" (click)="changeRateProducts()">
          <label class="align-items-center" for="rateProducts">
          <span class="checkbox-attr">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
              <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
            </svg>
          </span>
            <span>{t}Оценить качество товаров{/t}</span>
          </label>
        </div>

        <div class="margin-24-bottom" *ngIf="rateProducts">
          <div class="order-review-product__wrapper" *ngFor="let product of order.productItems">
            <div class="order-review-product__image-title">
              <div class="order-review-product__img">
                <img [src]="product.getImage()['small_url']" alt="">
              </div>
              <div class="order-product__title_offer">
                <div class="order-product__title" [innerHTML]="product.title"></div>
                <div class="order-product__offers fz-12 margin-8-top" *ngIf="product.offerValues">
                  <div *ngFor="let item of product.offerValues" class="c-primary-shady">
                    <span>{ { item.title } }: { { item.value } }</span>
                  </div>
                </div>
              </div>
              <div class="order-review-product__checkbox checkbox d-flex margin-16-bottom">
                <input
                  type="checkbox"
                  id="reviewOneProduct_{ { product.entityId } }"
                  name="reviewOneProduct_{ { product.entityId } }"
                  [(ngModel)]="review.productArr[product.entityId].enable"
                  (click)="changeRateOneProduct(review.productArr[product.entityId])"
                >
                <label class="align-items-center" for="reviewOneProduct_{ { product.entityId } }">
                  <span class="checkbox-attr">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                      <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                    </svg>
                  </span>
                  <span>{t}Оценить товар{/t}</span>
                </label>
              </div>
            </div>
            <div class="order-review-product__content" *ngIf="review.productArr[product.entityId].enable">
              <div>
                <div class="form-label">
                  {t}Оценка{/t}
                  <ul class="order-review-product__rateStars">
                    <li
                      class="rateStars"
                      [ngClass]="review.productArr[product.entityId].rate >= star ? 'rateStars__active' : ''"
                      (click)="setProductRate(star, review.productArr[product.entityId])"
                      *ngFor="let star of [1,2,3,4,5]"
                    ></li>
                  </ul>
                </div>
                <input type="hidden" class="form-control" id="rateStars" >
              </div>
              <div>
                <label for="message" class="form-label">{t}Текст отзыва{/t}</label>
                <textarea
                  class="form-control"
                  name="message"
                  id="message"
                  cols="10"
                  rows="2"
                  required
                  [(ngModel)]="review.productArr[product.entityId].message"
                ></textarea>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <ion-footer>
    <div class="container">
      <div class="modal-footer-button" *ngIf="reviewExist">
        <button
          type="button"
          class="button button_primary button_small w-100 ion-margin-bottom"
          (click)="dismissModal(reviewExist)"
        >
          {t}Обновить страницу{/t}
        </button>
      </div>
      <div class="modal-footer-button" *ngIf="!reviewExist">
        <button
          type="button"
          class="button button_primary button_small w-100 ion-margin-bottom"
          (click)="addReview()"
          [attr.disabled]="formIsLoading ? '' : null"
        >
          {t}Отправить отзыв{/t}
          <span *ngIf="formIsLoading" class="formIsLoading">
            <svg width="44" height="44" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="var(--rs-color-primary)">
              <g fill="none" fill-rule="evenodd" stroke-width="2">
                <circle cx="22" cy="22" r="1">
                  <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                  <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
                </circle>
                <circle cx="22" cy="22" r="1">
                  <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                  <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
                </circle>
              </g>
            </svg>
            </span>
        </button>
      </div>
    </div>
  </ion-footer>
</div>