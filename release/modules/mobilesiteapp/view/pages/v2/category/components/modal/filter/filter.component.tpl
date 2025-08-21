<div id="modal-filter">
  <ion-header>
    <div class="container">
      <div class="modal-filter-head">
        <div class="modal-filter-head__flex-1">
          <button type="button" class="modal-close" (click)="dismissModal()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd" d="M19.7758 4.22932C20.0767 4.53306 20.0744 5.02325 19.7707 5.32418L13.0323 12L19.7707 18.6758C20.0744 18.9767 20.0767 19.4669 19.7758 19.7707C19.4749 20.0744 18.9847 20.0767 18.6809 19.7758L11.9323 13.0898L5.31907 19.6416C5.01532 19.9425 4.52514 19.9402 4.22421 19.6365C3.92328 19.3327 3.92557 18.8425 4.22932 18.5416L10.8322 12L4.22934 5.4584C3.92559 5.15747 3.92331 4.66728 4.22424 4.36353C4.52516 4.05978 5.01535 4.0575 5.3191 4.35843L11.9323 10.9102L18.6809 4.22421C18.9847 3.92328 19.4748 3.92557 19.7758 4.22932Z" fill="#1B1B1F" stroke="#1B1918" stroke-width="0.3" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <div class="modal-filter-head__title">
          {t}Фильтр{/t}
        </div>
        <div class="d-flex ion-justify-content-end modal-filter-head__flex-1">
          <button type="button" class="modal-filter-head__clear" (click)="clearFilters()">{t}Сбросить{/t}</button>
        </div>
      </div>
    </div>
  </ion-header>
  <div class="inner-content">
    <div class="section" *ngIf="filterData">
      <div class="container" *ngIf="filterData.price || filterData.brands">
        <ul class="filter-accordion">
          <!-- Фильтр по цене -->
          <li *ngIf="filterData.price && filterData.price.isShow()">
            <input
              class="filter-accordion__checker"
              type="checkbox"
              id="cost"
              checked
            >
            <label class="filter-accordion__label" for="cost">{t}Цена{/t}</label>
            <div class="filter-accordion__content">
              <div class="d-flex">
                <div class="margin-16-right">
                  <label for="interval_from" class="form-label">{t}От{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    id="interval_from"
                    name="bfilter[cost][from]"
                    [(ngModel)]="filterData.price.value.lower"
                    (change)="filterData.price.changeValue($event, 'lower')"
                  >
                </div>
                <div>
                  <label for="interval_to" class="form-label">{t}До{/t}</label>
                  <input
                    type="text"
                    class="form-control"
                    id="interval_to"
                    name="bfilter[cost][to]"
                    [(ngModel)]="filterData.price.value.upper"
                  >
                </div>
              </div>
              <ion-range
                min="{ { filterData.price.intervalFrom } }"
                max="{ { filterData.price.intervalTo } }"
                step="1"
                snaps="true"
                ticks="false"
                dualKnobs="true"
                [(ngModel)]="filterData.price.value"
              ></ion-range>
            </div>
          </li>
          <!-- Фильтр по бренду-->
          <li *ngIf="filterData.brands">
            <input
              class="filter-accordion__checker"
              type="checkbox" id="brands"
              checked
            >
            <label class="filter-accordion__label" for="brands">{t}Производитель{/t}</label>
            <div class="filter-accordion__content">
              <ul class="filter-list">
                <li *ngFor="let brand of filterData.brands">
                  <div class="checkbox">
                    <input type="checkbox" id="brand_{ { brand.id } }" name="bfilter[brand][]" [(ngModel)]="brand.checked">
                    <label for="brand_{ { brand.id } }">
                      <span class="checkbox-attr">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                          <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                        </svg>
                      </span>
                      <span class="c-dark">{ { brand.title } }</span>
                    </label>
                  </div>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>

      <!-- Фильтр по характеристикам-->
      <div class="container" *ngIf="filterData.properties && filterData.properties.length">
        <ul class="filter-accordion">
          <li *ngFor="let propertyFilter of filterData.properties">
            <!-- Характеристика Список -->
            <div *ngIf="propertyFilter.type === 'list'">
              <input
                class="filter-accordion__checker"
                type="checkbox"
                id="property_{ { propertyFilter.id } }"
                name="pf[{ { propertyFilter.id } }][]"
                checked
              >
              <label class="filter-accordion__label" for="property_{ { propertyFilter.id } }">{ { propertyFilter.title } }</label>
              <div class="filter-accordion__content">
                <ul class="filter-list">
                  <li *ngFor="let allowedValue of propertyFilter.allowedValues">
                    <div class="checkbox">
                      <input type="checkbox" id="property_value_{ { allowedValue.id } }" [(ngModel)]="allowedValue.checked">
                      <label for="property_value_{ { allowedValue.id } }">
                        <span class="checkbox-attr">
                          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17 4H7C5.34315 4 4 5.34315 4 7V17C4 18.6569 5.34315 20 7 20H17C18.6569 20 20 18.6569 20 17V7C20 5.34315 18.6569 4 17 4ZM7 3C4.79086 3 3 4.79086 3 7V17C3 19.2091 4.79086 21 7 21H17C19.2091 21 21 19.2091 21 17V7C21 4.79086 19.2091 3 17 3H7Z" />
                            <path class="checkbox-attr__check"  fill-rule="evenodd" clip-rule="evenodd" d="M17 8.8564L11.3143 16L7 11.9867L7.82122 10.9889L11.1813 14.1146L16.048 8L17 8.8564Z" />
                          </svg>
                        </span>
                        <span class="c-dark">{ { allowedValue.value } }</span>
                      </label>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <!-- Характеристика Цвет или Картинка -->
            <div *ngIf="propertyFilter.type === 'color' || propertyFilter.type === 'image'">
              <input class="filter-accordion__checker" type="checkbox" id="property_{ { propertyFilter.id } }" checked>
              <label class="filter-accordion__label" for="property_{ { propertyFilter.id } }">{ { propertyFilter.title } }</label>
              <div class="filter-accordion__content">
                <ul class="item-product-choose">
                  <li *ngFor="let allowedValue of propertyFilter.allowedValues">
                    <div class="radio-color">
                      <input type="checkbox" id="property_value_{ { allowedValue.id } }" name="pf[{ { propertyFilter.id } }][]"   (click)="propertyFilter.onTriggerChecked(allowedValue);">
                      <label for="property_value_{ { allowedValue.id } }">
                        <ion-img *ngIf="propertyFilter.type === 'image'" [src]="allowedValue.image.small_url" alt=""></ion-img>
                        <span *ngIf="propertyFilter.type === 'color'" class="radio-bg-color" style="background: { { allowedValue.color } }"></span>
                      </label>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
            <!-- Характеристика Диапозон -->
            <div *ngIf="propertyFilter.type === 'int'">
              <input class="filter-accordion__checker" type="checkbox" id="property_{ { propertyFilter.id } }" checked>
              <label class="filter-accordion__label" for="property_{ { propertyFilter.id } }">{ { propertyFilter.title } }</label>
              <div class="filter-accordion__content">
                <div class="d-flex">
                  <div class="margin-16-right">
                    <label for="interval_from" class="form-label">{t}От{/t}</label>
                    <input
                      type="text"
                      class="form-control"
                      id="property_value_{ { propertyFilter.id } }_interval_from"
                      [(ngModel)]="propertyFilter.value.lower"
                    >
                  </div>
                  <div>
                    <label for="interval_to" class="form-label">{t}До{/t}</label>
                    <input
                      type="text"
                      class="form-control"
                      id="property_value_{ { propertyFilter.id } }_interval_to"
                      [(ngModel)]="propertyFilter.value.upper"
                    >
                  </div>
                </div>
                <ion-range
                  min="{ { propertyFilter.intervalFrom } }"
                  max="{ { propertyFilter.intervalTo } }"
                  step="{ { propertyFilter.getStep() } }"
                  snaps="true"
                  ticks="false"
                  dualKnobs="true"
                  [(ngModel)]="propertyFilter.value"
                ></ion-range>
              </div>
            </div>
            <!-- Характеристика Строка  -->
            <div *ngIf="propertyFilter.type === 'string'">
              <input class="filter-accordion__checker" type="checkbox" id="property_{ { propertyFilter.id } }" checked>
              <label class="filter-accordion__label" for="property_{ { propertyFilter.id } }">{ { propertyFilter.title } }</label>
              <div class="filter-accordion__content">
                <input
                  type="text"
                  class="form-control"
                  id="property_{ { propertyFilter.id } }_value"
                  name="pf[{ { propertyFilter.id } }]"
                  [(ngModel)]="propertyFilter.value"
                >
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
  <ion-footer>
    <div class="container">
      <div class="modal-footer-button">
        <button type="button" class="button button_primary button_small w-100 ion-margin-bottom" (click)="applyFilter()">{t}Применить фильтр{/t}</button>
      </div>
    </div>
  </ion-footer>
</div>
