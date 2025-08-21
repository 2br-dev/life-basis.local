<div id="productitem">
  <button
    class="product-list__favorite"
    type="button"
    [ngClass]="isFavoritePage || product.isInFavorite() || product.inFavorite ? 'product-head__favorite_active' : ''"
    (click)="toggleInFavorite(product)"
  >
    <svg width="20" height="20" viewBox="0 0 24 22" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path class="inFavoriteСontour" d="M17.2289 0C15.1859 0 13.2734 0.88834 12 2.37674C10.7266 0.888289 8.81417 0 6.77106 0C3.0375 0 0 2.91698 0 6.5025C0 9.31012 1.7433 12.5576 5.18136 16.1546C7.82722 18.9228 10.7055 21.0661 11.5246 21.6571L11.9998 22L12.4751 21.6572C13.2942 21.0662 16.1726 18.9229 18.8185 16.1547C22.2567 12.5577 24 9.31017 24 6.5025C24 2.91698 20.9625 0 17.2289 0ZM17.6527 15.1271C15.4764 17.4039 13.135 19.2445 11.9998 20.0927C10.8647 19.2445 8.52343 17.4039 6.34713 15.127C3.23003 11.8658 1.58242 8.88345 1.58242 6.5025C1.58242 3.75497 3.91005 1.51966 6.77106 1.51966C8.65382 1.51966 10.3923 2.5051 11.308 4.09147L12 5.29027L12.692 4.09147C13.6077 2.50515 15.3461 1.51966 17.2289 1.51966C20.09 1.51966 22.4176 3.75492 22.4176 6.5025C22.4176 8.88355 20.7699 11.8659 17.6527 15.1271Z"/>
      <path class="inFavoriteBody" d="M17.6527 15.1271C15.4764 17.4039 13.135 19.2445 11.9998 20.0927C10.8647 19.2445 8.52343 17.4039 6.34713 15.127C3.23003 11.8658 1.58242 8.88345 1.58242 6.5025C1.58242 3.75497 3.91005 1.51966 6.77106 1.51966C8.65382 1.51966 10.3923 2.5051 11.308 4.09147L12 5.29027L12.692 4.09147C13.6077 2.50515 15.3461 1.51966 17.2289 1.51966C20.09 1.51966 22.4176 3.75492 22.4176 6.5025C22.4176 8.88355 20.7699 11.8659 17.6527 15.1271Z"/>
    </svg>
  </button>
  <a
    class="item-card__link"
    (click)="goToProduct(product, category)"
    [ngClass]="product.specdirs && product.specdirs.length ? 'item-card__have-spec-dir' : ''"
  >
    <div *ngIf="(product.rating != '0.0') || (product.specdirs && product.specdirs.length)">
      <div
        *ngIf="product.getSpecDir()"
        class="item-card__label"
        [style.background]="product.getSpecDir()['label_bg_color']"
        [style.color]="product.getSpecDir()['label_text_color']"
        [innerHTML]="product.getSpecDir()['name']"
      ></div>
      <div class="product-rating" *ngIf="product.rating != '0.0'">
        <div class="rating-star">
          <svg width="24" height="15" viewBox="0 0 32 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 0L18.2451 6.90983H25.5106L19.6327 11.1803L21.8779 18.0902L16 13.8197L10.1221 18.0902L12.3673 11.1803L6.48944 6.90983H13.7549L16 0Z" fill="#FFD12F"></path>
          </svg>
        </div>
        <div class="rating-score">{ { product.rating } }</div>
      </div>
    </div>
    <div class="item-card__img" *ngIf="product.getMainImage()">
      <canvas width="215" height="112"></canvas>
      <ion-img [src]="product.getMainImage()['middle_url']" alt=""></ion-img>
    </div>
    <div class="item-card__title">{ { product.title } }</div>
  </a>
  <app-addtocart-button [product]="product" [category]="category"></app-addtocart-button>
</div>