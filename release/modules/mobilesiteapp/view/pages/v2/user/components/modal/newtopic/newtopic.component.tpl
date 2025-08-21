{if $client_version <= 3.7}
  <div id="modal-new-topic">
    <ion-header>
      <div class="container">
        <div class="modal-new-topic-head">
          <div class="modal-new-topic-head__flex-1">
            <button type="button" class="modal-close" (click)="dismissModal()">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="modal-new-topic-head__title">
            Создать обращение
          </div>
          <div class="modal-new-topic-head__flex-1"></div>
        </div>
      </div>
    </ion-header>
    <div class="inner-content">
      {literal}
        <div class="section">
          <div class="container">
            <div class="margin-16-bottom" *ngIf="errors && errors.length">
              <div class="form__error" *ngFor="let error of errors">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
                  <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
                  <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
                </svg>
                <span class="margin-8-left">
                                {{error}}
                            </span>
              </div>
            </div>
            <div class="form">
              <div>
                <div class="margin-16-bottom">
                  <label for="title" class="form-label">Тема обращения</label>
                  <input type="text" class="form-control" id="title" [(ngModel)]="newTopic.title">
                </div>
                <div class="margin-24-bottom">
                  <label for="message" class="form-label">Опишите свой вопрос</label>
                  <textarea class="form-control" name="message" id="message" cols="20" rows="8" [(ngModel)]="newTopic.message"></textarea>
                </div>
              </div>
              <app-file-upload *ngIf="allowAttachments" [type]="long" [position]="'flex-start'"></app-file-upload>
            </div>
          </div>
        </div>
      {/literal}
    </div>
    <ion-footer>
      <div class="container">
        <app-attached-files *ngIf="allowAttachments"></app-attached-files>

        <div class="modal-footer-button">
          <button
                  class="button button_primary button_small w-100 ion-margin-bottom"
                  [attr.disabled]="formIsLoading ? '' : null"
                  (click)="createTopic()"
          >
            Отправить
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
  </div>
{else}
  <ion-header>
    <ion-toolbar>
      <div class="container">
        <div class="modal-new-topic-head">
          <div class="modal-new-topic-head__flex-1">
            <button type="button" class="modal-close" (click)="dismissModal()">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
          <div class="modal-new-topic-head__title">
            {t}Создать обращение{/t}
          </div>
          <div class="modal-new-topic-head__flex-1"></div>
        </div>
      </div>
    </ion-toolbar>
  </ion-header>
  <ion-content>
    <div class="section">
      <div class="container">
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
          <div>
            <div class="margin-16-bottom">
              <label for="title" class="form-label">{t}Тема обращения{/t}</label>
              <input type="text" class="form-control" id="title" [(ngModel)]="newTopic.title">
            </div>
            <div class="margin-24-bottom">
              <label for="message" class="form-label">{t}Опишите свой вопрос{/t}</label>
              <textarea class="form-control" name="message" id="message" cols="20" rows="8" [(ngModel)]="newTopic.message"></textarea>
            </div>
          </div>
          <app-file-upload *ngIf="allowAttachments" [type]="long" [position]="'flex-start'"></app-file-upload>
        </div>
      </div>
    </div>
  </ion-content>
  <ion-footer>
    <div class="container">
      <app-attached-files *ngIf="allowAttachments"></app-attached-files>

      <div class="modal-footer-button">
        <button
                class="button button_primary button_small w-100 ion-margin-bottom"
                [attr.disabled]="formIsLoading ? '' : null"
                (click)="createTopic()"
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
{/if}

