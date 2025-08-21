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
        <div class="head__title">{t}Личные данные{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>
<ion-content [fullscreen]="true">
  <div id="profileedit">
    <div class="section">
      <div class="container">
        <ion-grid>
          <ion-row>
            <ion-col size-lg="5" size-md="6" class="ion-hide-md-down">
              <div class="menu">
                <app-lk-menu></app-lk-menu>
              </div>
            </ion-col>
            <ion-col size-lg="5" size-md="6" offset-lg="1" size="12" *ngIf="fields">
              <div class="margin-16-left-table">
                <div class="margin-24-bottom" *ngIf="errors && errors.length">
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
                <div>
                  <div class="checkbox margin-24-bottom">
                    <input
                      type="checkbox"
                      id="isCompany"
                      name="isCompany"
                      (click)="isCompany()"
                      [attr.checked]="fields.isCompany ? '' : null"
                    >
                    <label class="align-items-center" for="isCompany">
                      <span class="checkbox-attr">
                       <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z"/>
                         <path class="checkbox-attr__check" fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z"/>
                       </svg>
                      </span>
                      <span>{t}Компания или ИП{/t}</span>
                    </label>
                  </div>
                  <div *ngIf="fields.isCompany">
                    <div class="margin-24-bottom">
                      <label for="company" class="form-label">{t}Наименование компании{/t}</label>
                      <input type="text" class="form-control" id="company" [(ngModel)]="fields.company">
                    </div>
                    <div class="margin-24-bottom">
                      <label for="companyInn" class="form-label">{t}ИНН{/t}</label>
                      <input type="text" class="form-control" id="companyInn" [(ngModel)]="fields.companyInn">
                    </div>
                  </div>
                  <div class="margin-24-bottom">
                    <label for="surname" class="form-label">{t}Фамилия{/t}</label>
                    <input type="text" class="form-control" id="surname" [(ngModel)]="fields.surname">
                  </div>
                  <div class="margin-24-bottom">
                      <label for="name" class="form-label">{t}Имя{/t}</label>
                      <input type="text" class="form-control" id="name" [(ngModel)]="fields.name">
                  </div>
                  <div class="margin-24-bottom">
                      <label for="midname" class="form-label">{t}Отчество{/t}</label>
                      <input type="text" class="form-control" id="midname" [(ngModel)]="fields.midname">
                  </div>
                  <div class="margin-24-bottom phone_input">
                    <label for="phone" class="form-label">{t}Телефон{/t}</label>
                    <input
                      type="text"
                      class="form-control"
                      id="phone"
                      name="phone"
                      [ngClass]="phoneIsResolved ? 'phone_is_resolved' : ''"
                      [(ngModel)]="fields.phone"
                      [attr.disabled]="disablePhoneInput() ? '' : null"
                    >
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path class="checkbox-attr__check" fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z"/>
                    </svg>
                  </div>
                  <div
                     class="margin-16-bottom"
                     *ngIf="verificationSession.codeSendFlag || phoneIsResolved"
                  >
                    <button class="button button_default button_small w-100" type="button"
                      (click)="changePhone()">{t}Изменить номер телефона{/t}
                    </button>
                  </div>
                  <div *ngIf="!phoneIsResolved && authType != 0">
                    <div
                      class="margin-16-bottom c-dark"
                      *ngIf="verificationSession.codeRefreshDelay"
                    >
                      {t}Выслать повторно через:{/t} { { verificationSession.codeRefreshDelayTime } } {t}сек.{/t}
                    </div>
                      <div class="margin-16-bottom" *ngIf="!verificationSession.codeRefreshDelay">
                        <button
                          class="button button_default button_small w-100"
                          type="button"
                          [attr.disabled]="verificationSession.codeRefreshDelay ? '' : null"
                          (click)="getCode()"
                        >
                          {t}Получить код{/t}
                        </button>
                      </div>
                      <div *ngIf="verificationSession.codeSendFlag">
                        <div class="margin-16-bottom">
                          <label for="code" class="form-label">{t}Код{/t}</label>
                          <input
                            type="text"
                            class="form-control"
                            id="code" name="code"
                            [attr.placeholder]="verificationSession.codeDebug && verificationSession.codeSendFlag ? verificationSession.codeDebug : null"
                            [(ngModel)]="verificationSession.code">
                        </div>
                        <div class="margin-16-bottom">
                          <button
                            class="button button_default button_small w-100"
                            type="button"
                            (click)="checkCode()"
                          >
                            {t}Подтвердить телефон{/t}
                          </button>
                        </div>
                      </div>
                  </div>
                  <div class="margin-24-bottom">
                    <label for="email" class="form-label">{t}Электронная почта{/t}</label>
                    <input type="text" class="form-control" id="email" [(ngModel)]="fields.eMail">
                  </div>
                  <div *ngIf="additionalFields && additionalFields.length">
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

                          <div class="additionalCheckbox" *ngIf="field.isBoolType()">
                            <div class="margin-16-right form-label" [innerHTML]="field.title"></div>
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
                  <div class="checkbox margin-24-bottom">
                    <input
                      type="checkbox"
                      id="changePassword"
                      name="changePassword"
                      (click)="needChangePass()"
                      [(ngModel)]="fields.changepass"
                    >
                    <label class="align-items-center" for="changePassword">
                      <span class="checkbox-attr">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z"/>
                          <path class="checkbox-attr__check" fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z"/>
                        </svg>
                      </span>
                      <span>{t}Изменить пароль{/t}</span>
                    </label>
                  </div>
                  <div *ngIf="fields.changepass">
                    <div class="margin-24-bottom">
                      <label for="currentPass" class="form-label">{t}Старый пароль{/t}</label>
                      <input type="password" class="form-control" id="currentPass" [(ngModel)]="pass.pass">
                    </div>
                    <div class="margin-24-bottom">
                      <label for="openPass" class="form-label">{t}Новый пароль{/t}</label>
                      <input type="password" class="form-control" id="openPass" [(ngModel)]="pass.openpass">
                    </div>
                    <div class="margin-24-bottom">
                      <label for="openPassConfirm" class="form-label">{t}Повторите пароль{/t}</label>
                      <input type="password" class="form-control" id="openPassConfirm" [(ngModel)]="pass.openpassConfirm">
                    </div>
                  </div>
                  <button
                    class="button button_primary w-100 margin-16-top"
                    (click)="updateProfile()"
                    [attr.disabled]="formIsLoading ? '' : null"
                  >
                    {t}Сохранить{/t}
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
            </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>
