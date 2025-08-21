<div id="products">
  <div class="margin-48-bottom">
    <div *ngIf="inLoading">
      <app-products-skeleton [numOfProducts]="dirProductsNum"></app-products-skeleton>
    </div>

    <div *ngIf="!inLoading && dir && products.length">
      <h3 class="margin-24-bottom-table" *ngIf="dirTitle" [innerHTML]="dirTitle">{t}Категория{/t}</h3>
      <h3 class="margin-24-bottom-table" *ngIf="dir && !dirTitle" [innerHTML]="dir.name">{t}Категория{/t}</h3>

      <div class="catalog-list">
        <div class="item-card" *ngFor="let product of products">
          {if $client_version >= 2.4}
            <app-product-item [product]="product" [category]="dir"></app-product-item>
          {else}
            <button
              type="button"
              class="product-list__favorite"
              (click)="toggleInFavorite(product)"
              [ngClass]="product.isInFavorite() || product.inFavorite ? 'product-head__favorite_active' : ''"
            >
              <svg width="20" height="20" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="inFavoriteСontour" d="M17.2289 0C15.1859 0 13.2734 0.88834 12 2.37674C10.7266 0.888289 8.81417 0 6.77106 0C3.0375 0 0 2.91698 0 6.5025C0 9.31012 1.7433 12.5576 5.18136 16.1546C7.82722 18.9228 10.7055 21.0661 11.5246 21.6571L11.9998 22L12.4751 21.6572C13.2942 21.0662 16.1726 18.9229 18.8185 16.1547C22.2567 12.5577 24 9.31017 24 6.5025C24 2.91698 20.9625 0 17.2289 0ZM17.6527 15.1271C15.4764 17.4039 13.135 19.2445 11.9998 20.0927C10.8647 19.2445 8.52343 17.4039 6.34713 15.127C3.23003 11.8658 1.58242 8.88345 1.58242 6.5025C1.58242 3.75497 3.91005 1.51966 6.77106 1.51966C8.65382 1.51966 10.3923 2.5051 11.308 4.09147L12 5.29027L12.692 4.09147C13.6077 2.50515 15.3461 1.51966 17.2289 1.51966C20.09 1.51966 22.4176 3.75492 22.4176 6.5025C22.4176 8.88355 20.7699 11.8659 17.6527 15.1271Z"/>
                <path class="inFavoriteBody" d="M17.6527 15.1271C15.4764 17.4039 13.135 19.2445 11.9998 20.0927C10.8647 19.2445 8.52343 17.4039 6.34713 15.127C3.23003 11.8658 1.58242 8.88345 1.58242 6.5025C1.58242 3.75497 3.91005 1.51966 6.77106 1.51966C8.65382 1.51966 10.3923 2.5051 11.308 4.09147L12 5.29027L12.692 4.09147C13.6077 2.50515 15.3461 1.51966 17.2289 1.51966C20.09 1.51966 22.4176 3.75492 22.4176 6.5025C22.4176 8.88355 20.7699 11.8659 17.6527 15.1271Z"/>
              </svg>
            </button>
            <a class="item-card__link" (click)="goToProduct(product, dir)">
              <div
                *ngIf="product.getSpecDir()"
                class="item-card__label"
                [style.background]="product.getSpecDir()['label_bg_color']"
                [style.color]="product.getSpecDir()['label_text_color']"
                [innerHTML]="product.getSpecDir()['name']"
              ></div>
              <div class="item-card__img" *ngIf="product.getMainImage()">
                <canvas width="215" height="112"></canvas>
                <ion-img [src]="product.getMainImage()['middle_url']" alt=""></ion-img>
              </div>
              <div class="item-card__title" [innerHTML]="product.title"></div>
            </a>
            <app-addtocart-button [product]="product" [category]="dir"></app-addtocart-button>
          {/if}
        </div>
      </div>
      <button
        class="button button_default margin-24-top w-100"
        type="button" (click)="navigateTo('/tabs/catalog/category/listproduct/' + dir.id)"
      >{t}Еще{/t}</button>
    </div>
  </div>
</div>