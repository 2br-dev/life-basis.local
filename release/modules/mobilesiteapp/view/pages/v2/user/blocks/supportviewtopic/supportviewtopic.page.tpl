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
        <div class="head__title" *ngIf="inLoading">{t}Просмотр обращения{/t}</div>
        <div class="head__title" *ngIf="!inLoading && topic" [innerHTML]="topic.title"></div>
        <div class="head__expand_right">
          <span class="topic-number__title" *ngIf="!inLoading && topic" [innerHTML]="topic.number" (click)="updateMessages()"></span>
        </div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true" #content>
  <div id="support-view-topic">
    <div class="section">
      <div class="container">
        <ion-grid>
          <ion-row>
            <ion-col size-lg="5" size-md="6" class="ion-hide-md-down">
              <div class="menu">
                <app-lk-menu></app-lk-menu>
              </div>
            </ion-col>
            <ion-col size-lg="5" size-md="6" offset-lg="1" size="12">
              <div *ngIf="inLoading">
                  <app-support-view-topic-skeleton></app-support-view-topic-skeleton>
              </div>
                <div class="lk-chat__wrapper" *ngIf="!inLoading && topic && messagesData">
                  <div class="margin-16-bottom" *ngFor="let data of messagesData">
                    <div class="messages-group-date margin-24-bottom margin-24-top">
                        <span>{ { formatDate(data.date, 'd MMMM') } }</span>
                    </div>
                    <div class="margin-16-bottom" *ngFor="let message of data.messages">
                      <div class="lk-chat-item" [ngClass]="message.isAdmin == 1 ? 'lk-chat-item_admin' : 'lk-chat-item_client'">
                        <div
                          [ngClass]="message.isAdmin == 0
                          && (message.status == 'process' || message.status == 'fail')
                          ? 'message-in-progress'
                          : ''"
                          (click)="message.isAdmin == 0 && message.status == 'fail' ? openMessageStatus(message) : ''"
                          >
                            <div class="message-status__process" *ngIf="message.isAdmin == 0 && message.status == 'process'">
                              <svg width="25" height="25" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="#fff">
                                <g fill="none" fill-rule="evenodd" stroke-width="2">
                                  <circle cx="22" cy="22" r="1">
                                    <animate attributeName="r" begin="0s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite"/>
                                    <animate attributeName="stroke-opacity" begin="0s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite"/>
                                  </circle>
                                  <circle cx="22" cy="22" r="1">
                                    <animate attributeName="r" begin="-0.9s" dur="1.8s" values="1; 20" calcMode="spline" keyTimes="0; 1" keySplines="0.165, 0.84, 0.44, 1" repeatCount="indefinite"/>
                                    <animate attributeName="stroke-opacity" begin="-0.9s" dur="1.8s" values="1; 0" calcMode="spline" keyTimes="0; 1" keySplines="0.3, 0.61, 0.355, 1" repeatCount="indefinite"/>
                                  </circle>
                                </g>
                              </svg>
                            </div>
                            <div class="message-status__fail" *ngIf="message.isAdmin && message.status == 'fail'">
                              <svg width="25" height="25" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
                                <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
                                <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
                              </svg>
                            </div>
                            <div class="lk-chat-item__body">
                              <div class="lk-chat-item__sender" *ngIf="message.isAdmin == 0">
                                <strong>{t}Вы писали{/t}</strong>
                              </div>
                              <div class="lk-chat-item__sender" *ngIf="message.isAdmin == 1">
                                <strong>{ { message.userName } }</strong>
                              </div>
                              <div class="lk-chat-item__message" [innerHTML]="message.message | safeHtml"></div>
                                <div class="lk-chat-item__attachments" *ngIf="message.attachments && message.attachments.length">
                                  <a
                                    class="lk-chat-item__attachment"
                                    [href]="attachment.getLink()"
                                    *ngFor="let attachment of message.attachments"
                                  >
                                    <svg height="14" width="14" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                      <g fill="none" fill-rule="evenodd" id="Action-/-15---Action,-attach,-attached,-attachment,-document,-file,-paperclip-icon" stroke="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="1">
                                          <path d="M19.5143182,15.6837662 L14.6773864,20.5035713 C12.0060227,23.1654762 7.67488642,23.1654762 5.00352275,20.5035713 C2.33215908,17.8416665 2.33215908,13.5258659 5.00352275,10.863961 L13.0650758,2.83095244 C14.8459849,1.05634919 17.7334091,1.05634919 19.5143182,2.83095244 C21.2952273,4.6055557 21.2952273,7.48275606 19.5143182,9.25735931 L12.2589205,16.487067 C11.3684659,17.3743687 9.92475381,17.3743687 9.03429926,16.487067 C8.1438447,15.5997654 8.1438447,14.1611652 9.03429926,13.2738636 L13.0650758,9.25735931 L16.073465,6.24897007" id="Path" stroke="var(--rs-color-link)" stroke-width="2"/>
                                      </g>
                                    </svg>
                                    <span class="lk-chat-item__attachment-title">{ { attachment.title } }</span>
                                    <span class="lk-chat-item__attachment-extension">.{ { attachment.extension } }</span>
                                    <span class="lk-chat-item__attachment-size">, { { attachment.size | filesize } }</span>
                                  </a>
                                </div>
                                <div class="lk-chat-item__date">
                                  { { formatDate(message.dateof) } }
                                </div>
                            </div>
                          </div>
                      </div>
                    </div>
                  </div>
                </div>
              </ion-col>
            </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>

<div id="support-view-topic-footer" *ngIf="!inLoading">
  <app-attached-files *ngIf="allowAttachments"></app-attached-files>
  <div>
    <div class="new-message__wrapper" [ngClass]="showSendButton() ? 'showSendButton' : ''">
      <div class="new-message__input">
        <textarea
          [(ngModel)]="newMessage"
          [value]="newMessage"
          (input)="onMessageChange($event)"
          (focus)="onMessageFocus($event)"
          rows="1"
          placeholder="{t}Новое сообщение{/t}"
        ></textarea>
      </div>
      <app-file-upload *ngIf="allowAttachments"></app-file-upload>
    </div>
    <div
      class="send-new-message"
      (click)="preparesNewMessage()"
      *ngIf="showSendButton()"
    >
      <svg height="24" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <g id="icons">
            <path d="M21.5,11.1l-17.9-9C2.7,1.7,1.7,2.5,2.1,3.4l2.5,6.7L16,12L4.6,13.9l-2.5,6.7c-0.3,0.9,0.6,1.7,1.5,1.2l17.9-9   C22.2,12.5,22.2,11.5,21.5,11.1z" id="send" fill="#bbbbbb"/>
        </g>
      </svg>
    </div>
  </div>
</div>
