<ion-content>
  <div id="catalog" *ngIf="!inLoading">
    <div class="index-head">
      <div class="container">
        <div class="index-head__inner">
          <div class="index-head__location">
              <div class="index-head__logo" *ngIf="logo">
                  <img [src]="logo.big_url" alt="">
              </div>
              <div *ngIf="affiliates">
                  <app-affiliate [currentAffiliate]="currentAffiliate"></app-affiliate>
              </div>
          </div>
          <div class="d-flex margin-16-left">
            <app-phone-button *ngIf="mobilePhone" [phone]="mobilePhone"></app-phone-button>
            <app-search-button></app-search-button>
          </div>
        </div>
      </div>
    </div>
    <div class="section">
      <div class="container">
        <app-stories *ngIf="showStoriesBlock"></app-stories>
        <app-mainbanner *ngIf="mainBannerZone" [params]="{ zone: mainBannerZone }"></app-mainbanner>
        <div *ngIf="showBonusCardBlock || {if $client_version >= 3.1}menuList{else}showPromoBlock{/if}" id="promo" class="margin-24-bottom">
          <app-bonuscard *ngIf="showBonusCardBlock" [hideHintVia]="24"></app-bonuscard>
          {if $client_version >= 3.1}
            <div *ngIf="menuList">
              <app-promo *ngFor="let menuId of menuList" [menuId]="menuId"></app-promo>
            </div>
          {else}
            <app-promo *ngIf="showPromoBlock"></app-promo>
          {/if}
        </div>
        <app-banners *ngIf="bannerZone" [params]="{ zone: bannerZone }"></app-banners>
        <app-special *ngIf="showSpecialBlock"></app-special>
        <app-categories [parentId]="rootDir"></app-categories>
        <div *ngIf="dirList">
          <app-products
            *ngFor="let dir of dirList"
            [dirId]="dir.dir"
            [dirProductsNum]="dir.dir_products_num"
            [dirTitle]="dir.dir_title"
          ></app-products>
        </div>
        {if $client_version >= 3.9}
          <app-comments *ngIf="showLastCommentsBlock"></app-comments>
        {/if}
        <app-brands *ngIf="showBrandsBlock"></app-brands>
      </div>
    </div>
  </div>
</ion-content>
