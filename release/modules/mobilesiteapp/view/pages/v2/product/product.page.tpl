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
        <div class="head__title" *ngIf="dir" [innerHTML]="dir.name"></div>
        <div class="head__expand_right">
          <button
            type="button"
            class="product-head__favorite"
            *ngIf="!inLoading && product"
            (click)="toggleInFavorite(product)"
            [ngClass]="product.inFavorite || product.isInFavorite() ? 'product-head__favorite_active' : ''"
          >
            <svg width="24" height="24" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path class="inFavoriteСontour" d="M17.2289 0C15.1859 0 13.2734 0.88834 12 2.37674C10.7266 0.888289 8.81417 0 6.77106 0C3.0375 0 0 2.91698 0 6.5025C0 9.31012 1.7433 12.5576 5.18136 16.1546C7.82722 18.9228 10.7055 21.0661 11.5246 21.6571L11.9998 22L12.4751 21.6572C13.2942 21.0662 16.1726 18.9229 18.8185 16.1547C22.2567 12.5577 24 9.31017 24 6.5025C24 2.91698 20.9625 0 17.2289 0ZM17.6527 15.1271C15.4764 17.4039 13.135 19.2445 11.9998 20.0927C10.8647 19.2445 8.52343 17.4039 6.34713 15.127C3.23003 11.8658 1.58242 8.88345 1.58242 6.5025C1.58242 3.75497 3.91005 1.51966 6.77106 1.51966C8.65382 1.51966 10.3923 2.5051 11.308 4.09147L12 5.29027L12.692 4.09147C13.6077 2.50515 15.3461 1.51966 17.2289 1.51966C20.09 1.51966 22.4176 3.75492 22.4176 6.5025C22.4176 8.88355 20.7699 11.8659 17.6527 15.1271Z"/>
              <path class="inFavoriteBody" d="M17.6527 15.1271C15.4764 17.4039 13.135 19.2445 11.9998 20.0927C10.8647 19.2445 8.52343 17.4039 6.34713 15.127C3.23003 11.8658 1.58242 8.88345 1.58242 6.5025C1.58242 3.75497 3.91005 1.51966 6.77106 1.51966C8.65382 1.51966 10.3923 2.5051 11.308 4.09147L12 5.29027L12.692 4.09147C13.6077 2.50515 15.3461 1.51966 17.2289 1.51966C20.09 1.51966 22.4176 3.75492 22.4176 6.5025C22.4176 8.88355 20.7699 11.8659 17.6527 15.1271Z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content>
  <div id="product">
    <div class="product" *ngIf="!inLoading && product">
      <div class="container">
        <ion-grid>
          <ion-row>
            <ion-col size-md="6" size="12">
              <div class="product__image product-images-swiper" *ngIf="product.getImages()">
                <swiper
                  [slidesPerView]="1"
                  [lazy]="true"
                >
                  <ng-template swiperSlide *ngFor="let image of product.getImages()">
                    <div class="product__img-wrap">
                      <canvas width="393" height="348"></canvas>
                      <ion-img [src]="image.big_url" tappable (click)="previewImage(image)"></ion-img>
                    </div>
                  </ng-template>
                </swiper>
              </div>

            </ion-col>
            <ion-col size-md="6" size="12">
              <div class="product__title margin-40-bottom">{ { product.title } }</div>
              <div class="margin-40-bottom">
                <div class="product__price-wrap">
                  <div>
                    <div class="product__price-old" *ngIf="product.getOldCost()">
                      <span class="product__price-old-price">{ { product.getOldCost() } }</span>
                      <span class="product__price-sale" *ngIf="product.getDiscountPercent()">- { { product.getDiscountPercent() } }%</span>
                    </div>
                    <div class="product__price" *ngIf="product.getCost()">
                      { { product.getCost() } }
                    </div>

                    <div class="product__bonuses margin-16-top" *ngIf="useBonuses && product.getOfferBonuses()">
                      <div class="bonus-coin">
                        <span class="fw-semibold">+{ { product.getOfferBonuses() } }</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="18" height="18" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <g><path style="opacity:0.994" fill="#feca59" d="M 232.5,-0.5 C 247.833,-0.5 263.167,-0.5 278.5,-0.5C 360.279,7.85938 424.112,45.8594 470,113.5C 491.886,149.37 503.053,188.37 503.5,230.5C 501.992,278.547 486.992,321.88 458.5,360.5C 445.201,376.799 430.535,391.799 414.5,405.5C 400.357,415.301 385.69,424.301 370.5,432.5C 353.258,440.246 335.591,446.579 317.5,451.5C 297.04,455.588 276.373,457.922 255.5,458.5C 234.58,458.346 213.913,456.013 193.5,451.5C 175.328,446.554 157.661,440.221 140.5,432.5C 125.053,424.385 110.386,415.052 96.5,404.5C 80.4729,391.806 66.1395,377.473 53.5,361.5C 24.8617,322.546 9.52835,278.88 7.5,230.5C 12.5359,135.339 57.5359,66.5058 142.5,24C 171.26,10.8125 201.26,2.64583 232.5,-0.5 Z"/></g>
                          <g><path style="opacity:1" fill="#e6a71e" d="M 235.5,52.5 C 286.488,49.4113 333.155,61.9113 375.5,90C 438.861,137.889 461.028,200.389 442,277.5C 426.201,324.303 396.368,359.136 352.5,382C 306.002,405.363 257.336,411.363 206.5,400C 155.25,387.383 115.083,359.216 86,315.5C 54.4382,260.047 53.4382,204.047 83,147.5C 110.124,103.5 148.624,74.3336 198.5,60C 210.832,56.8381 223.166,54.3381 235.5,52.5 Z"/></g>
                          <g><path style="opacity:1" fill="#feca59" d="M 235.5,79.5 C 282.484,75.4459 325.15,86.9459 363.5,114C 401.055,143.274 421.055,181.774 423.5,229.5C 417.5,229.5 411.5,229.5 405.5,229.5C 402.873,180.073 380.206,142.906 337.5,118C 281.989,89.006 226.655,89.3393 171.5,119C 115.784,154.47 95.9507,204.304 112,268.5C 130.383,312.624 162.55,341.457 208.5,355C 253.917,367.25 297.251,362.083 338.5,339.5C 342.07,343.579 345.07,348.079 347.5,353C 345.286,355.458 342.619,357.458 339.5,359C 296.119,379.771 251.119,384.105 204.5,372C 163.462,359.939 131.295,336.105 108,300.5C 86.5609,262.969 82.5609,223.635 96,182.5C 113.959,138.541 145.126,108.041 189.5,91C 204.665,85.8755 219.998,82.0422 235.5,79.5 Z"/></g>
                          <g><path style="opacity:0.949" fill="#e6a71e" d="M 7.5,230.5 C 9.52835,278.88 24.8617,322.546 53.5,361.5C 52.5033,378.659 52.17,395.992 52.5,413.5C 27.8126,381.086 13.3126,344.42 9,303.5C 7.70271,279.185 7.20271,254.852 7.5,230.5 Z"/></g>
                          <g><path style="opacity:0.948" fill="#ce893d" d="M 503.5,230.5 C 503.797,254.852 503.297,279.185 502,303.5C 497.687,344.42 483.187,381.086 458.5,413.5C 458.5,395.833 458.5,378.167 458.5,360.5C 486.992,321.88 501.992,278.547 503.5,230.5 Z"/></g>
                          <g><path style="opacity:1" fill="#feca58" d="M 390.5,286.5 C 395.674,289.252 400.674,292.252 405.5,295.5C 397.937,309.276 388.437,321.61 377,332.5C 372.609,328.612 368.776,324.279 365.5,319.5C 375.542,309.726 383.876,298.726 390.5,286.5 Z"/></g>
                          <g><path style="opacity:0.983" fill="#ce893d" d="M 53.5,361.5 C 66.1395,377.473 80.4729,391.806 96.5,404.5C 96.5,422.5 96.5,440.5 96.5,458.5C 79.7948,445.462 65.1281,430.462 52.5,413.5C 52.17,395.992 52.5033,378.659 53.5,361.5 Z"/></g>
                          <g><path style="opacity:0.983" fill="#e6a71e" d="M 458.5,360.5 C 458.5,378.167 458.5,395.833 458.5,413.5C 445.872,430.462 431.205,445.462 414.5,458.5C 414.5,440.833 414.5,423.167 414.5,405.5C 430.535,391.799 445.201,376.799 458.5,360.5 Z"/></g>
                          <g><path style="opacity:0.985" fill="#ce893d" d="M 414.5,405.5 C 414.5,423.167 414.5,440.833 414.5,458.5C 400.873,469.114 386.206,478.114 370.5,485.5C 370.5,467.833 370.5,450.167 370.5,432.5C 385.69,424.301 400.357,415.301 414.5,405.5 Z"/></g>
                          <g><path style="opacity:0.986" fill="#e6a71e" d="M 96.5,404.5 C 110.386,415.052 125.053,424.385 140.5,432.5C 140.5,450.167 140.5,467.833 140.5,485.5C 124.794,478.114 110.127,469.114 96.5,458.5C 96.5,440.5 96.5,422.5 96.5,404.5 Z"/></g>
                          <g><path style="opacity:0.988" fill="#ce893c" d="M 140.5,432.5 C 157.661,440.221 175.328,446.554 193.5,451.5C 193.5,469.167 193.5,486.833 193.5,504.5C 174.955,500.321 157.289,493.987 140.5,485.5C 140.5,467.833 140.5,450.167 140.5,432.5 Z"/></g>
                          <g><path style="opacity:0.988" fill="#e6a71e" d="M 370.5,432.5 C 370.5,450.167 370.5,467.833 370.5,485.5C 353.711,493.987 336.045,500.321 317.5,504.5C 317.5,486.833 317.5,469.167 317.5,451.5C 335.591,446.579 353.258,440.246 370.5,432.5 Z"/></g>
                          <g><path style="opacity:0.993" fill="#e5a61e" d="M 193.5,451.5 C 213.913,456.013 234.58,458.346 255.5,458.5C 255.5,476.167 255.5,493.833 255.5,511.5C 248.167,511.5 240.833,511.5 233.5,511.5C 220.14,509.921 206.806,507.588 193.5,504.5C 193.5,486.833 193.5,469.167 193.5,451.5 Z"/></g>
                          <g><path style="opacity:0.997" fill="#ce893d" d="M 317.5,451.5 C 317.5,469.167 317.5,486.833 317.5,504.5C 304.194,507.588 290.86,509.921 277.5,511.5C 270.167,511.5 262.833,511.5 255.5,511.5C 255.5,493.833 255.5,476.167 255.5,458.5C 276.373,457.922 297.04,455.588 317.5,451.5 Z"/></g>
                        </svg>
                        <div [ngPlural]="product.orderBonuses">
                          <ng-template ngPluralCase="=1">{t}бонусный балл{/t}</ng-template>
                          <ng-template ngPluralCase="few">{t}бонусных балла{/t}</ng-template>
                          <ng-template ngPluralCase="other">{t}бонусных баллов{/t}</ng-template>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Комплектации -->
              <div class="offersWrapper" *ngIf="product.isOffersUse() || product.isMultiOffersUse() || product.isVirtualMultiOffersUse()">
                <!-- Многомерные комплектации -->
                <div class="multiOffers" *ngIf="product.isMultiOffersUse() || product.isVirtualMultiOffersUse()">
                  <div class="multiOffer" *ngFor="let multioffer of product.multiOffers">
                    <!--Список в виде цветов и картинок многомерных комплектаций-->
                    <div class="margin-40-bottom" *ngIf="multioffer.propertyType == 'color' || multioffer.propertyType == 'image'">
                      <div class="product__section-subtitle">{ { multioffer.title } }</div>
                      <ul class="item-product-choose">
                        <li *ngFor="let multiofferValue of multioffer.values">
                          <div class="radio-color" [ngClass]="multioffer.propertyType == 'color' ? 'radio-color' : 'radio-image radio-image_cover'">
                            <input
                              type="radio"
                              [attr.id]="multiofferValue.id ? multiofferValue.id : multiofferValue.value"
                              [name]="'multioffer_' + multioffer.propId + '[]'"
                              [attr.checked]="multioffer.isCurrent(multiofferValue)"
                              [attr.disabled]="product.virtualOffers && product.virtualOffers.length && !multiofferValue.exist ? '' : null"
                              (change)="multioffer.setCurrentValue(multiofferValue); changeMultiOffer()"
                            >
                            <label
                              [attr.disabled]="product.virtualOffers && product.virtualOffers.length && !multiofferValue.exist ? '' : null"
                              [attr.for]="multiofferValue.id ? multiofferValue.id : multiofferValue.value"
                            >
                              <ion-img
                                *ngIf="multioffer.propertyType == 'image'"
                                [src]="multiofferValue.images.micro_url"
                              ></ion-img>
                              <span
                                *ngIf="multioffer.propertyType == 'color'"
                                class="radio-bg-color"
                                style="background: { { multiofferValue.color } }"
                              ></span>
                            </label>
                          </div>
                        </li>
                      </ul>
                    </div>

                    <!--Простой список многомерных комплектаций-->
                    <div class="margin-40-bottom" *ngIf="multioffer.propertyType == 'list'">
                      <div class="product__section-subtitle">{ { multioffer.title } }</div>
                      <ul class="item-product-choose">
                        <li *ngFor="let multiofferValue of multioffer.values">
                          <div class="radio-image radio-image_txt">
                            <input
                              type="radio"
                              [id]="multiofferValue.id ? multiofferValue.id : multiofferValue.value"
                              [name]="'multioffer_' + multioffer.propId + '[]'"
                              [attr.checked]="multioffer.isCurrent(multiofferValue)"
                              [attr.disabled]="product.virtualOffers && product.virtualOffers.length && !multiofferValue.exist ? '' : null"
                              (change)="multioffer.setCurrentValue(multiofferValue); changeMultiOffer()"
                            >
                            <label
                              [attr.disabled]="product.virtualOffers && product.virtualOffers.length && !multiofferValue.exist ? '' : null"
                              [for]="multiofferValue.id ? multiofferValue.id : multiofferValue.value"
                              [ngClass]="multioffer.isCurrent(multiofferValue) ? 'fw-900' : ''"
                            >
                              { { multiofferValue.value } }
                            </label>
                          </div>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Простые комплектации -->
                <div class="margin-40-bottom singleOffer" *ngIf="!product.isMultiOffersUse() && product.isOffersUse()">
                  <div class="product__section-subtitle">{ { product.getOfferCaption() } }</div>
                  <ul class="item-product-choose">
                    <li *ngFor="let offer of product.offers">
                      <div class="radio-image radio-image_txt">
                        <input
                          type="radio"
                          [id]="offer.id"
                          [name]="'offer_' + product.offer.id + '[]'"
                          [value]="offer.title"
                          (change)="product.setOfferById(offer.id); changeOffer()"
                          [attr.checked]="product.offer.title === offer.title ? '' : null"
                        >
                        <label
                          [ngClass]="product.offer.title === offer.title ? 'fw-900' : ''"
                          [for]="offer.id"
                        >
                          { { offer.title } }
                        </label>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Краткое описание -->
              <div class="margin-40-bottom" *ngIf="product.shortDescription">
                <div class="product__description product__description_full c-dark" [innerHTML]="product.shortDescription"></div>
              </div>
              <!-- Наличие на складах -->
              <div class="margin-40-bottom" *ngIf="product.stickRanges && getWarehouses() && getWarehouses().length">
                <div class="product__availability">
                  <div class="product__section-title">{t}Наличие в магазинах{/t}</div>
                  <div class="product__availability-list">
                    <div class="product-availability-item" *ngFor="let warehouse of getWarehouses()">
                      <div class="d-flex">
                        <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                          <path d="M9.9997 0.833252C5.03226 0.833252 1.82507 6.01084 4.0539 10.3404L7.47493 17.5821C8.4736 19.6963 11.5273 19.6932 12.5245 17.5821L15.9456 10.3403C18.1735 6.0125 14.9694 0.833252 9.9997 0.833252V0.833252ZM14.8966 9.82756C14.8898 9.84081 15.0515 9.49885 11.4623 17.0966C10.8829 18.3229 9.1171 18.3241 8.53718 17.0966C4.9615 9.52751 5.11 9.84145 5.1029 9.82763C3.27577 6.29224 5.87141 1.98535 9.9997 1.98535C14.1256 1.98535 16.7249 6.28943 14.8966 9.82756Z" />
                          <path d="M10.0003 4.16675C8.16233 4.16675 6.66699 5.66209 6.66699 7.50008C6.66699 9.33808 8.16233 10.8334 10.0003 10.8334C11.8383 10.8334 13.3337 9.33808 13.3337 7.50008C13.3337 5.66209 11.8384 4.16675 10.0003 4.16675ZM10.0003 9.63364C8.82387 9.63364 7.8668 8.67657 7.8668 7.50012C7.8668 6.32367 8.82387 5.3666 10.0003 5.3666C11.1768 5.3666 12.1338 6.32367 12.1338 7.50012C12.1338 8.67657 11.1768 9.63364 10.0003 9.63364Z" />
                        </svg>
                        <div class="margin-8-left">
                          <span *ngIf="warehouse.title" [innerHTML]="warehouse.title"></span>
                          <span *ngIf="!warehouse.title" [innerHTML]="warehouse.getName()"></span>
                        </div>
                      </div>
                      <div class="product-availability-item__indicator">
                        <div
                          class="product-availability-item__dot"
                          *ngFor="let stick of product.stickRanges"
                          [ngClass]="
                              (stick <= product.getStickNumForWarehouse(warehouse))
                              ? product.getStickNumForWarehouse(warehouse) > 1
                              ? 'product-availability-item__dot_many'
                              : 'product-availability-item__dot_few'
                              : ''"
                        ></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Описание -->
              <div class="margin-40-bottom" *ngIf="product.description">
                <div class="product__section-title">{t}Описание{/t}</div>
                <div
                  class="product__description c-dark"
                  [ngClass]="descriptionFull ? 'product__description_full' : ''"
                  [innerHTML]="product.description"
                >
                </div>
                <button
                  type="button"
                  class="product__more margin-16-top"
                  (click)="toggleDescription()"
                  [ngClass]="descriptionFull ? 'product__more_revert' : ''"
                >
                  <span class="margin-8-right" [innerHTML]="descriptionButtonName">{t}Развернуть{/t}</span>
                  <svg width="21" height="20" viewBox="0 0 21 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.27241 7.68306C5.52625 7.43898 5.93781 7.43898 6.19165 7.68306L10.4987 11.8245L14.8057 7.68306C15.0596 7.43898 15.4711 7.43898 15.725 7.68306C15.9788 7.92714 15.9788 8.32286 15.725 8.56694L10.9583 13.1503C10.7045 13.3944 10.2929 13.3944 10.0391 13.1503L5.27241 8.56694C5.01857 8.32286 5.01857 7.92714 5.27241 7.68306Z"/>
                  </svg>
                </button>
              </div>
              <!-- Характеристики -->
              <div class="margin-40-bottom" *ngIf="product.propertyValues || product.brand">
                <div class="product__section-title">{t}Характеристики{/t}</div>
                <div
                  class="product__features"
                  [ngClass]="(propertiesFull || product.propertyValues.length <= 4) ? 'product__features_full' : ''"
                >
                  <div class="product__feature" *ngIf="product.brand">
                    <div class="c-dark margin-16-right">{t}Бренд{/t}</div>
                    <div class="fw-bold">
                      <a (click)="navigateTo('/tabs/catalog/brands/' + product.brand.id)">{ { product.brand.title } }</a>
                    </div>
                  </div>
                  <div class="margin-32-top" *ngFor="let propertyGroup of product.propertyValues">
                    <h5>{ { propertyGroup.title } }</h5>
                    <div class="product__feature" *ngFor="let property of propertyGroup.list">
                      <div class="c-dark margin-16-right">{ { property.title } }</div>
                      <div class="fw-bold">{ { property.textValue } }</div>
                    </div>
                  </div>
                </div>
                <button
                  type="button"
                  class="product__more margin-24-top"
                  *ngIf="product.propertyValues.length >= 4"
                  (click)="toggleProperties()"
                  [ngClass]="propertiesFull ? 'product__more_revert' : ''"
                >
                  <span class="margin-8-right" [innerHTML]="propertiesButtonName"></span>
                  <svg width="21" height="20" viewBox="0 0 21 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.27241 7.68306C5.52625 7.43898 5.93781 7.43898 6.19165 7.68306L10.4987 11.8245L14.8057 7.68306C15.0596 7.43898 15.4711 7.43898 15.725 7.68306C15.9788 7.92714 15.9788 8.32286 15.725 8.56694L10.9583 13.1503C10.7045 13.3944 10.2929 13.3944 10.0391 13.1503L5.27241 8.56694C5.01857 8.32286 5.01857 7.92714 5.27241 7.68306Z"/>
                  </svg>
                </button>
              </div>
              <!-- Отзывы -->
              <div>
                <a (click)="navigateTo('/tabs/catalog/product/' + product.id +'/comments')" class="product-reviews">
                  <div class="product-reviews__info">
                    <div class="product-reviews__title">{t}Средний{/t} <br> {t}рейтинг{/t}</div>
                    <div
                      class="product-reviews__count"
                      [ngPlural]="product.getCommentsCount()"
                    >
                      <ng-template ngPluralCase="=0">{t}Нет оценок{/t}</ng-template>
                      <ng-template ngPluralCase="=1">{ { product.getCommentsCount() } } {t}оценка{/t}</ng-template>
                      <ng-template ngPluralCase="other">{ { product.getCommentsCount() } } {t}оценки{/t}</ng-template>
                    </div>
                  </div>
                  <div class="product-reviews__stars">
                    <div class="margin-32-right-table margin-24-right">
                      <div class="product-reviews__score">{ { product.rating } }</div>
                      <div class="rating-stars">
                        <div class="rating-stars__active" style="width: { { product.getRatingPercent() } }%"></div>
                      </div>
                    </div>
                    <svg width="11" height="20" viewBox="0 0 11 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M0.345196 19.6485C-0.115065 19.1799 -0.115065 18.4201 0.345196 17.9515L8.15468 10L0.345196 2.04853C-0.115065 1.5799 -0.115065 0.820101 0.345196 0.351473C0.805457 -0.117157 1.55169 -0.117157 2.01195 0.351473L10.6548 9.15147C11.1151 9.6201 11.1151 10.3799 10.6548 10.8485L2.01195 19.6485C1.55169 20.1172 0.805456 20.1172 0.345196 19.6485Z" fill="#1B1B1F"/>
                    </svg>
                  </div>
                </a>
              </div>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
      {if $client_version >= 2.4}
      <!-- Рекомендуемые товары -->
      <div class="margin-48-top overflow-hidden" *ngIf="product.recommendedProducts && product.recommendedProducts.length">
        <div class="container">
          <div class="product__section-title">{t}С этим товаром покупают{/t}</div>
          <swiper class="recommended-wrapper" [slidesPerView]="'auto'" [spaceBetween]="12">
            <ng-template swiperSlide *ngFor="let product of product.recommendedProducts">
              <div class="item-card recommended-product">
                <app-product-item [product]="product"></app-product-item>
              </div>
            </ng-template>
          </swiper>
        </div>
      </div>
      {/if}
    </div>
    <div class="skeleton" *ngIf="inLoading">
      <app-product-skeleton></app-product-skeleton>
    </div>
  </div>
