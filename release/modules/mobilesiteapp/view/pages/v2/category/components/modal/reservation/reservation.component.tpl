{if $client_version <= 3.7}
{literal}
  <div id="modal-reservation">
    <ion-header>
      <div class="container">
        <div class="modal-reservation-head">
          <div class="modal-reservation-head__flex-1">
            <button type="button" class="modal-close" (click)="dismissModal()">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="modal-reservation-head__title">
            Заказать
          </div>
          <div class="modal-reservation-head__flex-1"></div>
        </div>
      </div>
    </ion-header>
    <div class="inner-content">
      <div class="container">
        <form>
          <p>В данный момент товара нет в наличии. Заполните форму и мы оповестим вас о поступлении товара.</p>
          <div class="item-card ion-margin-bottom">
            <div class="modal-reservation__product-title">{{product.title}}</div>
            <div *ngIf="product.isOffersUse() || product.isMultiOffersUse()">
              <div *ngIf="product.isMultiOffersUse()">
                <ul class="modal-reservation__multioffers-list">
                  <li *ngFor="let item of (product.offer.propsdataArr | keyvalue)">
                    {{item.key}}: {{item.value}}
                  </li>
                </ul>
              </div>
              <div *ngIf="!product.isMultiOffersUse() && product.isOffersUse()">
                <div class="product__section-subtitle">
                  {{product.getOfferCaption()}}: <span>{{product.offer.title}}</span>
                </div>
              </div>
            </div>
          </div>
          <div class="margin-24-bottom">
            <label for="phone" class="form-label">Телефон</label>
            <input type="text" class="form-control" name="phone" id="phone" [(ngModel)]="data.phone">
          </div>
          <div class="margin-24-bottom">
            <label for="email" class="form-label">E-mail</label>
            <input type="text" class="form-control" name="email" id="email" [(ngModel)]="data.email" [email]="true">
          </div>
        </form>
      </div>
    </div>
    <ion-footer>
      <div class="container">
        <div class="modal-footer-button">
          <button
                  type="button"
                  class="button button_primary button_small w-100 ion-margin-bottom"
                  [attr.disabled]="data.email.length || data.phone.length ? null : ''"
                  (click)="reserve(product)"
          >Оповестить меня</button>
        </div>
      </div>
    </ion-footer>
  </div>
{/literal}
{else}
  <ion-header>
    <ion-toolbar>
      <div class="container">
        <div class="modal-reservation-head">
          <div class="modal-reservation-head__flex-1">
            <button type="button" class="modal-close" (click)="dismissModal()">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="modal-reservation-head__title">
            {t}Заказать{/t}
          </div>
          <div class="modal-reservation-head__flex-1"></div>
        </div>
      </div>
    </ion-toolbar>
  </ion-header>
  <ion-content>
    <div class="container">
      <form>
        <p>{t}В данный момент товара нет в наличии. Заполните форму и мы оповестим вас о поступлении товара.{/t}</p>
        <div class="item-card ion-margin-bottom">
          <div class="modal-reservation__product-title">{ { product.title } }</div>
          <div *ngIf="product.isOffersUse() || product.isMultiOffersUse()">
            <div *ngIf="product.isMultiOffersUse()">
              <ul class="modal-reservation__multioffers-list">
                <li *ngFor="let item of (product.offer.propsdataArr | keyvalue)">
                  { { item.key } }: { { item.value } }
                </li>
              </ul>
            </div>
            <div *ngIf="!product.isMultiOffersUse() && product.isOffersUse()">
              <div class="product__section-subtitle">
                { { product.getOfferCaption() } }: <span>{ { product.offer.title } }</span>
              </div>
            </div>
          </div>
        </div>
        <div class="margin-24-bottom">
          <label for="phone" class="form-label">{t}Телефон{/t}</label>
          <input type="text" class="form-control" name="phone" id="phone" [(ngModel)]="data.phone">
        </div>
        <div class="margin-24-bottom">
          <label for="email" class="form-label">{t}E-mail{/t}</label>
          <input type="text" class="form-control" name="email" id="email" [(ngModel)]="data.email" [email]="true">
        </div>
      </form>
    </div>
  </ion-content>
  <ion-footer>
    <div class="container">
      <div class="modal-footer-button">
        <button
                type="button"
                class="button button_primary button_small w-100 ion-margin-bottom"
                [attr.disabled]="data.email.length || data.phone.length ? null : ''"
                (click)="reserve(product)"
        >{t}Оповестить меня{/t}</button>
      </div>
    </div>
  </ion-footer>
{/if}