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
        <div class="head__title">
          <form class="search-input">
            <input type="search" name="query" #searchInput placeholder="Поиск по товарам" autocomplete="off" (search)="onApplySearch()" (input)="liveSearch($event)" [(ngModel)]="query.queryString">
          </form>
        </div>
        <div class="head__expand_right">
          <button type="button" class="margin-8-right" (click)="query.queryString = ''" *ngIf="query.queryString && query.queryString.length">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#bababb"/>
            </svg>
          </button>
          <button class="search-input__submit" type="submit" (click)="onApplySearch()" *ngIf="query.queryString && query.queryString.length">
            <svg width="24" height="24" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12.3798 2.33325C6.84319 2.33325 2.33398 6.84242 2.33398 12.379C2.33398 17.9156 6.84318 22.4248 12.3798 22.4248C14.7992 22.4248 17.0232 21.5677 18.7597 20.1362H18.7698L24.0053 25.3819C24.385 25.7615 25.0029 25.7615 25.3826 25.3819C25.7622 25.0022 25.7622 24.3944 25.3826 24.0147L20.1268 18.7691C20.1284 18.7658 20.1251 18.761 20.1268 18.759C21.5584 17.0225 22.4256 14.7986 22.4256 12.3791C22.4256 6.84252 17.9164 2.33335 12.3798 2.33335L12.3798 2.33325ZM12.3798 4.2776C16.8656 4.2776 20.4812 7.89324 20.4812 12.379C20.4812 16.8648 16.8656 20.4805 12.3798 20.4805C7.894 20.4805 4.27833 16.8648 4.27833 12.379C4.27833 7.89324 7.894 4.2776 12.3798 4.2776Z" fill="#1B1B1F"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content>
  <div id="search">
    <div class="margin-24-top">
      <div class="container">
        <ul class="searchList" *ngIf="searchList && searchList.length && !query.queryString">
          <li (click)="fillSearchQuery(string)" *ngFor="let string of searchList">
            <span [innerHTML]="string"></span>
            <button type="button" class="deleteQuery" (click)="deleteFromSearchList(string)">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
              </svg>
            </button>
          </li>
        </ul>
        <div *ngIf="liveResult && query.queryString">
          <div *ngIf="liveResult.products.length">
            <p>{t}Товары{/t}</p>
            <ul class="searchList liveResult" >
              <li *ngFor="let item of liveResult.products" (click)="goToProduct(item)">
              <span *ngIf="item.getImage()" class="liveResult_image--wrap">
                <img [src]="item.getImage()[0]['small_url']" [alt]="item.title">
              </span>
                <span [innerHTML]="item.title"></span>
              </li>
            </ul>
          </div>
          <div *ngIf="liveResult.categories.length">
            <p>{t}Категории{/t}</p>
            <ul class="searchList liveResult" >
              <li *ngFor="let item of liveResult.categories" (click)="navigateTo('/tabs/catalog/category/listproduct/' + item.id)">
              <span *ngIf="item.getImage()" class="liveResult_image--wrap">
                <img [src]="item.getImage()['small_url']" [alt]="item.title">
              </span>
                <span [innerHTML]="item.title"></span>
              </li>
            </ul>
          </div>
          <div *ngIf="liveResult.brands.length">
            <p>{t}Бренды{/t}</p>
            <ul class="searchList liveResult" >
              <li *ngFor="let item of liveResult.brands" (click)="navigateTo('/tabs/catalog/brands/' + item.id)">
              <span *ngIf="item.getImage()" class="liveResult_image--wrap">
                <img [src]="item.getImage()['small_url']" [alt]="item.title">
              </span>
                <span [innerHTML]="item.title"></span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</ion-content>