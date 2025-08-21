<div id="modal-addtocart">
  <ion-header>
    <div class="container">
      <div class="modal-addtocart-head">
        <div class="modal-addtocart-head__flex-1">
          <button type="button" class="modal-close" (click)="dismissModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <div class="modal-addtocart-head__title" [innerHTML]="product.title"></div>
        <div class="modal-addtocart-head__flex-1"></div>
      </div>
    </div>
  </ion-header>
  <div class="inner-content">
    <div class="container" *ngIf="product">
      {if $client_version >= 3.7}
        <div class="product__price margin-10-bottom">
          <span>{t}Цена:{/t} </span>
          <span class="price" [innerHTML]="product.getCost()"></span>
        </div>
      {/if}

      <!-- Комплектации -->
      <div class="offersWrapper" *ngIf="product.isOffersUse() || product.isMultiOffersUse() || product.isVirtualMultiOffersUse()">
        <!-- Многомерные комплектации -->
        <div class="multiOffers" *ngIf="product.isMultiOffersUse() || product.isVirtualMultiOffersUse()">
          <div class="multiOffer" *ngFor="let multioffer of product.multiOffers">
            <!--Список в виде цветов и картинок многомерных комплектаций-->
            <div class="margin-10-bottom" *ngIf="multioffer.propertyType == 'color' || multioffer.propertyType == 'image'">
              <div class="product__section-subtitle margin-10-bottom" [innerHTML]="multioffer.title"></div>
              <ul class="item-product-choose">
                <li *ngFor="let multiofferValue of multioffer.values">
                  <div class="radio-color" [ngClass]="multioffer.propertyType == 'color' ? 'radio-color' : 'radio-image radio-image_cover'">
                    <input
                      type="radio"
                      [attr.id]="multiofferValue.id ? multiofferValue.id : multiofferValue.value"
                      [name]="'multioffer_' + multioffer.propId + '[]'"
                      [attr.checked]="multioffer.isCurrent(multiofferValue)"
                      (change)="multioffer.setCurrentValue(multiofferValue); changeMultiOffer()"
                    >
                    <label [attr.for]="multiofferValue.id ? multiofferValue.id : multiofferValue.value">
                      <ion-img
                        *ngIf="multioffer.propertyType == 'image'"
                        [src]="multiofferValue.images.micro_url"
                      ></ion-img>
                      <span
                        *ngIf="multioffer.propertyType == 'color'"
                        class="radio-bg-color"
                        [style.background]="multiofferValue.color"
                      ></span>
                    </label>
                  </div>
                </li>
              </ul>
            </div>

            <!--Простой список многомерных комплектаций-->
            <div class="margin-10-bottom" *ngIf="multioffer.propertyType == 'list'">
              <div class="product__section-subtitle margin-10-bottom" [innerHTML]="multioffer.title"></div>
              <ul class="item-product-choose">
                <li *ngFor="let multiofferValue of multioffer.values">
                  <div class="radio-image radio-image_txt">
                    <input
                      type="radio"
                      [id]="multiofferValue.id ? multiofferValue.id : multiofferValue.value"
                      [name]="'multioffer_' + multioffer.propId + '[]'"
                      [attr.checked]="multioffer.isCurrent(multiofferValue)"
                      (change)="multioffer.setCurrentValue(multiofferValue); changeMultiOffer()"
                    >
                    <label [for]="multiofferValue.id ? multiofferValue.id : multiofferValue.value" [innerHTML]="multiofferValue.value"></label>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Простые комплектации -->
        <div class="margin-10-bottom singleOffer" *ngIf="!product.isMultiOffersUse() && product.isOffersUse()">
          <div class="product__section-subtitle margin-10-bottom" [innerHTML]="product.getOfferCaption()"></div>
          <ul class="item-product-choose">
            <li *ngFor="let offer of product.offers">
              <div class="radio-image radio-image_txt">
                <input
                  type="radio"
                  [id]="offer.id"
                  [name]="'offer_' + product.offer.id + '[]'"
                  [value]="offer.title"
                  (change)="product.setOfferById(offer.id)"
                  [attr.checked]="product.offer.title === offer.title ? '' : null"
                >
                <label [for]="offer.id" [innerHTML]="offer.title"></label>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <ion-footer>
    <div class="container">
      <div class="modal-footer-button">
        <button type="button" class="button button_primary button_small w-100 ion-margin-bottom" *ngIf="product.isBuyButtonShow()" (click)="addToCartAndDismissModal()">{t}Добавить в корзину{/t}</button>
        <button type="button" class="item-card__notify w-100 ion-margin-bottom" *ngIf="product.isReservationButtonShow()" (click)="openReservation(product)" [innerHTML]="product.getCost() == '{t}Цена по запросу{/t}' ? '{t}Узнать цену{/t}' : '{t}Уведомить о поступлении{/t}'"></button>
        {if $client_version >= 3.7}
          <button type="button" class="button button_primary w-100 ion-margin-bottom" *ngIf="product.isNotStocksButtonShow()" disabled>{t}Нет в наличии{/t}</button>
        {/if}
      </div>
    </div>
  </ion-footer>
</div>
