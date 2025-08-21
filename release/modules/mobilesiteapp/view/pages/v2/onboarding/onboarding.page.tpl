<ion-content [fullscreen]="true">
  <div id="onboarding">
    <div *ngIf="inLoading">
      <app-onboarding-skeleton></app-onboarding-skeleton>
    </div>
    <div *ngIf="!inLoading && onBoardingList.length > 0">
      <div class="onboarding-container">
        <div class="onboarding-head margin-32-top">
          <div *ngIf="logo">
            <img class="slide__logo" width="260" height="72" [src]="logo.big_url" alt="">
          </div>
          <div>
            <button type="button" (click)="closeOnBoarding()">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
              </svg>
            </button>
          </div>
        </div>
      </div>
      <div class="slides">
        <ion-slides class="onboarding-slider" pager>
          <ion-slide *ngFor="let item of onBoardingList">
            <div class="onboarding-section">
              <div class="slide">
                <div class="slide__title">
                  <h1 [innerHTML]="item.title"></h1>
                </div>
                <div class="slide__img" *ngIf="item.getImage()">
                  <img [src]="item.getImage()['big_url']" alt="">
                </div>
              </div>
            </div>
          </ion-slide>
        </ion-slides>
      </div>
    </div>
  </div>
</ion-content>
