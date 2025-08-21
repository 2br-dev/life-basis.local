<div class="story__invisible-button_wrapper">
  <div
    class="story__invisible-button"
    (click)="navigateToStory()"
    [innerHTML]="story.buttonText"
    [style.color]="story.buttonColor"
    [style.background]="story.buttonBackgroundColor"
  ></div>
  <div
    class="story__invisible-button-arrow"
    [style.borderColor]="'transparent transparent ' + story.buttonBackgroundColor + ' transparent'"
  ></div>
</div>