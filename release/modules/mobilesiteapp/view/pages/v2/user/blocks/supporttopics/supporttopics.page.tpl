<ion-header>
  <ion-toolbar>
    <div class="container">
      <div class="head">
        <div class="head__expand">
          <button class="head__button" type="button" (click)="navigateBack()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M16.1862 19.7188C16.6046 19.3439 16.6046 18.7361 16.1862 18.3612L9.08666 12L16.1862 5.63882C16.6046 5.26392 16.6046 4.65608 16.1862 4.28118C15.7678 3.90627 15.0894 3.90627 14.671 4.28118L6.81381 11.3212C6.3954 11.6961 6.3954 12.3039 6.81381 12.6788L14.671 19.7188C15.0894 20.0937 15.7678 20.0937 16.1862 19.7188Z"
                fill="var(--rs-color-dark)"/>
            </svg>
          </button>
        </div>
        <div class="head__title">{t}Обращения в поддержку{/t}</div>
        <div class="head__expand_right"></div>
      </div>
    </div>
  </ion-toolbar>
</ion-header>

<ion-content [fullscreen]="true">
  <div id="support-topics">
    <ion-refresher *ngIf="canRefreshPage" slot="fixed" (ionRefresh)="doRefresh($event)">
        <ion-refresher-content refreshingSpinner="circles">
        </ion-refresher-content>
    </ion-refresher>
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
               <app-support-topics-skeleton></app-support-topics-skeleton>
             </div>
             <div *ngIf="!inLoading && topics && !topics.length">
               <ion-grid>
                 <ion-row class="ion-align-items-center">
                   <div class="ion-text-center w-100">
                     <div [innerHTML]="getSvgByKey('topics.empty')"></div>
                   </div>
                 </ion-row>
               </ion-grid>

               <div class="ion-text-center margin-32-top w-100">
                 <h2 class=" margin-32-top-mob">{t}Здесь пока пусто{/t}</h2>
                 <div class="c-dark margin-24-bottom">
                   <p>{t}Создайте тикет с вашим обращением, если у вас появились вопросы{/t}</p>
                 </div>
               </div>
             </div>
               <ion-list
                 class="margin-24-left-table topics__wrapper"
                 *ngIf="!inLoading && topics && topics.length"
                 #topicsNode
               >
                 <div class="item" *ngFor="let topic of topics">
                   <ion-item-sliding>
                     <ion-item (click)="navigateTo('/tabs/user/support/' + topic.id)">
                       <ion-label>
                         <div class="topic-item">
                           <div class="topic-item__top">
                             <div class="topic-item__title-node">
                               <span class="topic-item__title">{ { topic.title } }</span>
                               <span class="topic-item__new-messages" *ngIf="topic.newcount > 0" [innerHTML]="topic.newcount"></span>
                             </div>
                             <div class="topic-item__number" [innerHTML]="topic.number"></div>
                           </div>
                           <div class="topic-item__last-message" [innerHTML]="topic.lastMessage.message | safeHtml"></div>
                         </div>
                       </ion-label>
                     </ion-item>

                     <ion-item-options side="end">
                       <ion-item-option color="danger" (click)="deleteTopic(topic.id)">
                         <svg width="25" height="25" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                           <path d="M9.99935 6C9.81523 6 9.66602 6.14279 9.66602 6.31898V12.3477C9.66602 12.5237 9.81523 12.6667 9.99935 12.6667C10.1835 12.6667 10.3327 12.5237 10.3327 12.3477V6.31898C10.3327 6.14279 10.1835 6 9.99935 6Z" fill="var(--rs-color-white)"></path>
                           <path d="M5.99935 6C5.81523 6 5.66602 6.14279 5.66602 6.31898V12.3477C5.66602 12.5237 5.81523 12.6667 5.99935 12.6667C6.18346 12.6667 6.33268 12.5237 6.33268 12.3477V6.31898C6.33268 6.14279 6.18346 6 5.99935 6Z" fill="var(--rs-color-white)"></path>
                           <path d="M3.26116 5.30345V12.9968C3.26116 13.4515 3.43566 13.8785 3.7405 14.1849C4.04393 14.4922 4.46621 14.6666 4.90815 14.6673H11.0912C11.5333 14.6666 11.9555 14.4922 12.2589 14.1849C12.5637 13.8785 12.7382 13.4515 12.7382 12.9968V5.30345C13.3442 5.14976 13.7368 4.59038 13.6558 3.99624C13.5746 3.40223 13.0449 2.95787 12.4179 2.95775H10.7447V2.56743C10.7467 2.2392 10.6108 1.92402 10.3677 1.69214C10.1245 1.46039 9.79411 1.33134 9.45059 1.33403H6.54876C6.20524 1.33134 5.87487 1.46039 5.63169 1.69214C5.38851 1.92402 5.25269 2.2392 5.2546 2.56743V2.95775H3.58144C2.9544 2.95787 2.42477 3.40223 2.34358 3.99624C2.26252 4.59038 2.65518 5.14976 3.26116 5.30345ZM11.0912 14.0428H4.90815C4.34941 14.0428 3.91475 13.5842 3.91475 12.9968V5.33089H12.0846V12.9968C12.0846 13.5842 11.6499 14.0428 11.0912 14.0428ZM5.90819 2.56743C5.90602 2.40484 5.97291 2.24835 6.09367 2.13357C6.21431 2.01879 6.37847 1.95573 6.54876 1.95854H9.45059C9.62088 1.95573 9.78504 2.01879 9.90568 2.13357C10.0264 2.24823 10.0933 2.40484 10.0912 2.56743V2.95775H5.90819V2.56743ZM3.58144 3.58226H12.4179C12.7428 3.58226 13.0061 3.8339 13.0061 4.14432C13.0061 4.45475 12.7428 4.70638 12.4179 4.70638H3.58144C3.25656 4.70638 2.99321 4.45475 2.99321 4.14432C2.99321 3.8339 3.25656 3.58226 3.58144 3.58226Z" fill="var(--rs-color-white)"></path>
                           <path d="M7.99935 6C7.81523 6 7.66602 6.14279 7.66602 6.31898V12.3477C7.66602 12.5237 7.81523 12.6667 7.99935 12.6667C8.18346 12.6667 8.33268 12.5237 8.33268 12.3477V6.31898C8.33268 6.14279 8.18346 6 7.99935 6Z" fill="var(--rs-color-white)"></path>
                         </svg>
                       </ion-item-option>
                     </ion-item-options>
                   </ion-item-sliding>
                 </div>
                   <ion-infinite-scroll threshold="100px" *ngIf="showMoreButton" (ionInfinite)="showMore($event)">
                     <ion-infinite-scroll-content>
                     </ion-infinite-scroll-content>
                   </ion-infinite-scroll>
               </ion-list>
           </ion-col>
          </ion-row>
        </ion-grid>
      </div>
    </div>
  </div>
</ion-content>
<div class="new-topic__wrapper">
  <div class="new-topic" (click)="newTopic()">
    <svg height="18" viewBox="0 0 18 18" width="18" xmlns="http://www.w3.org/2000/svg">
      <g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1">
        <g fill="var(--rs-color-white)" id="Core" transform="translate(-213.000000, -129.000000)">
          <g id="create" transform="translate(213.000000, 129.000000)">
            <path d="M0,14.2 L0,18 L3.8,18 L14.8,6.9 L11,3.1 L0,14.2 L0,14.2 Z M17.7,4 C18.1,3.6 18.1,3 17.7,2.6 L15.4,0.3 C15,-0.1 14.4,-0.1 14,0.3 L12.2,2.1 L16,5.9 L17.7,4 L17.7,4 Z" id="Shape"/>
          </g>
        </g>
      </g>
    </svg>
  </div>
</div>