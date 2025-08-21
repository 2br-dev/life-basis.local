<div id="modal-payment">
  <ion-header>
    <div class="container">
      <div class="modal-payment-head">
        <div class="modal-payment-head__flex-1">
          <button type="button" class="modal-close" (click)="dismissModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <div class="modal-payment-head__title">
          {t}Выберите способ оплаты{/t}
        </div>
        <div class="modal-payment-head__flex-1"></div>
      </div>
    </div>
  </ion-header>
  <div class="inner-content">
    <div class="section">
      <div class="container" *ngIf="inLoading">
        <app-modalpayment-skeleton></app-modalpayment-skeleton>
      </div>
      <div class="container" *ngIf="!inLoading && paymentList && paymentList.length">
        <ul class="payment-accordion">
          <li>
            <ul class="payment-list">
              <li *ngFor="let payment of paymentList">
                <div class="radio">
                  <input
                    type="radio"
                    id="payment_{ { payment.id } }"
                    name="payment[]"
                    (change)="setCurrentPayment(payment)"
                    [attr.checked]="payment.id === currentPayment.id ? '' : null"
                  >
                  <label for="payment_{ { payment.id } }">
                    <span class="radio-attr">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" d="M0 0h24v24H0V0z"/>
                        <path class="radio-attr__contour" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                        <circle class="radio-attr__check" cx="12" cy="12" r="5"/>
                      </svg>
                    </span>
                    <span>{ { payment.title } }</span>
                  </label>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <ion-footer>
    <div class="container">
      <div class="modal-footer-button">
        <button type="button" class="button button_primary button_small w-100 ion-margin-bottom" (click)="applyPayment()">{t}Изменить способ оплаты{/t}</button>
      </div>
    </div>
  </ion-footer>
</div>