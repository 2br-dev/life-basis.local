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
        <div class="head__title">{t}Оставить отзыв{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content>
  <div id="commentadd">
    <div class="section">
      <div class="container">
        <div class="product__wrapper d-flex margin-24-right margin-24-bottom" *ngIf="productIsLoading">
          <ion-skeleton-text animated style="width: 100%;max-width: 70px;height: 70px;--border-radius: 4px;margin: 0;"></ion-skeleton-text>
          <div style="width: 100%;">
            <ion-skeleton-text animated style="width: 100%;max-width: 200px; height: 16px; margin:0 0 0 16px"></ion-skeleton-text>
            <ion-skeleton-text animated style="width: 100%;max-width: 160px; height: 16px; margin:8px 0 0 16px"></ion-skeleton-text>
          </div>
        </div>

        <div class="product__wrapper d-flex margin-24-right margin-24-bottom" *ngIf="!productIsLoading && product">
          <div class="item-card__img" *ngIf="product.getMainImage()">
            <img [src]="product.getMainImage()['middle_url']" alt="">
          </div>
          <div class="item-card__title">{ { product.title } }</div>
        </div>
        <div class="comment__error margin-24-bottom" *ngIf="!allowMoreComments">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
            <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
            <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
          </svg>
          <span class="margin-8-left" *ngIf="!allowMoreComments">{t}Разрешен один отзыв на товар, предыдущий отзыв будет заменен{/t}</span>
        </div>
        <div class="comment__error margin-24-bottom" *ngIf="needAuth">
          <a (click)="openAuth()">
            <span class="authLink">{t}Авторизуйтесь{/t}</span>{t}, чтобы оставить отзыв{/t}
          </a>
        </div>
        <div class="comment__error margin-24-bottom" *ngIf="errors && errors.length">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
            <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
            <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
          </svg>
          <span class="margin-8-left" *ngIf="errors && errors.length">{ { errors } }</span>
        </div>

        <div>
          <div class="margin-24-bottom">
            <label for="user_name" class="form-label">{t}Имя{/t}</label>
            <input type="text" class="form-control" id="user_name" [(ngModel)]="commentItems.userName">
          </div>
          <div class="margin-24-bottom">
            <label for="rateStars" class="form-label">{t}Оценка{/t}</label>
            <input type="hidden" class="form-control" id="rateStars" >
            <ul class="rateStars">
              <li
                class="rateStars"
                [ngClass]="commentItems.rate >= star ? 'rateStars__active' : ''"
                (click)="setRate(star)"
                *ngFor="let star of [1,2,3,4,5]"
              ></li>
            </ul>
          </div>
          <div class="margin-24-bottom">
            <label for="message" class="form-label">{t}Текст отзыва{/t}</label>
            <textarea class="form-control" name="message" id="message" cols="20" rows="8" [(ngModel)]="commentItems.message"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
</ion-content>
<ion-footer>
  <div class="container margin-16-bottom">
    <div class="modal-footer-button">
      <div *ngIf="usePolicy != 0 && (agreementPersonalData || policyPersonalData)" class="margin-16-bottom">
        <div class="checkbox d-flex ion-justify-content-center">
          <input type="checkbox" id="commentAddPolicy" name="commentAddPolicy" [(ngModel)]="canTrySubmit" (click)="changeCanTrySubmit()">
          <label class="align-items-center" for="commentAddPolicy">
            <span class="checkbox-attr">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
              <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
            </svg>
            </span>
            <span>
              {t}Я даю свое{/t} <a (click)="openPolicy('agreementPersonalData')">{t}согласие на обработку{/t}</a> {t}персональных данных{/t}</span>
          </label>
        </div>

        <div class="margin-32-top fz-12">
          {t}Отправляя комментарий, вы соглашаетесь с{/t} <a (click)="openPolicy('policyPersonalData')">{t}пользовательским соглашением и политикой конфиденциальности{/t}</a>
        </div>
      </div>

      <button
        type="button"
        class="button button_primary w-100"
        [attr.disabled]="!canTrySubmit || needAuth || formIsLoading ? '' : null"
        (click)="addComment()"
      >
        {t}Отправить{/t}
        <span *ngIf="formIsLoading" class="formIsLoading">
          <svg width="44" height="44" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="var(--rs-color-primary)">
            <g fill="none" fill-rule="evenodd" stroke-width="2">
              <circle cx="22" cy="22" r="1">
                <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
              </circle>
              <circle cx="22" cy="22" r="1">
                <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite" />
                <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite" />
              </circle>
            </g>
        </svg>
        </span>
      </button>
    </div>
  </div>
</ion-footer>