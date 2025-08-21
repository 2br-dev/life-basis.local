  <div class="index-bonus_card" *ngIf="!inLoading && !cardReady">
    <a (click)="openAddBonusCard()" class="index-bonus_card" *ngIf="!cardReady">
      <div>
        <div class="index-bonus_card__title">{t}Выпустить виртуальную карту{/t}</div>
        <div class="index-bonus_card__subtitle">{t}Копите баллы и экономьте{/t}</div>
      </div>
      <div class="index-bonus_card__arrow">
        <svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12.1618 8.30514L12.162 8.30531C12.3462 8.48921 12.4503 8.739 12.4503 9.00003C12.4503 9.26043 12.3468 9.51068 12.1618 9.69488L12.1618 8.30514ZM12.1618 8.30514L5.22386 1.38639L5.22377 1.38631C4.84007 1.00389 4.21894 1.00461 3.83604 1.38821L3.83594 1.3883C3.45306 1.77213 3.45417 2.39365 3.83785 2.77629L3.83785 2.7763L10.0788 9.00002L3.83763 15.2237L3.83757 15.2238C3.45397 15.6064 3.45286 16.2277 3.83567 16.6115L3.8358 16.6116C4.02748 16.8037 4.27964 16.9 4.53072 16.9C4.78107 16.9 5.03232 16.8043 5.22374 16.6135L5.22382 16.6135L12.1615 9.69521L12.1618 8.30514Z" fill="#49B34C" stroke="#49B34C" stroke-width="0.8"/>
        </svg>
      </div>
    </a>
  </div>
  <div class="index-bonus_card" *ngIf="!inLoading && cardReady && bonusCard">
    <a (click)="navigateTo('/tabs/catalog/bonuscard/show')" class="index-bonus_card index-bonus_card__ready" *ngIf="cardReady && bonusCard">
      <div class="index-bonus_card__barcode">
        <img [src]="bonusCard.barcodeUrl" width="672" height="400">
      </div>
    </a>
    <div class="index-bonus_card__subtitle index-bonus_card__ready__subtitle" *ngIf="showHint">{t}Копите баллы и экономьте{/t}</div>
  </div>
  <div *ngIf="inLoading">
    <app-promo-skeleton></app-promo-skeleton>
  </div>