</ion-content>
<ion-footer>
  <ion-toolbar>
    <div class="section" *ngIf="!inLoading && product">
      <div class="container">
        <div class="product-total" *ngIf="product.isNeedAdultButtonShow()">
          <div class="margin-16-right product-total__flex">
            <div class="product-total__price c-disabled-gray">
              { { product.getCost() } }
            </div>
          </div>
          <div class="margin-16-left product-total__flex d-flex ion-justify-content-end">
            <button type="button" class="item-card__notify" disabled>
              {t}Только в магазинах{/t}
            </button>
          </div>
        </div>
        <div *ngIf="!product.isNeedAdultButtonShow()">
          <div class="product-total" *ngIf="product.isBuyButtonShow()">
            <div class="margin-16-right product-total__flex">
              <div class="product-total__caption">{t}В наличии много{/t}</div>
              <div class="product-total__price">
                { { product.getCost() } }
              </div>
            </div>
            <div class="margin-16-left product-total__flex d-flex ion-justify-content-end">
              <button
                type="button"
                class="button button_primary product-total__button"
                [ngClass]="offerInCart ? 'bgc-primary-shady' : ''"
                (click)="addToCart(product)"
                *ngIf="!product['amountInCart'] || product['amountInCart'] && product.isOffersUse()"
              >
                <svg *ngIf="offerInCart" width="16" height="16" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                  <path d="M480 128c0 8.188-3.125 16.38-9.375 22.62l-256 256C208.4 412.9 200.2 416 192 416s-16.38-3.125-22.62-9.375l-128-128C35.13 272.4 32 264.2 32 256c0-18.28 14.95-32 32-32c8.188 0 16.38 3.125 22.62 9.375L192 338.8l233.4-233.4C431.6 99.13 439.8 96 448 96C465.1 96 480 109.7 480 128z" fill="var(--rs-color-white)"/>
                </svg>
                <svg *ngIf="!offerInCart" width="24" height="18" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2.9195 11.5445C2.9195 11.7961 3.12343 12 3.375 12C3.62657 12 3.83051 11.7961 3.83051 11.5445V9.0782H6.2968C6.5471 9.0782 6.75 8.87529 6.75 8.625C6.75 8.37471 6.5471 8.1718 6.2968 8.1718H3.83051V5.7055C3.83051 5.45394 3.62657 5.25 3.375 5.25C3.12343 5.25 2.9195 5.45394 2.9195 5.7055V8.1718H0.453199C0.202904 8.1718 0 8.37471 0 8.625C0 8.87529 0.202904 9.0782 0.453199 9.0782H2.9195V11.5445Z" fill="white"/>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1735 12.0766C11.1735 11.9524 11.2742 11.8516 11.3985 11.8516H21.9857C23.095 11.8516 24 11.2496 24 10.125V6C24 5.25 23.3852 5.11813 23.0147 5.0663L11.5002 3.45117C11.3149 3.42525 11.1771 3.26681 11.1771 3.07979V1.81443C11.1771 1.51423 10.9981 1.24294 10.7221 1.12487L8.18489 0.0395371C7.9331 -0.0677448 7.64482 0.0506352 7.539 0.305892C7.43318 0.561149 7.54995 0.853399 7.80173 0.960681L9.73324 1.78705C10.0092 1.90513 10.1882 2.1764 10.1882 2.47659V12.9245C10.1882 13.841 10.7882 14.6185 11.6114 14.8751C11.7654 14.9231 11.8535 15.1035 11.7954 15.254C11.7078 15.4808 11.6588 15.7272 11.6588 15.9838C11.6588 17.0974 12.5528 18 13.6476 18C14.7423 18 15.6363 17.0937 15.6363 15.9838C15.6363 15.7539 15.5982 15.5311 15.5274 15.3243C15.4709 15.1595 15.583 14.9665 15.7572 14.9665H19.4005C19.5742 14.9665 19.6858 15.158 19.6291 15.3223C19.5575 15.5299 19.5189 15.7525 19.5189 15.9838C19.5189 17.0974 20.413 18 21.5077 18C22.6024 18 23.4964 17.0937 23.4964 15.9838C23.4964 14.874 22.6024 13.9677 21.5077 13.9677H12.2025C11.6333 13.9677 11.1735 13.4979 11.1735 12.9245V12.0766ZM13.6512 16.9975C14.2022 16.9975 14.6547 16.5424 14.6547 15.9801C14.6547 15.4178 14.2022 14.9628 13.6512 14.9628C13.1002 14.9628 12.6477 15.4215 12.6477 15.9801C12.6477 16.5387 13.1002 16.9975 13.6512 16.9975ZM21.5113 16.9975C22.0623 16.9975 22.5148 16.5424 22.5148 15.9801C22.5148 15.4178 22.0623 14.9628 21.5113 14.9628C20.9603 14.9628 20.5078 15.4215 20.5078 15.9801C20.5078 16.5387 20.9603 16.9975 21.5113 16.9975ZM12.2025 10.875H21.9857C22.5513 10.875 23.0147 10.7058 23.0147 10.125V6.55823C23.0147 6.1841 22.7119 5.90427 22.3413 5.85253L12 4.60457C11.5002 4.5 11.1735 4.60457 11.1735 5.27421V9.80959C11.1771 10.875 11.25 10.875 12.2025 10.875Z" fill="white"/>
                </svg>

              </button>
              <div class="cart-amount cart-amount_card product-total__amount w-100" *ngIf="product['amountInCart'] && !product.isOffersUse()">
                <button
                  type="button"
                  (click)="decreaseQuantityCartItem(product)"
                >
                  <svg width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.4752 6.46875H1.47516V4.96875H10.4752V6.46875Z"/>
                  </svg>
                </button>
                <div class="cart-amount__input" (click)="openSetAmount(product)">
                  <span>{ { product.amountInCart } }</span>
                  <span *ngIf="product.getUnitLiter()">{ { product.getUnitLiter() } }</span>
                </div>
                <button
                  type="button"
                  (click)="increaseQuantityCartItem(product)"
                >
                  <svg width="12" height="12" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.7326 6.94364H6.87549V10.8008H5.58978V6.94364H1.73264V5.65792H5.58978V1.80078H6.87549V5.65792H10.7326V6.94364Z"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          <div class="product-total" *ngIf="product.isReservationButtonShow()">
            <div class="margin-16-right product-total__flex">
              <div
                class="product-total__caption c-dark-red"
                {if $client_version >= 3.7}
                  *ngIf="product.getCost() != '{t}Цена по запросу{/t}'"
                {/if}
              >{t}Нет в наличии{/t}</div>
              <div class="product-total__price">
                { { product.getCost() } }
              </div>
            </div>
            <div class="margin-16-left product-total__flex d-flex ion-justify-content-end">
              <button
                type="button"
                class="product-total__notify"
                (click)="openReservation(product)"
                {if $client_version >= 3.7}
                  [innerHTML]="product.getCost() == '{t}Цена по запросу{/t}' ? '{t}Узнать цену{/t}' : '{t}Уведомить о поступлении{/t}'"
                {/if}
              > {t}Уведомлять{/t} <br>{t}о&nbsp;поступлении{/t}</button>
            </div>
          </div>

          <div class="product-total" *ngIf="product.isNotStocksButtonShow()">
            <div class="margin-16-right product-total__flex">
              <div class="product-total__caption c-dark-red">{t}Нет в наличии{/t}</div>
              <div class="product-total__price">
                { { product.getCost() } }
              </div>
            </div>
            <div class="margin-16-left product-total__flex d-flex ion-justify-content-end">
              <button
                type="button"
                class="product-total__notify"
                disabled
              >
                <svg width="24" height="18" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M2.9195 11.5445C2.9195 11.7961 3.12343 12 3.375 12C3.62657 12 3.83051 11.7961 3.83051 11.5445V9.0782H6.2968C6.5471 9.0782 6.75 8.87529 6.75 8.625C6.75 8.37471 6.5471 8.1718 6.2968 8.1718H3.83051V5.7055C3.83051 5.45394 3.62657 5.25 3.375 5.25C3.12343 5.25 2.9195 5.45394 2.9195 5.7055V8.1718H0.453199C0.202904 8.1718 0 8.37471 0 8.625C0 8.87529 0.202904 9.0782 0.453199 9.0782H2.9195V11.5445Z" fill="var(--rs-color-gray-shady)"/>
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.1735 12.0766C11.1735 11.9524 11.2742 11.8516 11.3985 11.8516H21.9857C23.095 11.8516 24 11.2496 24 10.125V6C24 5.25 23.3852 5.11813 23.0147 5.0663L11.5002 3.45117C11.3149 3.42525 11.1771 3.26681 11.1771 3.07979V1.81443C11.1771 1.51423 10.9981 1.24294 10.7221 1.12487L8.18489 0.0395371C7.9331 -0.0677448 7.64482 0.0506352 7.539 0.305892C7.43318 0.561149 7.54995 0.853399 7.80173 0.960681L9.73324 1.78705C10.0092 1.90513 10.1882 2.1764 10.1882 2.47659V12.9245C10.1882 13.841 10.7882 14.6185 11.6114 14.8751C11.7654 14.9231 11.8535 15.1035 11.7954 15.254C11.7078 15.4808 11.6588 15.7272 11.6588 15.9838C11.6588 17.0974 12.5528 18 13.6476 18C14.7423 18 15.6363 17.0937 15.6363 15.9838C15.6363 15.7539 15.5982 15.5311 15.5274 15.3243C15.4709 15.1595 15.583 14.9665 15.7572 14.9665H19.4005C19.5742 14.9665 19.6858 15.158 19.6291 15.3223C19.5575 15.5299 19.5189 15.7525 19.5189 15.9838C19.5189 17.0974 20.413 18 21.5077 18C22.6024 18 23.4964 17.0937 23.4964 15.9838C23.4964 14.874 22.6024 13.9677 21.5077 13.9677H12.2025C11.6333 13.9677 11.1735 13.4979 11.1735 12.9245V12.0766ZM13.6512 16.9975C14.2022 16.9975 14.6547 16.5424 14.6547 15.9801C14.6547 15.4178 14.2022 14.9628 13.6512 14.9628C13.1002 14.9628 12.6477 15.4215 12.6477 15.9801C12.6477 16.5387 13.1002 16.9975 13.6512 16.9975ZM21.5113 16.9975C22.0623 16.9975 22.5148 16.5424 22.5148 15.9801C22.5148 15.4178 22.0623 14.9628 21.5113 14.9628C20.9603 14.9628 20.5078 15.4215 20.5078 15.9801C20.5078 16.5387 20.9603 16.9975 21.5113 16.9975ZM12.2025 10.875H21.9857C22.5513 10.875 23.0147 10.7058 23.0147 10.125V6.55823C23.0147 6.1841 22.7119 5.90427 22.3413 5.85253L12 4.60457C11.5002 4.5 11.1735 4.60457 11.1735 5.27421V9.80959C11.1771 10.875 11.25 10.875 12.2025 10.875Z" fill="var(--rs-color-gray-shady)"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-footer>