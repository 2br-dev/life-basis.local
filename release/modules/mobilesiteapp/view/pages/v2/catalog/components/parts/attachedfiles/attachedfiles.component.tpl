<div id="attached-files" *ngIf="attachedFiles && attachedFiles.length">
  <div class="attached-files__wrapper">
    <div class="attached-file" *ngFor="let file of attachedFiles">
      <span class="attached-file__title">
        <div class="attached-file__name">{ { file.title } }</div>
        <div class="attached-file__extension">.{ { file.extension } }</div>
        <div class="attached-file__size" *ngIf="file.size">, { { file.size | filesize } }</div>
      </span>
      <div class="attached-file__status">
        <svg *ngIf="file.status == 'process'" class="in-progress" width="24" height="24" viewBox="0 0 44 44" xmlns="http://www.w3.org/2000/svg" stroke="#fff">
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
        <svg (click)="showError(file)" *ngIf="file.status == 'fail'" class="fail" width="24" height="24" viewBox="2 2 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 20C16.4183 20 20 16.4183 20 12C20 7.58172 16.4183 4 12 4C7.58172 4 4 7.58172 4 12C4 16.4183 7.58172 20 12 20Z" fill="#F55858"/>
          <path d="M12 8.0274C11.6548 8.0274 11.375 8.30722 11.375 8.6524V12.6772C11.375 13.0224 11.6548 13.3022 12 13.3022C12.3452 13.3022 12.625 13.0224 12.625 12.6772V8.6524C12.625 8.30722 12.3452 8.0274 12 8.0274Z" fill="#F55858"/>
          <path d="M12 15.7549C12.466 15.7549 12.8438 15.3772 12.8438 14.9112C12.8438 14.4452 12.466 14.0674 12 14.0674C11.534 14.0674 11.1562 14.4452 11.1562 14.9112C11.1562 15.3772 11.534 15.7549 12 15.7549Z" fill="#F55858"/>
        </svg>
        <svg (click)="deleteFile(file.publicHash)" *ngIf="file.status == 'success'" class="delete" width="23" height="23" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"></path>
        </svg>
      </div>
    </div>
  </div>
</div>