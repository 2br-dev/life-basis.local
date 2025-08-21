<ion-header>
  <ion-toolbar>
    <div class="container">
      <div class="head">
        <div class="head__expand">
          <button class="head__button" type="button" (click)="backButton()">
              <img src="assets/icon/arrow-back.svg" width="24" height="24" alt="">
          </button>
        </div>
        <div class="head__title" *ngIf="authPage">{t}Вход{/t}</div>
        <div class="head__title" *ngIf="registrationPage">{t}Регистрация{/t}</div>
        <div class="head__title" *ngIf="recoverPage">{t}Восстановление пароля{/t}</div>
        <div class="head__title" *ngIf="changePasswordPage">{t}Восстановление пароля{/t}</div>
        <div class="head__expand_right">
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>
<ion-content class="ion-padding">
  <div id="modal-auth">
    <div class="container auth-content">
      <div *ngIf="authPage && !inLoading" id="auth">
        <div class="margin-16-bottom" *ngIf="errors && errors.length">
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
          <div class="form">
            <div *ngIf="authType == '0' {if $client_version >= 2.9}|| enableLoginWithPassword{/if}">
              <div class="margin-16-bottom">
                <label for="login" class="form-label" *ngIf="authPlaceholder" [innerHTML]="authPlaceholder"></label>
                <label for="login" class="form-label" *ngIf="!authPlaceholder">{t}Электронная почта{/t}</label>
                <input type="text" class="form-control" name="login" id="login" [(ngModel)]="authFields.login" [value]="authFields.login">
              </div>
                <div class="password-box">
                  <label for="password" class="form-label">Пароль</label>
                  <input
                          type="password"
                          class="form-control"
                          name="password"
                          id="password"
                          #authPassword
                          [(ngModel)]="authFields.password"
                          [value]="authFields.password"
                  >
                  {if $client_version >= 3.5}
                    <button class="show-hide__password" (click)="showHidePassword()">
                      <svg *ngIf="!showPassword" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 256" xml:space="preserve">
                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                          <path d="M 45 73.264 c -14.869 0 -29.775 -8.864 -44.307 -26.346 c -0.924 -1.112 -0.924 -2.724 0 -3.836 C 15.225 25.601 30.131 16.737 45 16.737 c 14.868 0 29.775 8.864 44.307 26.345 c 0.925 1.112 0.925 2.724 0 3.836 C 74.775 64.399 59.868 73.264 45 73.264 z M 6.934 45 C 19.73 59.776 32.528 67.264 45 67.264 c 12.473 0 25.27 -7.487 38.066 -22.264 C 70.27 30.224 57.473 22.737 45 22.737 C 32.528 22.737 19.73 30.224 6.934 45 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 45 62 c -9.374 0 -17 -7.626 -17 -17 s 7.626 -17 17 -17 s 17 7.626 17 17 S 54.374 62 45 62 z M 45 34 c -6.065 0 -11 4.935 -11 11 s 4.935 11 11 11 s 11 -4.935 11 -11 S 51.065 34 45 34 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                        </g>
                      </svg>
                      <svg *ngIf="showPassword" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 256" xml:space="preserve">
                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                          <path d="M 13.148 79.853 c -0.768 0 -1.536 -0.293 -2.121 -0.879 c -1.172 -1.171 -1.172 -3.071 0 -4.242 l 63.705 -63.705 c 1.172 -1.172 3.07 -1.172 4.242 0 c 1.172 1.171 1.172 3.071 0 4.242 L 15.269 78.974 C 14.684 79.56 13.916 79.853 13.148 79.853 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 25.296 67.703 c -0.504 0 -1.012 -0.127 -1.474 -0.388 C 16.105 62.961 8.323 56.098 0.693 46.918 c -0.924 -1.112 -0.924 -2.724 0 -3.835 c 21.533 -25.904 43.565 -32.767 65.485 -20.399 c 0.816 0.461 1.371 1.277 1.498 2.207 s -0.188 1.864 -0.852 2.527 L 27.418 66.824 C 26.841 67.402 26.073 67.703 25.296 67.703 z M 6.933 45 c 5.972 6.896 11.974 12.242 17.891 15.934 l 34.842 -34.842 C 42.131 18.098 24.824 24.311 6.933 45 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 34.394 58.606 c -0.795 0 -1.558 -0.315 -2.121 -0.878 C 28.873 54.329 27 49.809 27 45 c 0 -9.925 8.075 -18 18 -18 c 4.749 0 9.23 1.833 12.617 5.163 c 0.569 0.56 0.893 1.322 0.897 2.12 s -0.308 1.565 -0.869 2.132 L 36.524 57.719 c -0.562 0.566 -1.326 0.886 -2.124 0.888 C 34.398 58.606 34.396 58.606 34.394 58.606 z M 45 33 c -6.617 0 -12 5.383 -12 12 c 0 2.175 0.574 4.261 1.651 6.085 L 50.995 34.6 C 49.19 33.556 47.136 33 45 33 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 44.973 73.26 c -3.552 0 -7.104 -0.501 -10.657 -1.505 c -1.594 -0.45 -2.521 -2.108 -2.071 -3.703 c 0.45 -1.594 2.105 -2.524 3.703 -2.07 C 51.38 70.341 67.226 63.287 83.066 45 c -3.977 -4.592 -7.98 -8.514 -11.925 -11.68 c -1.292 -1.037 -1.499 -2.925 -0.462 -4.218 c 1.038 -1.292 2.927 -1.499 4.218 -0.462 c 4.796 3.849 9.644 8.708 14.409 14.442 c 0.925 1.111 0.925 2.724 0 3.835 C 74.743 64.438 59.874 73.26 44.973 73.26 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 45 63 c -0.386 0 -0.77 -0.013 -1.149 -0.036 c -1.654 -0.101 -2.913 -1.523 -2.812 -3.178 c 0.102 -1.652 1.527 -2.909 3.178 -2.811 C 44.476 56.991 44.737 57 45 57 c 6.617 0 12 -5.383 12 -12 c 0 -0.27 -0.009 -0.538 -0.026 -0.803 c -0.107 -1.653 1.146 -3.081 2.799 -3.188 c 1.665 -0.103 3.082 1.146 3.189 2.799 C 62.987 44.202 63 44.599 63 45 C 63 54.925 54.925 63 45 63 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                        </g>
                      </svg>
                    </button>
                  {/if}
                </div>
            </div>
            <div *ngIf="authType == '2' {if $client_version >= 2.9}&& !enableLoginWithPassword{/if}">
              <div *ngIf="!verificationSession.codeSendFlag">
                <div class="margin-16-bottom">
                  <label for="phone" class="form-label">{t}Введите номер телефона{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    name="phone"
                    id="phone"
                    [(ngModel)]="authFields.phone"
                    [value]="authFields.phone"
                  >
                </div>
              </div>
                <div class="phone_code" *ngIf="verificationSession.codeSendFlag">
                  <div class="c-dark margin-32-right-table margin-32-bottom-mob">
                    <p>{t}Код отправлен на номер{/t} { { verificationSession.codeSendPhoneMask } }</p>
                  </div>
                  <div class="margin-16-bottom">
                    <label for="phone" class="form-label">{t}Введите код{/t}</label>
                    <input
                      type="text"
                      class="form-control"
                      name="login"
                      id="verificationCode"
                      [(ngModel)]="verificationSession.code"
                      [value]="verificationSession.code"
                      [attr.placeholder]="verificationSession.codeDebug && verificationSession.codeSendFlag ? verificationSession.codeDebug : null"
                    >
                  </div>
                    <div class="margin-24-top c-dark" *ngIf="verificationSession.codeRefreshDelay">
                      {t}Выслать повторно через:{/t} { { verificationSession.codeRefreshDelayTime } } {t}сек.{/t}
                    </div>

                    <div class="margin-24-top" *ngIf="!verificationSession.codeRefreshDelay">
                      <button
                        type="button"
                        class="button button_default button_small w-100"
                        [attr.disabled]="verificationSession.codeRefreshDelay ? '' : null"
                        (click)="getCode()"
                      >{t}Получить новый код{/t}</button>
                    </div>
                </div>
                <!--<div class="phone-code">
                  <input type="text" class="form-control">
                  <input type="text" class="form-control">
                  <input type="text" class="form-control">
                  <input type="text" class="form-control">
                </div>-->
              </div>
              <div *ngIf="authType == '1'">
                <div *ngIf="!verificationSession.codeSendFlag">
                  <div class="margin-16-bottom">
                    <label for="login" class="form-label">{t}Электронная почта{/t}</label>
                    <input
                      type="text"
                      class="form-control"
                      name="login"
                      id="login"
                      [(ngModel)]="authFields.login"
                      [value]="authFields.login"
                    >
                  </div>
                    <div class="password-box">
                      <label for="twoStepPassword" class="form-label">{t}Пароль{/t}</label>
                      <input
                        type="password"
                        class="form-control"
                        name="twoStepPassword"
                        id="twoStepPassword"
                        #authPassword
                        [(ngModel)]="authFields.password"
                        [value]="authFields.password"
                      >
                      {if $client_version >= 3.5}
                        <button class="show-hide__password" (click)="showHidePassword()">
                          <svg *ngIf="!showPassword" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 256" xml:space="preserve">
                            <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                              <path d="M 45 73.264 c -14.869 0 -29.775 -8.864 -44.307 -26.346 c -0.924 -1.112 -0.924 -2.724 0 -3.836 C 15.225 25.601 30.131 16.737 45 16.737 c 14.868 0 29.775 8.864 44.307 26.345 c 0.925 1.112 0.925 2.724 0 3.836 C 74.775 64.399 59.868 73.264 45 73.264 z M 6.934 45 C 19.73 59.776 32.528 67.264 45 67.264 c 12.473 0 25.27 -7.487 38.066 -22.264 C 70.27 30.224 57.473 22.737 45 22.737 C 32.528 22.737 19.73 30.224 6.934 45 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                              <path d="M 45 62 c -9.374 0 -17 -7.626 -17 -17 s 7.626 -17 17 -17 s 17 7.626 17 17 S 54.374 62 45 62 z M 45 34 c -6.065 0 -11 4.935 -11 11 s 4.935 11 11 11 s 11 -4.935 11 -11 S 51.065 34 45 34 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            </g>
                          </svg>
                          <svg *ngIf="showPassword" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 256" xml:space="preserve">
                            <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                              <path d="M 13.148 79.853 c -0.768 0 -1.536 -0.293 -2.121 -0.879 c -1.172 -1.171 -1.172 -3.071 0 -4.242 l 63.705 -63.705 c 1.172 -1.172 3.07 -1.172 4.242 0 c 1.172 1.171 1.172 3.071 0 4.242 L 15.269 78.974 C 14.684 79.56 13.916 79.853 13.148 79.853 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                              <path d="M 25.296 67.703 c -0.504 0 -1.012 -0.127 -1.474 -0.388 C 16.105 62.961 8.323 56.098 0.693 46.918 c -0.924 -1.112 -0.924 -2.724 0 -3.835 c 21.533 -25.904 43.565 -32.767 65.485 -20.399 c 0.816 0.461 1.371 1.277 1.498 2.207 s -0.188 1.864 -0.852 2.527 L 27.418 66.824 C 26.841 67.402 26.073 67.703 25.296 67.703 z M 6.933 45 c 5.972 6.896 11.974 12.242 17.891 15.934 l 34.842 -34.842 C 42.131 18.098 24.824 24.311 6.933 45 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                              <path d="M 34.394 58.606 c -0.795 0 -1.558 -0.315 -2.121 -0.878 C 28.873 54.329 27 49.809 27 45 c 0 -9.925 8.075 -18 18 -18 c 4.749 0 9.23 1.833 12.617 5.163 c 0.569 0.56 0.893 1.322 0.897 2.12 s -0.308 1.565 -0.869 2.132 L 36.524 57.719 c -0.562 0.566 -1.326 0.886 -2.124 0.888 C 34.398 58.606 34.396 58.606 34.394 58.606 z M 45 33 c -6.617 0 -12 5.383 -12 12 c 0 2.175 0.574 4.261 1.651 6.085 L 50.995 34.6 C 49.19 33.556 47.136 33 45 33 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                              <path d="M 44.973 73.26 c -3.552 0 -7.104 -0.501 -10.657 -1.505 c -1.594 -0.45 -2.521 -2.108 -2.071 -3.703 c 0.45 -1.594 2.105 -2.524 3.703 -2.07 C 51.38 70.341 67.226 63.287 83.066 45 c -3.977 -4.592 -7.98 -8.514 -11.925 -11.68 c -1.292 -1.037 -1.499 -2.925 -0.462 -4.218 c 1.038 -1.292 2.927 -1.499 4.218 -0.462 c 4.796 3.849 9.644 8.708 14.409 14.442 c 0.925 1.111 0.925 2.724 0 3.835 C 74.743 64.438 59.874 73.26 44.973 73.26 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                              <path d="M 45 63 c -0.386 0 -0.77 -0.013 -1.149 -0.036 c -1.654 -0.101 -2.913 -1.523 -2.812 -3.178 c 0.102 -1.652 1.527 -2.909 3.178 -2.811 C 44.476 56.991 44.737 57 45 57 c 6.617 0 12 -5.383 12 -12 c 0 -0.27 -0.009 -0.538 -0.026 -0.803 c -0.107 -1.653 1.146 -3.081 2.799 -3.188 c 1.665 -0.103 3.082 1.146 3.189 2.799 C 62.987 44.202 63 44.599 63 45 C 63 54.925 54.925 63 45 63 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                            </g>
                          </svg>
                        </button>
                      {/if}
                    </div>
                </div>
                <div class="phone_code" *ngIf="verificationSession.codeSendFlag">
                  <div class="c-dark margin-32-right-table margin-32-bottom-mob">
                    <p>{t}Код отправлен на номер{/t} { { verificationSession.codeSendPhoneMask } }</p>
                  </div>
                    <div class="margin-16-bottom">
                      <label for="twoStepVerificationCode" class="form-label">{t}Введите код{/t}</label>
                      <input
                        type="text"
                        class="form-control"
                        name="twoStepVerificationCode"
                        id="twoStepVerificationCode"
                        [attr.placeholder]="verificationSession.codeDebug && verificationSession.codeSendFlag ? verificationSession.codeDebug : null"
                        [(ngModel)]="verificationSession.code"
                        [value]="verificationSession.code"
                        (keyup.enter)="checkCode()"
                      >
                    </div>

                    <div class="margin-24-top c-dark" *ngIf="verificationSession.codeRefreshDelay">
                        {t}Выслать повторно через:{/t} { { verificationSession.codeRefreshDelayTime } } {t}сек.{/t}
                    </div>

                    <div class="margin-24-top" *ngIf="!verificationSession.codeRefreshDelay">
                      <button
                        type="button"
                        class="button button_default button_small w-100"
                        [attr.disabled]="verificationSession.codeRefreshDelay ? '' : null"
                        (click)="getCode()"
                      >{t}Получить новый код{/t}</button>
                    </div>
                </div>
              </div>
                <div class="form__bottom">
                  <div class="login_buttons">
                    <div class="login_buttons" *ngIf="authType == '0' {if $client_version >= 2.9}|| enableLoginWithPassword{/if}">
                      <button
                        class="button button_medium button_primary margin-24-top"
                        [attr.disabled]="formIsLoading ? '' : null"
                        (click)="loginAuth()"
                      >{t}Войти{/t}
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
                    <div class="phone_buttons" *ngIf="authType == '2' {if $client_version >= 2.9}&& !enableLoginWithPassword{/if}">
                      <button
                        class="button button_medium button_primary margin-24-top"
                        [attr.disabled]="formIsLoading ? '' : null"
                        *ngIf="!verificationSession.codeSendFlag"
                        (click)="phoneAuth()"
                      >{t}Войти{/t}
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
                      <button
                        class="button button_medium button_primary margin-24-top"
                        [attr.disabled]="formIsLoading ? '' : null"
                        *ngIf="verificationSession.codeSendFlag"
                        (click)="checkCode()"
                      >{t}Войти{/t}
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

                      {if $client_version >= 2.9}
                        <div class="margin-32-top w-100 ion-text-center">
                          <a class="registration-link" (click)="loginWithPassword()">{t}Войти с помощью пароля{/t}</a>
                        </div>
                      {/if}

                    </div>
                    <div class="two_step_buttons" *ngIf="authType == '1'">
                      <button
                        class="button button_medium button_primary margin-24-top"
                        [attr.disabled]="formIsLoading ? '' : null"
                        *ngIf="verificationSession.codeSendFlag"
                        (click)="checkCode()"
                      >{t}Войти{/t}
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
                      <button
                        class="button button_medium button_primary margin-24-top"
                        [attr.disabled]="formIsLoading ? '' : null"
                        *ngIf="!verificationSession.codeSendFlag"
                        (click)="loginAuth()"
                      >{t}Войти{/t}
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

                  <div class="margin-32-top w-100 ion-text-center">
                    <a class="registration-link" (click)="changePage('registration')">{t}Регистрация{/t}</a>
                  </div>

                  <div class="margin-32-top w-100 ion-text-center" *ngIf="enablePasswordRecovery">
                    <a class="registration-link" (click)="changePage('recover')">{t}Забыли пароль?{/t}</a>
                  </div>
                </div>
            </div>
          </div>
          <div *ngIf="registrationPage && !inLoading" id="registration">
            <div class="form">
              <div class="margin-16-bottom" *ngIf="errors && errors.length">
                <div class="form__error" *ngFor="let error of errors">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
                    <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
                    <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
                  </svg>
                  <span class="margin-8-left" [innerHTML]="error"></span>
                </div>
              </div>

              <div class="margin-16-bottom">
                <label for="fio" class="form-label">{t}ФИО{/t}</label>
                <input type="text" class="form-control" id="fio" name="fio" [(ngModel)]="registrationFields.fio" [value]="registrationFields.fio">
              </div>
                <div class="margin-16-bottom phone_input" *ngIf="registrationFields.hasOwnProperty('phone')">
                  <label for="phone" class="form-label">{t}Номер телефона{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    id="phone"
                    name="phone"
                    [ngClass]="phoneIsResolved ? 'phone_is_resolved' : ''"
                    [(ngModel)]="registrationFields.phone"
                    [value]="registrationFields.phone"
                    [attr.disabled]="disablePhoneInput() ? '' : null"
                  >
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                  </svg>
                </div>
                <div *ngIf="twoFactorRegister && authType != 0">
                  <div class="margin-16-bottom" *ngIf="verificationSession.codeSendFlag || phoneIsResolved">
                    <button class="button button_default button_small w-100" type="button" (click)="changePhone()">{t}Изменить номер телефона{/t}</button>
                  </div>
                    <div *ngIf="!phoneIsResolved">
                      <div class="margin-16-bottom c-dark" *ngIf="verificationSession.codeRefreshDelay">
                        {t}Выслать повторно через:{/t} <span [innerHTML]="verificationSession.codeRefreshDelayTime"></span> {t}сек.{/t}
                      </div>
                      <div class="margin-16-bottom" *ngIf="!verificationSession.codeRefreshDelay">
                        <button
                          class="button button_default button_small w-100"
                          type="button"
                          [attr.disabled]="verificationSession.codeRefreshDelay ? '' : null"
                          (click)="getCode()"
                        >{t}Получить код{/t}</button>
                      </div>
                        <div *ngIf="verificationSession.codeSendFlag">
                          <div class="margin-16-bottom">
                            <label for="code" class="form-label">{t}Код{/t}</label>
                            <input
                              type="text"
                              class="form-control"
                              id="code" name="code"
                              [attr.placeholder]="verificationSession.codeDebug && verificationSession.codeSendFlag ? verificationSession.codeDebug : null"
                              [(ngModel)]="verificationSession.code"
                              [value]="verificationSession.code"
                            >
                          </div>
                          <div class="margin-16-bottom">
                            <button class="button button_default button_small w-100" type="button" (click)="checkCode()">{t}Подтвердить телефон{/t}</button>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="margin-16-bottom" *ngIf="registrationFields.hasOwnProperty('login')">
                  <label for="login" class="form-label">{t}Логин{/t}</label>
                  <input type="text" class="form-control" id="login" name="login" [(ngModel)]="registrationFields.login" [value]="registrationFields.login">
                </div>
                <div class="margin-16-bottom" *ngIf="registrationFields.hasOwnProperty('eMail')">
                  <label for="email" class="form-label">{t}E-mail{/t}</label>
                  <input type="text" class="form-control" id="email" name="email" [(ngModel)]="registrationFields.eMail" [value]="registrationFields.eMail">
                </div>
                <div class="margin-16-bottom" *ngIf="needCaptcha && captchaUrl">
                  <img [src]="captchaUrl" alt="">
                  <label for="email" class="form-label">{t}Защитный код{/t}</label>
                  <input type="text" class="form-control" id="captcha" name="captcha" [(ngModel)]="registrationFields.captcha" [value]="registrationFields.captcha">
                </div>

                <div *ngIf="additionalFields && additionalFields.length">
                  <div *ngFor="let field of additionalFields">
                    <div>
                      <div class="margin-24-bottom">
                        <label [for]="field.alias" *ngIf="!field.isBoolType()" class="form-label" [innerHTML]="field.title"></label>
                        <select class="form-select" *ngIf="field.isListType()" [(ngModel)]="field.val">
                          <option
                            [value]="value"
                            *ngFor="let value of field.valuesArray"
                            [innerHTML]="value"
                            [attr.selected]="field.currentVal && field.currentVal == value ? true : null"
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

                <div class="margin-16-bottom password-box">
                  <label for="password" class="form-label">{t}Пароль{/t}</label>
                  <input
                    type="password"
                    class="form-control"
                    id="password" name="password"
                    #regPassword
                    [(ngModel)]="registrationFields.openpass"
                    [value]="registrationFields.openpass"
                  >
                  {if $client_version >= 3.5}
                    <button class="show-hide__password" (click)="showHidePassword()">
                      <svg *ngIf="!showPassword" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 256" xml:space="preserve">
                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                          <path d="M 45 73.264 c -14.869 0 -29.775 -8.864 -44.307 -26.346 c -0.924 -1.112 -0.924 -2.724 0 -3.836 C 15.225 25.601 30.131 16.737 45 16.737 c 14.868 0 29.775 8.864 44.307 26.345 c 0.925 1.112 0.925 2.724 0 3.836 C 74.775 64.399 59.868 73.264 45 73.264 z M 6.934 45 C 19.73 59.776 32.528 67.264 45 67.264 c 12.473 0 25.27 -7.487 38.066 -22.264 C 70.27 30.224 57.473 22.737 45 22.737 C 32.528 22.737 19.73 30.224 6.934 45 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 45 62 c -9.374 0 -17 -7.626 -17 -17 s 7.626 -17 17 -17 s 17 7.626 17 17 S 54.374 62 45 62 z M 45 34 c -6.065 0 -11 4.935 -11 11 s 4.935 11 11 11 s 11 -4.935 11 -11 S 51.065 34 45 34 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                        </g>
                      </svg>
                      <svg *ngIf="showPassword" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 256 256" xml:space="preserve">
                        <g style="stroke: none; stroke-width: 0; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: none; fill-rule: nonzero; opacity: 1;" transform="translate(1.4065934065934016 1.4065934065934016) scale(2.81 2.81)" >
                          <path d="M 13.148 79.853 c -0.768 0 -1.536 -0.293 -2.121 -0.879 c -1.172 -1.171 -1.172 -3.071 0 -4.242 l 63.705 -63.705 c 1.172 -1.172 3.07 -1.172 4.242 0 c 1.172 1.171 1.172 3.071 0 4.242 L 15.269 78.974 C 14.684 79.56 13.916 79.853 13.148 79.853 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 25.296 67.703 c -0.504 0 -1.012 -0.127 -1.474 -0.388 C 16.105 62.961 8.323 56.098 0.693 46.918 c -0.924 -1.112 -0.924 -2.724 0 -3.835 c 21.533 -25.904 43.565 -32.767 65.485 -20.399 c 0.816 0.461 1.371 1.277 1.498 2.207 s -0.188 1.864 -0.852 2.527 L 27.418 66.824 C 26.841 67.402 26.073 67.703 25.296 67.703 z M 6.933 45 c 5.972 6.896 11.974 12.242 17.891 15.934 l 34.842 -34.842 C 42.131 18.098 24.824 24.311 6.933 45 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 34.394 58.606 c -0.795 0 -1.558 -0.315 -2.121 -0.878 C 28.873 54.329 27 49.809 27 45 c 0 -9.925 8.075 -18 18 -18 c 4.749 0 9.23 1.833 12.617 5.163 c 0.569 0.56 0.893 1.322 0.897 2.12 s -0.308 1.565 -0.869 2.132 L 36.524 57.719 c -0.562 0.566 -1.326 0.886 -2.124 0.888 C 34.398 58.606 34.396 58.606 34.394 58.606 z M 45 33 c -6.617 0 -12 5.383 -12 12 c 0 2.175 0.574 4.261 1.651 6.085 L 50.995 34.6 C 49.19 33.556 47.136 33 45 33 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 44.973 73.26 c -3.552 0 -7.104 -0.501 -10.657 -1.505 c -1.594 -0.45 -2.521 -2.108 -2.071 -3.703 c 0.45 -1.594 2.105 -2.524 3.703 -2.07 C 51.38 70.341 67.226 63.287 83.066 45 c -3.977 -4.592 -7.98 -8.514 -11.925 -11.68 c -1.292 -1.037 -1.499 -2.925 -0.462 -4.218 c 1.038 -1.292 2.927 -1.499 4.218 -0.462 c 4.796 3.849 9.644 8.708 14.409 14.442 c 0.925 1.111 0.925 2.724 0 3.835 C 74.743 64.438 59.874 73.26 44.973 73.26 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                          <path d="M 45 63 c -0.386 0 -0.77 -0.013 -1.149 -0.036 c -1.654 -0.101 -2.913 -1.523 -2.812 -3.178 c 0.102 -1.652 1.527 -2.909 3.178 -2.811 C 44.476 56.991 44.737 57 45 57 c 6.617 0 12 -5.383 12 -12 c 0 -0.27 -0.009 -0.538 -0.026 -0.803 c -0.107 -1.653 1.146 -3.081 2.799 -3.188 c 1.665 -0.103 3.082 1.146 3.189 2.799 C 62.987 44.202 63 44.599 63 45 C 63 54.925 54.925 63 45 63 z" style="stroke: none; stroke-width: 1; stroke-dasharray: none; stroke-linecap: butt; stroke-linejoin: miter; stroke-miterlimit: 10; fill: var(--rs-color-primary); fill-rule: nonzero; opacity: 1;" transform=" matrix(1 0 0 1 0 0) " stroke-linecap="round" />
                        </g>
                      </svg>
                    </button>
                  {/if}
                </div>
                <div class="margin-16-bottom">
                  <label for="passwordConfirm" class="form-label">{t}Повторите пароль{/t}</label>
                  <input
                    type="password"
                    class="form-control"
                    id="passwordConfirm"
                    name="passwordConfirm"
                    #regConfirmPassword
                    [(ngModel)]="registrationFields.openpassConfirm"
                    [value]="registrationFields.openpassConfirm"
                  >
                </div>

                <div class="form__bottom">
                  {if $client_version >= 2.5}
                    <div class="checkbox margin-24-bottom" *ngIf="usePolicy != 0 && (agreementPersonalData || policyPersonalData)">
                      <input type="checkbox" id="registrationPolicy" name="registrationPolicy" [(ngModel)]="canTrySubmit" (click)="changeCanTrySubmit()">
                      <label class="align-items-center" for="registrationPolicy">
                        <span class="checkbox-attr">
                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                            <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                          </svg>
                        </span>
                        <span>{t}Я даю свое{/t} <a (click)="openPolicy('agreementPersonalData')">{t}согласие на обработку{/t}</a> {t}персональных данных{/t}</span>
                      </label>

                      <div class="margin-32-top fz-12">{t}Регистрируясь, вы соглашаетесь с{/t} <a (click)="openPolicy('policyPersonalData')">{t}пользовательским соглашением и политикой конфиденциальности{/t}</a></div>
                    </div>
                  {else}
                    <div class="checkbox margin-24-bottom" *ngIf="usePolicy == 1 && policyData">
                      <input type="checkbox" id="registrationPolicy" name="registrationPolicy" [(ngModel)]="canTrySubmit" (click)="changeCanTrySubmit()">
                      <label class="align-items-center" for="registrationPolicy">
                        <span class="checkbox-attr">
                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                            <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                          </svg>
                        </span>
                        <span>{t}Согласен на{/t} <a (click)="openPolicy()">{t}обработку персональных данных{/t}</a></span>
                      </label>
                    </div>
                  {/if}

                  <button
                    class="button button_medium button_primary"
                    [attr.disabled]="!canTrySubmit || formIsLoading ? '' : null"
                    (click)="register()"
                  >
                    {t}Зарегистрироваться{/t}
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
          </div>
          <div *ngIf="recoverPage && !inLoading" id="recovery">
            <div class="margin-16-bottom" *ngIf="errors && errors.length">
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
              <div class="form">
                <div class="c-dark ion-text-center" *ngIf="hideForm">
                  <p>{t}На указанный контакт отправлено письмо с дальнейшими инструкциями по восстановлению пароля{/t}</p>
                </div>

                <div *ngIf="!hideForm">
                  <div class="margin-16-bottom">
                    <label for="login" class="form-label" [innerHTML]="authPlaceholder"></label>
                    <input type="text" class="form-control" id="login" name="login" [(ngModel)]="recoverFields.login" [value]="recoverFields.login">
                  </div>
                </div>

                <div *ngIf="!phoneIsResolved && needResolvePhone">
                  <div class="margin-16-bottom c-dark" *ngIf="verificationSession.codeRefreshDelay">
                    {t}Выслать повторно через:{/t} <span [innerHTML]="verificationSession.codeRefreshDelayTime"></span> {t}сек.{/t}
                  </div>
                  <div class="margin-16-bottom" *ngIf="!verificationSession.codeRefreshDelay">
                    <button
                      class="button button_default button_small w-100"
                      type="button"
                      [attr.disabled]="verificationSession.codeRefreshDelay ? '' : null"
                      (click)="getCode()"
                    >{t}Получить код{/t}</button>
                  </div>
                  <div *ngIf="verificationSession.codeSendFlag">
                    <div class="margin-16-bottom">
                      <label for="code" class="form-label">{t}Код{/t}</label>
                      <input
                        type="text"
                        class="form-control"
                        id="code" name="code"
                        [attr.placeholder]="verificationSession.codeDebug && verificationSession.codeSendFlag ? verificationSession.codeDebug : null"
                        [(ngModel)]="verificationSession.code"
                        [value]="verificationSession.code"
                      >
                    </div>
                    <div class="margin-16-bottom">
                      <button class="button button_default button_small w-100" type="button" (click)="checkCode()">{t}Подтвердить телефон{/t}</button>
                    </div>
                  </div>
                </div>

                <div class="form__bottom" *ngIf="!hideForm">
                  <button
                    class="button button_medium button_primary"
                    [attr.disabled]="formIsLoading ? '' : null"
                    (click)="recover()"
                  >
                    {t}Восстановить{/t}
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
          </div>
          <div *ngIf="changePasswordPage && !inLoading" id="changePassword">
            <div class="margin-16-bottom" *ngIf="errors && errors.length">
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
            <div class="form">
              <div class="margin-16-bottom">
                <label for="newPass" class="form-label">{t}Новый пароль{/t}</label>
                <input
                  type="password"
                  class="form-control"
                  name="newPass"
                  id="newPass"
                  [(ngModel)]="changePasswordFields.newPass"
                  [value]="changePasswordFields.newPass"
                >
              </div>
                <div>
                  <label for="newPassConfirm" class="form-label">{t}Повтор нового пароля{/t}</label>
                  <input
                    type="password"
                    class="form-control"
                    name="newPassConfirm"
                    id="newPassConfirm"
                    [(ngModel)]="changePasswordFields.newPassConfirm"
                    [value]="changePasswordFields.newPassConfirm"
                  >
                </div>

                <div class="form__bottom">
                  <div class="login_buttons">
                    <div class="login_buttons">
                      <button
                        class="button button_medium button_primary margin-24-top"
                        [attr.disabled]="formIsLoading ? '' : null"
                        (click)="changePass()"
                      >
                        {t}Сменить пароль{/t}
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
                </div>
            </div>
          </div>
      </div>
  </div>
</ion-content>

