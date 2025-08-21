<div id="file-upload">
  <div class="new-message__attach" [style.justifyContent]="position" (click)="onClickAddAttachment()">
    <input class="fileInputNode" type="file" (change)="onFileInputChange($event)">
    <svg height="24" width="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <g fill="none" fill-rule="evenodd" id="Action-/-15---Action,-attach,-attached,-attachment,-document,-file,-paperclip-icon" stroke="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="1">
        <path d="M19.5143182,15.6837662 L14.6773864,20.5035713 C12.0060227,23.1654762 7.67488642,23.1654762 5.00352275,20.5035713 C2.33215908,17.8416665 2.33215908,13.5258659 5.00352275,10.863961 L13.0650758,2.83095244 C14.8459849,1.05634919 17.7334091,1.05634919 19.5143182,2.83095244 C21.2952273,4.6055557 21.2952273,7.48275606 19.5143182,9.25735931 L12.2589205,16.487067 C11.3684659,17.3743687 9.92475381,17.3743687 9.03429926,16.487067 C8.1438447,15.5997654 8.1438447,14.1611652 9.03429926,13.2738636 L13.0650758,9.25735931 L16.073465,6.24897007" id="Path" stroke="#bbbbbb" stroke-width="2"/>
      </g>
    </svg>
    <span *ngIf="type != 'short'" [innerHTML]="attachDescription"></span>
  </div>
</div>