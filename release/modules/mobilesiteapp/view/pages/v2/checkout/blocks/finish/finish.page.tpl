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
        <div class="head__title">{t}Детали заказа{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div class="section" id="checkout-finish">
    <div *ngIf="inLoading">
      <app-finish-skeleton></app-finish-skeleton>
    </div>
    <div class="container" *ngIf="!inLoading && order">
      <ion-grid>
        <ion-row>
          <ion-col size-lg="6" size-md="8" offset-md="2" offset-lg="3" size="12">
            <div>
              <div class="c-dark margin-32-bottom">
                {t}Заказ №{/t} { { order.orderNum } } {t}успешно создан.{/t}
                {t}В ближайшее время мы свяжемся с вами для уточнения деталей заказа.{/t}
              </div>

              <div class="margin-40-top margin-40-bottom" *ngIf="order.isCanOnlinePay()">
                <button
                  (click)="goToPay(order)"
                  class="button button_primary w-100"
                  [attr.disabled]="order.formIsLoading ? '' : null"
                >{t}Оплатить{/t}
                  <span *ngIf="order.formIsLoading" class="formIsLoading">
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

              <div *ngIf="order.productItems && order.productItems.length">
                <h3>{t}Состав заказа:{/t}</h3>

                <div *ngIf="!showAllProducts">
                  <div class="order-item-images margin-24-top">
                    <div class="order-item-image" *ngFor="let productItem of order.productItems; let i=index">
                      <div *ngIf="productItem.getImage() && i < 5">
                        <canvas width="56" height="48"></canvas>
                        <ion-img [src]="productItem.getImage()['small_url']" alt=""></ion-img>
                      </div>
                    </div>
                    <div class="order-item-images__more" *ngIf="order.getProductNum(5)">+{ { order.getProductNum(5) } }</div>
                  </div>
                  <div class="d-flex ion-justify-content-center margin-32-bottom" *ngIf="showAllProductsButton(5)">
                    <button type="button" class="order-more">
                      <span class="margin-8-right" (click)="showAllProducts = !showAllProducts">{t}Показать все{/t}</span>
                      <svg width="21" height="20" viewBox="0 0 21 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.27241 7.68306C5.52625 7.43898 5.93781 7.43898 6.19165 7.68306L10.4987 11.8245L14.8057 7.68306C15.0596 7.43898 15.4711 7.43898 15.725 7.68306C15.9788 7.92714 15.9788 8.32286 15.725 8.56694L10.9583 13.1503C10.7045 13.3944 10.2929 13.3944 10.0391 13.1503L5.27241 8.56694C5.01857 8.32286 5.01857 7.92714 5.27241 7.68306Z" />
                      </svg>
                    </button>
                  </div>
                </div>
                <div *ngIf="showAllProducts">
                  <div class="margin-32-bottom">
                    <div class="order-product" *ngFor="let productItem of order.productItems; let i=index">
                      <div class="order-product__info">
                        <div class="order-product__img" *ngIf="productItem.getImage()">
                          <canvas width="56" height="48"></canvas>
                          <ion-img [src]="productItem.getImage()['small_url']" alt=""></ion-img>
                        </div>

                        <div class="order-product__title_offer">
                          <div class="order-product__title" [innerHTML]="productItem.title"></div>
                          <div class="order-product__offers fz-12 margin-8-top" *ngIf="productItem.offerValues">
                            <div *ngFor="let item of productItem.offerValues" class="c-primary-shady">
                              <span>{ { item.title } }: { { item.value } }</span>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="order-product__price-wrap">
                        <div class="fw-medium margin-24-right">
                          { { productItem.amount } }
                          <span *ngIf="productItem.getUnitLiter()" [innerHTML]="productItem.getUnitLiter()"></span>
                        </div>
                        <div class="product-cost-wrap">
                          <div
                            class="order-product__price-old"
                            *ngIf="productItem.costFormatted && productItem.priceFormatted && productItem.priceFormatted !== productItem.costFormatted"
                          >{ { productItem.priceFormatted } }</div>
                          <div class="order-product__price">
                            <span *ngIf="productItem.costFormatted">{ { productItem.costFormatted } }</span>
                            <span *ngIf="!productItem.costFormatted">{ { productItem.priceFormatted } }</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="d-flex ion-justify-content-center margin-32-bottom" *ngIf="showAllProductsButton(5)">
                    <button type="button" class="order-more order-more_revert">
                      <span class="margin-8-right" (click)="showAllProducts = !showAllProducts">{t}Свернуть{/t}</span>
                      <svg width="21" height="20" viewBox="0 0 21 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M5.27241 7.68306C5.52625 7.43898 5.93781 7.43898 6.19165 7.68306L10.4987 11.8245L14.8057 7.68306C15.0596 7.43898 15.4711 7.43898 15.725 7.68306C15.9788 7.92714 15.9788 8.32286 15.725 8.56694L10.9583 13.1503C10.7045 13.3944 10.2929 13.3944 10.0391 13.1503L5.27241 8.56694C5.01857 8.32286 5.01857 7.92714 5.27241 7.68306Z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <div class="order-info">
                <div class="order-info__item">
                  <div class="order-info__key">{t}Заказчик:{/t}</div>
                  <div class="c-dark">{ { order.getUserFio() } }</div>
                </div>
                <div class="order-info__item">
                  <div class="order-info__key">{t}Доставка:{/t}</div>
                  <div class="c-dark">{ { order.getDelivery()['title'] } }</div>
                </div>
                <div class="order-info__item">
                  <div class="order-info__key">{t}Адрес доставки:{/t}</div>
                  <div class="c-dark">{ { order.getAddressLine() } }</div>
                </div>
                <!--<div class="order-info__item">
                  <div class="order-info__key">Дата доставки:</div>
                  <div class="c-dark">05.04.2020 г.</div>
                </div>
                <div class="order-info__item">
                  <div class="order-info__key">Время доставки: </div>
                  <div class="c-dark">с 15:00 до 20:00</div>
                </div>-->
                <div class="order-info__item">
                  <div class="order-info__key">{t}Способ оплаты:{/t}</div>
                  <div class="c-dark">{ { order.getPayment()['title'] } }</div>
                </div>
                <div *ngIf="order.getOrderDocs()">
                  <div class="order-info__item">
                    <div class="order-info__key">{t}Документы к заказу:{/t}</div>
                    <div class="c-dark">
                      <a
                        class="order-item__receipt"
                        *ngFor="let doc of order.getOrderDocs()"
                        (click)="openDocument(doc.link)"
                      >{ { doc.title } }</a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="order-total">
                <div *ngIf="order.otherItems && order.otherItems.length">
                  <div
                    class="order-total__item"
                    [ngClass]="item.type === 'order_discount' ? 'c-dark-red' : ''"
                    *ngFor="let item of order.otherItems"
                  >
                    <div class="fw-medium margin-16-right">{ { item.title } }</div>
                    <div class="fw-semibold text-nowrap">
                      <span *ngIf="item.type === 'order_discount' && item.getCost()">-</span>
                      { { item.getCost() } }
                    </div>
                  </div>
                </div>
                <div class="order-total__sum">
                  <div class="margin-16-right">{t}Итого{/t}</div>
                  <div class="text-nowrap">{ { order.totalcostFormatted } }</div>
                </div>
              </div>
              <div class="margin-40-top margin-40-bottom">
                <button (click)="navigateTo('/tabs/catalog')" class="button button_primary w-100">{t}Вернуться в каталог{/t}</button>
              </div>
            </div>
          </ion-col>
        </ion-row>
      </ion-grid>
    </div>
  </div>
</ion-content>