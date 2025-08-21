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
        <div class="head__title">{t}Детали доставки{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true" {if $client_version >= 3.1} (click)="hideDeliveriesDescription($event)" {/if}>
  <div id="checkout-delivery" class="container">
    <ion-grid>
      <ion-row>
        <ion-col size-lg="6" size-md="8" offset-md="2" offset-lg="3" size="12">
          <app-delivery-skeleton *ngIf="inLoading"></app-delivery-skeleton>
          <div *ngIf="!inLoading">
            <div>
              <div>
                <h3 class="margin-24-bottom margin-24-top">{t}Способ доставки{/t}</h3>
                <div class="margin-24-bottom">
                  <div class="checkout-label">{t}Город{/t}</div>
                  <input type="text" class="form-control address-input" [value]="currentCity ? currentCity.getLabel() : null" (focus)="openCity()">
                </div>
                <div *ngIf="!deliveries">
                  <p>{t}Выберите город{/t}</p>
                </div>
                <div *ngIf="deliveries">
                  <div class="delivery-grid" >
                    <div class="delivery-card" *ngFor="let delivery of deliveries">
                      <input type="radio"
                        id="delivery_{ { delivery.id } }"
                        (change)="setCurrentDelivery(delivery)"
                        [attr.checked]="currentDelivery && currentDelivery.id == delivery.id ? '' : null"
                        [attr.disabled]="delivery.error ? '' : null"
                        name="delivery"
                      >
                      <label for="delivery_{ { delivery.id } }">
                        <span class="delivery-card__icon">
                          <svg width="49" height="48" viewBox="0 0 49 48" xmlns="http://www.w3.org/2000/svg" *ngIf="!delivery.getImage()">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M37.4213 4.09618C38.1321 3.84258 38.9229 4.10288 39.3497 4.73101L44.2071 11.8792C44.3979 12.16 44.5 12.4926 44.5 12.8332V35.7746C44.5 36.489 44.0544 37.1257 43.388 37.3634L25.0547 43.904C24.6957 44.032 24.3043 44.032 23.9453 43.904L5.612 37.3634C4.94563 37.1257 4.5 36.489 4.5 35.7746V13.0066C4.5 12.2922 4.94563 11.6555 5.612 11.4178L23.9453 4.87724C24.3043 4.74919 24.6957 4.74919 25.0547 4.87724L30.1433 6.69265L37.4213 4.09618ZM9.47613 12.6908L24.5 7.33089L26.4272 8.01841L19.4917 10.4927C18.4578 10.8615 18.0518 12.1218 18.6726 13.0354L21.3064 16.9113L9.47613 12.6908ZM8.13063 14.8623C7.58845 14.6688 7.01996 15.0754 7.01996 15.6566V34.0221C7.01996 34.7365 7.4656 35.3732 8.13197 35.6109L22.1294 40.6046C22.6715 40.798 23.24 40.3914 23.24 39.8102V21.4447C23.24 20.7304 22.7944 20.0937 22.128 19.8559L8.13063 14.8623ZM26.872 19.8559C26.2056 20.0937 25.76 20.7304 25.76 21.4447V39.8102C25.76 40.3914 26.3285 40.798 26.8706 40.6046L40.868 35.6109C41.5344 35.3732 41.98 34.7365 41.98 34.0221V15.6566C41.98 15.0754 41.4116 14.6688 40.8694 14.8623L26.872 19.8559ZM37.9509 5.6857L42.5666 12.4783L24.6782 18.8601L20.0625 12.0675L37.9509 5.6857Z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M42.5666 12.4783L37.9509 5.6857L20.0625 12.0675L24.6782 18.8601L42.5666 12.4783ZM24.8376 18.3668L41.9297 12.2691L37.7915 6.1792L20.6994 12.2769L24.8376 18.3668Z"/>
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M41.9297 12.2691L24.8376 18.3668L20.6994 12.2769L37.7915 6.1792L41.9297 12.2691ZM21.3364 12.4861L24.9971 17.8733L41.2927 12.0597L37.632 6.67254L21.3364 12.4861Z"/>
                            <path d="M9.47613 12.6908L24.5 7.33089L26.4272 8.01841L19.4917 10.4927C18.4578 10.8615 18.0518 12.1218 18.6726 13.0354L21.3064 16.9113L9.47613 12.6908Z"/>
                          </svg>
                          <span *ngIf="delivery.getImage()">
                            <img [src]="delivery.getImage()['small_url']" alt="">
                          </span>
                        </span>
                        <span class="delivery-card__desc">
                        <span class="delivery-card__title">
                          { { delivery.title } }
                        </span>
                        <span class="delivery-card__price" *ngIf="delivery.error">{t}Недоступно{/t}</span>
                        <span class="delivery-card__price" *ngIf="!delivery.error">{ { delivery.cost } }</span>
                          {if $client_version >= 3.1}
                            <div *ngIf="delivery.error">
                              <span class="delivery-card__info" style="opacity: 1;visibility: visible;" *ngIf="delivery.error" (click)="delivery.showDeliveryDescription()">
                              <svg width="18" height="19" viewBox="0 0 18 19" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 17.375C13.3492 17.375 16.875 13.8492 16.875 9.5C16.875 5.15076 13.3492 1.625 9 1.625C4.65076 1.625 1.125 5.15076 1.125 9.5C1.125 13.8492 4.65076 17.375 9 17.375ZM9 18.5C13.9706 18.5 18 14.4706 18 9.5C18 4.52944 13.9706 0.5 9 0.5C4.02944 0.5 0 4.52944 0 9.5C0 14.4706 4.02944 18.5 9 18.5Z" />
                                <path d="M9 4.73529C8.61166 4.73529 8.05078 5.1132 8.05078 5.50154V10.0294C8.05078 10.4177 8.61166 10.8558 9 10.8558C9.38834 10.8558 9.94922 10.4177 9.94922 10.0294V5.50154C9.94922 5.1132 9.38834 4.73529 9 4.73529Z"/>
                                <path d="M9 13.7243C9.52424 13.7243 9.94922 13.2993 9.94922 12.7751C9.94922 12.2509 9.52424 11.8259 9 11.8259C8.47576 11.8259 8.05078 12.2509 8.05078 12.7751C8.05078 13.2993 8.47576 13.7243 9 13.7243Z"/>
                              </svg>
                              </span>
                              <span *ngIf="delivery.error">
                                <span
                                  class="delivery-card__info-content"
                                  [ngClass]="delivery.showDescription ? 'delivery-card__info-content_active' : ''"
                                  [innerHTML]="delivery.error"
                                >
                                </span>
                              </span>
                              </div>
                            <div *ngIf="!delivery.error">
                              <span class="delivery-card__info" *ngIf="delivery.description" (click)="delivery.showDeliveryDescription()">
                              <svg width="18" height="19" viewBox="0 0 18 19" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9 17.375C13.3492 17.375 16.875 13.8492 16.875 9.5C16.875 5.15076 13.3492 1.625 9 1.625C4.65076 1.625 1.125 5.15076 1.125 9.5C1.125 13.8492 4.65076 17.375 9 17.375ZM9 18.5C13.9706 18.5 18 14.4706 18 9.5C18 4.52944 13.9706 0.5 9 0.5C4.02944 0.5 0 4.52944 0 9.5C0 14.4706 4.02944 18.5 9 18.5Z" />
                                <path d="M9 4.73529C8.61166 4.73529 8.05078 5.1132 8.05078 5.50154V10.0294C8.05078 10.4177 8.61166 10.8558 9 10.8558C9.38834 10.8558 9.94922 10.4177 9.94922 10.0294V5.50154C9.94922 5.1132 9.38834 4.73529 9 4.73529Z"/>
                                <path d="M9 13.7243C9.52424 13.7243 9.94922 13.2993 9.94922 12.7751C9.94922 12.2509 9.52424 11.8259 9 11.8259C8.47576 11.8259 8.05078 12.2509 8.05078 12.7751C8.05078 13.2993 8.47576 13.7243 9 13.7243Z"/>
                              </svg>
                              </span>
                              <span *ngIf="delivery.description">
                                <span
                                  class="delivery-card__info-content"
                                  [ngClass]="delivery.showDescription ? 'delivery-card__info-content_active' : ''"
                                  [innerHTML]="delivery.description"
                                >
                                </span>
                              </span>
                              </div>
                          {/if}
                          </span>
                      </label>
                    </div>
                  </div>
                  <div class="margin-16-top" *ngIf="this.currentDelivery && this.currentDelivery.additionalField.length > 0">
                    <div *ngIf="addresses && addresses.length > 0">
                      <div class="checkout-label">{t}Адрес доставки:{/t}</div>
                      <select class="form-select" (change)="setCurrentAddress($event)">
                        <option
                          [value]="address.id"
                          [attr.selected]="useAddr && address.id == useAddr.id ? '' : null"
                          *ngFor="let address of addresses"
                        >
                          { { address.fullAddress } }
                        </option>
                        <option value="newAddress">{t}Другой адрес{/t}</option>
                      </select>
                    </div>
                    <div class="address-field margin-16-top" *ngFor="let field of this.currentDelivery.additionalField">
                      <div *ngIf="field.name == 'address' && needShowAddressInput">
                        <div *ngIf="autocompleteAddressList && autocompleteAddressList.length" class="autocomplete-address-list__wrapper">
                          <div
                            class="autocomplete-address-list__item"
                            *ngFor="let item of autocompleteAddressList"
                            [innerHTML]="item.label"
                            (click)="chooseAddress(item)"
                          ></div>
                        </div>
                        <div class="checkout-label">{ { field.description } }</div>
                        <input
                          type="text"
                          [(ngModel)]="field.value"
                          [value]="field.value"
                          (input)="autocompleteAddress($event)"
                          (blur)="clearAutocompleteAddress()"
                          class="form-control address-input"
                        >
                      </div>
                      <div *ngIf="field.name != 'address' && needShowAddressInput">
                        <div class="checkout-label">{ { field.description } }</div>
                        <input
                          type="text"
                          [(ngModel)]="field.value"
                          [value]="field.value"
                          class="form-control address-input"
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div
                *ngIf="currentDelivery && currentDelivery.mobilesiteappDescription"
                class="margin-48-top delivery-desc"
                [innerHTML]="currentDelivery.mobilesiteappDescription | safeHtml"
              ></div>
            </div>
            <div class="margin-16-top" *ngIf="errors && errors.length">
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
          </div>
          <div class="margin-32-top" #additionalHTML></div>
          {if $client_version >= 2.5}
            <div class="margin-32-top">
              <div class="checkout-label">{t}Комментарий к заказу{/t}</div>
              <textarea class="form-control" [(ngModel)]="comment"></textarea>
            </div>
          {/if}
        </ion-col>
      </ion-row>
    </ion-grid>
  </div>
</ion-content>
<ion-footer>
  <ion-toolbar>
    <div class="section">
      <div class="container">
        <button
          class="button button_primary w-100"
          [attr.disabled]="!currentDelivery ? '' : null"
          (click)="nextStep()"
        >{t}Продолжить оформление{/t}</button>
      </div>
    </div>
  </ion-toolbar>
</ion-footer>