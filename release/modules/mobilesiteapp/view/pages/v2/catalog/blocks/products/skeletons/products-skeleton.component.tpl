<div class="margin-24-bottom">
  <ion-skeleton-text animated style="width: 100%;max-width: 140px; height: 28px;"></ion-skeleton-text>
</div>

<div class="catalog-list">
  <div
    *ngFor="let item of count"
    style="border: 1px solid var(--color-gray); padding: 16px 8px 12px; border-radius: 12px;"
  >
    <div class="margin-32-bottom">
      <div class="skeleton-loader">
        <canvas width="221" height="118"></canvas>
      </div>
      <div style="flex: 1; margin-top: 12px">
        <ion-skeleton-text animated style="width: 100%; height: 14px; margin-bottom: 8px"></ion-skeleton-text>
        <ion-skeleton-text animated style="width: 80%; height: 14px; margin-bottom: 8px"></ion-skeleton-text>
      </div>
    </div>
    <div class="d-flex ion-hide-sm-down">
      <ion-skeleton-text animated style="width: 100%; height: 36px; margin-right: 16px; flex:1;"></ion-skeleton-text>
      <ion-skeleton-text animated style="width: 100%;max-width: 60px; height: 36px; "></ion-skeleton-text>
    </div>
    <div class="ion-hide-sm-up">
      <ion-skeleton-text animated style="width: 100%; height: 36px;"></ion-skeleton-text>
    </div>
  </div>
</div>