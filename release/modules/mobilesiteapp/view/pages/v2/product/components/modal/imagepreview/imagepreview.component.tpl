<div id="image-preview" class="content_wrapper">
 <div class="header">
   <button (click)="dismissModal()">
       <ion-icon name="close" slot="start"></ion-icon>
   </button>
 </div>

 <ion-content>
   <swiper [zoom]="true" #swiper>
     <ng-template swiperSlide>
       <div class="swiper-zoom-container">
         <img [src]="image.big_url">
       </div>
     </ng-template>
   </swiper>
 </ion-content>

 <ion-footer>
   <ion-row>
     <ion-col size="4" class="ion-text-center">
       <button (click)="zoom(false)">
         <ion-icon name="remove" slot="start"></ion-icon>
       </button>
     </ion-col>
     <ion-col size="4" class="ion-text-center"></ion-col>
     <ion-col size="4" class="ion-text-center">
       <button (click)="zoom(true)">
           <ion-icon name="add" slot="start"></ion-icon>
       </button>
     </ion-col>
   </ion-row>
 </ion-footer>
</div>