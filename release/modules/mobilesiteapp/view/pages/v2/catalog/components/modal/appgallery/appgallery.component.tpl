<div id="modal-app-gallery">
  <div class="inner-content">
    <div class="section">
      <div class="container">
        <ul class="modal-app-gallery-list">
          <li class="modal-app-gallery-link" (click)="takePhoto()">
            <span>{t}Сделать фото{/t}</span>
          </li>
          <li class="modal-app-gallery-link" (click)="selectImage()">
            <span>{t}Выбрать из галереи{/t}</span>
          </li>
          <li class="modal-app-gallery-link">
            <label for="inputTypeFile">
              <span>{t}Выбрать из хранилища{/t}</span>
            </label>
            <input id="inputTypeFile" name="inputTypeFile" type="file" (change)="onFileInputChange($event)">
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>