<ion-header>
  <div id="modal-stories-header">
    <div class="container">
      <div class="modal-stories-head__wrapper"></div>
    </div>
  </div>
</ion-header>

<div id="modal-stories-content">
  <div class="control-block" [ngClass]="haveVisibleButton ? 'haveVisibleButton' : ''">
    <div class="previous-story"></div>
    <div class="story-content"></div>
    <div class="next-story"></div>
  </div>

  <div class="stories-wrapper">
    <swiper #storiesSwiper [config]="config">
      <ng-template swiperSlide *ngFor="let story of currentStoriesGroup.stories">
        <div class="story__id" [attr.storyId]="story.id"></div>
        <div class="story__video" *ngIf="story.getType() === 'video'">
          <video [src]="story.backgroundVideo" preload="auto" muted="true" playsinline></video>
        </div>
        <div class="story__image" *ngIf="story.getBackgroundImage() && story.getType() === 'image'">
          <img  [src]="story.getBackgroundImage()['original_url']">
        </div>

        <div class="story__color" *ngIf="story.getType() === 'color'" [style.background]="story.backgroundColor"></div>

        <div class="story__content">
          <div class="story__content-image" *ngIf="story.getMainImage()">
            <img  [src]="story.getMainImage()['middle_url']">
          </div>

          <h3
            class="story__content-title"
            [style.color]="story.titleColor"
            [innerHTML]="story.title"
          ></h3>

          <div
            class="story__content-description"
            [innerHTML]="story.description | safeHtml"
            [style.color]="story.descriptionColor"
          ></div>

          <div
            class="button w-100 story__content-button"
            *ngIf="story.getButtonType() == 'visible' && story.buttonText && story.getButtonDirection()"
            (click)="navigateToStory(story)"
            [innerHTML]="story.buttonText"
            [style.color]="story.buttonColor"
            [style.background]="story.buttonBackgroundColor"
          ></div>
        </div>
      </ng-template>
    </swiper>
  </div>
</div>

