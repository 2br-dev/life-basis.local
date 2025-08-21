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
        <div class="head__title" *ngIf="categories && categories.rootCategory" [innerHTML]="categories.rootCategory.name"></div>
        <div class="head__expand_right">
          <app-phone-button *ngIf="mobilePhone" [phone]="mobilePhone"></app-phone-button>
          <app-search-button></app-search-button>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content>
  <div id="category">
    <div class="section">
      <div class="container" *ngIf="inLoading">
        <app-category-skeleton></app-category-skeleton>
      </div>
      <div class="container" *ngIf="!inLoading">
        <ion-grid>
          <ion-row>
            <ion-col size-md="6" size="12" *ngIf="categories && categories.rootCategory">
              <div class="margin-24-right-table margin-8-bottom-mob sticky" *ngIf="categories.rootCategory.getBackgroundImage()">
                <picture>
                  <source [srcset]="categories.rootCategory.getBackgroundImage()['middle_url']" media="(max-width: 767.98px)">
                  <img class="w-100" [src]="categories.rootCategory.getBackgroundImage()['middle_url']" alt="">
                </picture>
              </div>
              <div class="index-category" *ngIf="!categories.rootCategory.getBackgroundImage()" >
                <div class="index-category__wrapper" [style.background]="categories.rootCategory.mobileBackgroundColor">
                  <div class="index-category__title" [innerHTML]="categories.rootCategory.name" [style.color]="categories.rootCategory.mobileBackgroundTitleColor"></div>
                  <div class="index-category__icon">
                    <div class="index-category__icon-icon">
                      <div class="index-category__icon-background" [style.background]="categories.rootCategory.mobileBackgroundIconColor"></div>
                      <img *ngIf="categories.rootCategory.getIcon()" [src]="categories.rootCategory.getIcon()['middle_url']">
                      <svg *ngIf="!categories.rootCategory.getIcon()" viewBox="60 180 350 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M334.246 197.655C332.805 196.898 331.112 196.828 329.632 197.467L145.106 277.096C140.297 279.172 134.667 277.62 131.496 273.345L118.962 256.442C117.454 254.409 114.776 253.671 112.489 254.658L76.8703 270.214C74.1404 271.392 72.8984 274.598 74.0963 277.374C75.2943 280.15 78.4789 281.446 81.2089 280.268L104.699 269.993C109.518 267.886 115.177 269.431 118.36 273.723L178.545 354.884C192.143 373.22 199.92 395.257 200.834 418.037C191.058 423.73 186.89 436.132 191.557 446.947C196.59 458.609 210.015 464.072 221.484 459.123C232.953 454.174 238.188 440.659 233.155 428.996C231.4 424.928 228.622 421.618 225.263 419.237L338.546 370.352C337.974 374.429 338.476 378.721 340.232 382.789C345.264 394.452 358.689 399.914 370.158 394.965C381.627 390.016 386.863 376.501 381.83 364.838C376.797 353.176 363.373 347.714 351.904 352.663L211.442 413.275L211.245 408.351C210.676 394.177 215.279 382.703 228.115 377.164L349.105 324.953C357.128 321.491 361.182 311.982 358.966 301.832L337.073 201.38C336.724 199.78 335.687 198.414 334.246 197.655ZM201.444 442.681C204.084 448.8 211.128 451.666 217.145 449.069C223.162 446.472 225.909 439.382 223.269 433.263C220.628 427.144 213.585 424.278 207.568 426.875C201.55 429.471 198.803 436.562 201.444 442.681ZM365.82 384.911C371.837 382.315 374.584 375.224 371.943 369.105C369.303 362.986 362.259 360.12 356.242 362.717C350.225 365.314 347.478 372.404 350.118 378.523C352.759 384.642 359.802 387.508 365.82 384.911ZM287.517 254.546L333.325 234.778L327.951 210.119L272.157 234.195L287.517 254.546ZM309.666 283.893L341.075 270.339L335.7 245.679L294.306 263.542L309.666 283.893ZM273.453 311.445L292.94 337.264L325.757 323.103L306.27 297.284L273.453 311.445ZM230.451 330.002L249.937 355.822L282.755 341.66L263.268 315.841L230.451 330.002ZM187.315 348.617L206.544 374.547L239.752 360.217L220.265 334.398L187.315 348.617ZM277.331 258.941L261.972 238.591L229.155 252.752L244.514 273.103L277.331 258.941ZM234.329 277.498L218.969 257.148L186.152 271.31L201.511 291.66L234.329 277.498ZM256.479 306.845L241.118 286.494L208.301 300.655L223.661 321.007L256.479 306.845ZM180.615 339.583L213.476 325.402L198.115 305.051L165.458 319.143L180.615 339.583ZM251.304 282.098L266.664 302.45L299.481 288.288L284.121 267.937L251.304 282.098ZM191.326 296.055L175.967 275.705L143.603 289.671L158.759 310.109L191.326 296.055ZM335.943 318.707L344.766 314.9C347.957 313.523 349.496 308.965 348.424 304.061L343.451 281.239L316.456 292.888L335.943 318.707Z" fill="#A6A6A6"/>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </ion-col>
            <ion-col size-md="6" size="12" *ngIf="categories.categories && categories.categories.length">
              <ul class="category-list">
                <li *ngFor="let childCategory of categories.categories">
                  <a class="category-link" (click)="goToCategory(childCategory)">
                    <span class="category-link__img" *ngIf="childCategory.getImage()">
                      <ion-img [src]="childCategory.getImage()['micro_url']" alt=""></ion-img>
                    </span>
                    <span [ngClass]="childCategory.getImage() ? 'margin-24-left' : ''" [innerHTML]="childCategory.name"></span>
                  </a>
                </li>
              </ul>
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content >
