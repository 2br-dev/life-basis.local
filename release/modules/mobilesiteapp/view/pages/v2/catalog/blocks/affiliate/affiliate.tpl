<div id="affiliate">
  <div *ngIf="!inLoading">
    <button class="index-head__city" type="button" (click)="navigateTo('/tabs{if $client_version >= 3.5}/catalog{/if}/affiliate')">
      <svg width="24" height="25" viewBox="0 0 24 25" xmlns="http://www.w3.org/2000/svg">
        <path d="M11.9995 0.5C7.20677 0.5 3.30762 4.39916 3.30762 9.19184C3.30762 15.1397 11.086 23.8715 11.4172 24.2404C11.7282 24.5868 12.2713 24.5862 12.5818 24.2404C12.913 23.8715 20.6914 15.1397 20.6914 9.19184C20.6913 4.39916 16.7922 0.5 11.9995 0.5ZM11.9995 13.565C9.58816 13.565 7.62644 11.6032 7.62644 9.19184C7.62644 6.7805 9.58821 4.81878 11.9995 4.81878C14.4108 4.81878 16.3725 6.78055 16.3725 9.19189C16.3725 11.6032 14.4108 13.565 11.9995 13.565Z" />
      </svg>
      <span class="index-head__city-name" *ngIf="currentAffiliate" [innerHTML]="currentAffiliate.title"></span>
      <span class="index-head__city-name" *ngIf="!currentAffiliate">{t}Выберите город{/t}</span>
    </button>

    {*<div class="index-head__shop">
      <span class="fw-bold margin-8-right">Магазин:</span> <span class="c-dark">ул. Ленина, 65</span>
    </div>*}
  </div>

  <div *ngIf="inLoading">
    <app-affiliate-skeleton></app-affiliate-skeleton>
  </div>
</div>
