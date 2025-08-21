<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Export\Model\ExportType\Avito\OfferType;

use Catalog\Model\CostApi;
use Catalog\Model\Orm\Offer;
use Export\Model\ExportType\AbstractOfferType as AbstractOfferType;
use Export\Model\ExportType\ComplexFieldInterface;
use Export\Model\ExportType\Field as Field;
use Export\Model\Orm\ExportProfile as ExportProfile;
use Catalog\Model\Orm\Product as Product;
use RS\Exception as RSException;
use RS\Http\Request as HttpRequest;

abstract class CommonAvitoOfferType extends AbstractOfferType
{
    /**
     * Запись "Особенных" полей, для данного типа описания
     * Перегружается в потомке. По умолчанию выводит все поля в соответствии с fieldmap
     *
     * @param ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param Product $product - объект товара
     * @param integer $offer_id - индекс комплектации для отображения
     * @throws RSException
     */
    protected function writeEspecialOfferTags(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_id)
    {
        foreach ($this->getEspecialTags() as $field) {
            $this->writeElementFromFieldmap($field, $profile, $writer, $product, $offer_id);
        }

        foreach ($this->addCustomProfileEspecialTags($profile) as $field) {
            if ($field instanceof ComplexFieldInterface) {
                $field->writeSomeTags($writer, $profile, $product, $offer_id);
            } else {
                $value = $this->getElementFromCustomField($field, $profile, $product);
                if (!empty($value)) {
                    $writer->writeElement($field->name, $value);
                }
            }
        }
    }

    /**
     * Получить элемент в соответствии с настройками сопоставления полей экспорта свойствам товара
     *
     * @param Field $field
     * @param ExportProfile $profile
     * @param Product $product
     * @return string
     * @throws RSException
     */
    protected function getElementFromCustomField(Field $field, ExportProfile $profile, Product $product)
    {
        // Получаем объект типа экспорта (в нем хранятся соотвествия полей - custom_fields)
        $result = null;
        $export_type_object = $profile->getTypeObject();
        $converted_custom_fields = $this->getConvertField($export_type_object['custom_fields']);
        if (!empty($converted_custom_fields[$field->name]['prop_id'])) {
            // Идентификатор свойстава товара
            $property_id = (int)$converted_custom_fields[$field->name]['prop_id'];
            // Значение по умолчанию
            $default_value = $converted_custom_fields[$field->name]['value'];
            // Получаем значение свойства товара
            $value = $product->getPropertyValueById($property_id);
            // Если яндекс ожидает строку (true|false)
            if ($field->type == TYPE_BOOLEAN) {
                // Если значение свойства 1 или непустая строка - выводим 'true', в противном случае 'false'

                if ($field->boolAsInt) {
                    $result = $value === 'есть' ? '1' : (!isset($value) ? '1' : '0');
                }
                elseif ((!$value || $value == t('нет')) && (!$default_value || $default_value == t('нет'))) {
                    $result = "false";
                } else {
                    $result = "true";
                }
            } else {
                // Выводим значение свойства, либо значение по умолчанию
                $result = $value === null ? $default_value : $value;
            }
        }

        if (is_callable($field->modifier)) {
            $result = call_user_func($field->modifier, $result, $field, $profile, $product);
        }

        return $result;
    }

    /**
     * Возвращает объект, преобразованный из структуры:
     * [ '0' => ['name' => 'name1', ...], '2' => ['name' => 'name1', ...]]
     * в структуру:
     * [ 'name1' => [...], 'name2' => [...]]
     *
     * @param $custom_fields
     * @return array
     */
    function getConvertField($custom_fields)
    {
        $converted_custom_fields = [];

        foreach ($custom_fields as $item) {
            $converted_custom_fields[$item['name']]['prop_id'] = $item['prop_id'];
            $converted_custom_fields[$item['name']]['value'] = $item['value'];
        }

        return $converted_custom_fields;
    }

    /**
     * Дополняет список "особенных" полей для данного типа описания,
     * созданных пользователем в профиле экспорта.
     *
     * @param ExportProfile $profile
     * @return Field[]
     */
    protected function addCustomProfileEspecialTags(ExportProfile $profile)
    {
        $fields = [];
        if (isset($profile['data']['custom_fields'])) {
            $custom_fields = $profile['data']['custom_fields'];
            foreach ($custom_fields as $custom_field) {
                if ($custom_field['name']) {
                    $field = new Field();
                    $field->name = $custom_field['name'];
                    $fields[$field->name] = $field;
                }
            }
        }

        return $fields;
    }

