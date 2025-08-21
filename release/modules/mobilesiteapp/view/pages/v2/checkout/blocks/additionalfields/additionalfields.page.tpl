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
        <div class="head__title">{t}Дополнительные детали{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div class="container" id="checkout-additionalfields">
    <ion-grid>
      <ion-row>
        <ion-col size-lg="6" size-md="8" offset-md="2" offset-lg="3" size="12">
          <div *ngIf="inLoading">
            <app-additionalfields-skeleton></app-additionalfields-skeleton>
          </div>
          <div class="form" *ngIf="!inLoading">
            <div *ngIf="additionalFields.length">
              <div *ngFor="let field of additionalFields">
                <div>
                  <div class="margin-24-bottom">
                    <label [for]="field.alias" *ngIf="!field.isBoolType()" class="checkout-label" [innerHTML]="field.title"></label>
                    <select class="form-select" *ngIf="field.isListType()" [(ngModel)]="field.val">
                      <option
                        [value]="value"
                        *ngFor="let value of field.valuesArray"
                        [innerHTML]="value"
                        [attr.selected]="field.currentVal && field.currentVal == value ? '' : null"
                      ></option>
                    </select>
                    <textarea
                      class="form-control"
                      cols="20"
                      rows="8"
                      *ngIf="field.isTextAreaType()"
                      [name]="field.alias"
                      [id]="field.alias"
                      [value]="field.currentVal"
                      [(ngModel)]="field.val"
                    ></textarea>
                    <input
                      type="text"
                      class="form-control"
                      *ngIf="field.isStringType()"
                      [name]="field.alias"
                      [id]="field.alias"
                      [value]="field.val"
                      [(ngModel)]="field.val"
                    >

                    <div class="checkbox" *ngIf="field.isBoolType()">
                      <div class="margin-16-right" [innerHTML]="field.title"></div>
                      <input
                        type="checkbox"
                        class="form-control"
                        [name]="field.alias"
                        [id]="field.alias"
                        [value]="field.val"
                        [(ngModel)]="field.val"
                      >
                      <label [for]="field.alias"></label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="margin-16-top" *ngIf="errors && errors.length">
              <div class="form__error" *ngFor="let error of errors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
                  <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
                  <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
                </svg>
                <span class="margin-8-left">
                  { { error } }
                </span>
              </div>
            </div>
            <div class="margin-48-top">
              <button
                class="button button_primary w-100"
                (click)="nextStep()"
              >
                {t}Продолжить оформление{/t}
              </button>
            </div>
          </div>
        </ion-col>
      </ion-row>
    </ion-grid>
  </div>
</ion-content>