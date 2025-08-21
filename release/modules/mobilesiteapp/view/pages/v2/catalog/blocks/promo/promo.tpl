<div id="promo" {if $client_version < 3.1} class="margin-24-top" {/if}>
  <div class="index-promo" *ngIf="!inLoading && {if $client_version >= 3.1}menu{else}menus && menus.length{/if}">
    <div {if $client_version < 3.1}*ngFor="let menu of menus" {/if} class="index-promo__item">
      <a (click)="openMenu(menu)" class="index-promo-delivery">
        <div class="index-promo-delivery__info">
          <div class="index-promo-delivery__icon">
            <ion-icon *ngIf="menu.mobileImage" [name]="menu.mobileImage" color="dark" size="large"></ion-icon>
          </div>
          <div [innerHTML]="menu.title"></div>
        </div>
        <div class="index-promo-delivery__arrow">
          <svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.47072 27.5782C4.84309 27.0159 4.84309 26.1041 5.47072 25.5418L16.12 16L5.47072 6.45823C4.84309 5.89588 4.84309 4.98412 5.47072 4.42177C6.09835 3.85941 7.11594 3.85941 7.74356 4.42177L19.5293 14.9818C20.1569 15.5441 20.1569 16.4559 19.5293 17.0182L7.74356 27.5782C7.11594 28.1406 6.09835 28.1406 5.47072 27.5782Z" fill="#1B1B1F"/>
          </svg>
        </div>
      </a>
    </div>
  </div>

  <div *ngIf="inLoading">
    <app-bonuscard-skeleton></app-bonuscard-skeleton>
  </div>
</div>