    /**
    * Дополняет список "особенных" полей, общими для всех типов описания данного типа экспорта
    * 
    * @param $fields - массив "особенных" полей
    * @return Field[]
    */
    protected function addCommonEspecialTags($fields)
    {
        $field = new Field();
        $field->name        = 'AdType';
        $field->title       = t('Вид объявления (AdType)');
        $field->hint        = t('Поле "AdType" (Все категории)<br><br>
                                Одно из значений списка:<br>
                                - Товар приобретен на продажу<br>
                                - Товар от производителя');
        $field->required    = true;
        $fields[$field->name]  = $field;

        $field = new Field();
        $field->name        = 'ListingFee';
        $field->title       = t('Вариант платного размещения (ListingFee)');
        $field->hint        = t('Значение из списка, см. документацию Avito');
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'AdStatus';
        $field->title       = t('Платная услуга, которую нужно применить к объявлению (AdStatus)');
        $field->hint        = t('Значение из списка, см. документацию Avito');
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'AvitoId';
        $field->title       = t('Номер объявления на Avito (AvitoId)');
        $fields[$field->name] = $field;
        
        $field = new Fields\YesNo();
        $field->name        = 'AllowEmail';
        $field->title       = t('Возможность написать сообщение по объявлению через сайт (AllowEmail)');
        $field->hint        = t('Характеристика Да/Нет');
        $field->type        = TYPE_BOOLEAN;
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'ManagerName';
        $field->title       = t('Имя менеджера, контактного лица компании по данному объявлению (ManagerName)');
        $field->hint        = t('Не более 40 символов');
        $fields[$field->name] = $field;
        
        $field = new Field();
        $field->name        = 'ContactPhone';
        $field->title       = t('Контактный телефон по данному объявлению (ContactPhone)');
        $field->hint        = t('Только один российский номер телефона.<br>Должен быть обязательно указан код города или мобильного оператора');
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'Address';
        $field->title       = t('Полный адрес объекта (Address)');
        $field->hint        = t('Поле "Address"<br><br>
                                Максимум 256 символов.<br>
                                Примечание: Улица и номер дома не будут показаны в объявлении<br><br>
                                Пример заполнения: Россия, Тамбовская область, Моршанск, Лесная улица, 7');
        $field->required    = true;
        $fields[$field->name] = $field;

        $field = new Field();
        $field->name        = 'Condition';
        $field->title       = t('Состояние вещи (Condition)');
        $field->hint        = t('Значение из списка, см. документацию Avito');
        $fields[$field->name]  = $field;

        return $fields;
    }

    /**
     * Запись товарного предложения
     *
     * @param ExportProfile $profile - объект профиля экспорта
     * @param \XMLWriter $writer - объект библиотеки для записи XML
     * @param Product $product - объект товара
     * @param integer $offer_id - индекс комплектации для отображения
     * @throws RSException
     */
    public function writeOffer(ExportProfile $profile, \XMLWriter $writer, Product $product, $offer_id)
    {
        $writer->startElement("Ad");
            $this->fireOfferEvent('beforewriteoffer', $profile, $writer, $product, $offer_id);

            $writer->writeElement('Id', $product->id.'x'.$offer_id);
            $this->writeEspecialOfferTags($profile, $writer, $product, $offer_id);
            $prices = $product->getOfferCost($offer_id, $product['xcost']);
        if (!empty($profile['export_cost_id'])) {
            $price = ceil($prices[$profile['export_cost_id']]);
        } else {
            $price = ceil($prices[CostApi::getDefaultCostId()]);
        }
            $writer->writeElement('Price', $price);
            if ($product->hasImage()) {
                $current_offer = $product['offers']['items'][$offer_id];//Текущее предложение
                $this->writeOfferImages($profile, $writer, $product, $current_offer);
            }

            $this->fireOfferEvent('writeoffer', $profile, $writer, $product, $offer_id);
        $writer->endElement();
    }

    /**
    * Добавляет в XML сведения с фото для товара или комплектации
    * 
    * @param ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param Product $product - объект товара
    * @param Offer|false $current_offer - текущая комплектация, объект или false
    */
    protected function writeOfferImages(ExportProfile $profile, \XMLWriter $writer, Product $product, $current_offer)
    {
        $writer->startElement('Images');
        $images = $product->getImages();
        $offer_images = [];
        if ($current_offer) { //Если есть комплектации, посмотрим привязаны ли фото к конкретной комплектации
            $offer_images = $current_offer['photos_arr'];
            if (!empty($offer_images)) {
                foreach ($images as $k => $image) {
                    if (in_array($image['id'], $offer_images)) {
                        $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
                        $writer->startElement('Image');
                            $writer->writeAttribute('url', HttpRequest::commonInstance()->getDomain(true) . $image_url);
                        $writer->endElement();
                    }
                }
            }
        }
        //Если просто товар или фото комплектаций не привязано
        if (!$current_offer || ($current_offer && empty($offer_images))) {
            foreach ($images as $k => $image) {
                $image_url = ($profile['export_photo_originals']) ? $image->getOriginalUrl() : $image->getUrl(800, 800, 'axy');
                $writer->startElement('Image');
                    $writer->writeAttribute('url', HttpRequest::commonInstance()->getDomain(true) . $image_url);
                $writer->endElement();
            }
        }
        $writer->endElement();
    }
}
