<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Yandex\OfferType\Fields;
use Catalog\Model\Orm\Product;
use \Export\Model\ExportType;
use Export\Model\Orm\ExportProfile;

/**
* Структура данных, описывающая поле в экспортируемом XML документе
*/
class FieldDimensions extends ExportType\Field implements ExportType\ComplexFieldInterface
{
    /**
    * Добавляет необходимую структуру тегов в итоговый XML
    * 
    * @param \Export\Model\Orm\ExportProfile $profile - объект профиля экспорта
    * @param \XMLWriter $writer - объект библиотеки для записи XML
    * @param \Catalog\Model\Orm\Product $product - объект товара
    * @param integer $offer_index - индекс комплектации для отображения
    */
    function writeSomeTags(\XMLWriter $writer, ExportProfile $profile, Product $product, $offer_index = null){
        $export_type_object = $profile->getTypeObject();
        
        // Запись произойдёт только если заданы все 3 габарита, и только 1 раз
        if ($this->name == 'dimensions_l') {
            if (!empty($export_type_object['fieldmap']['dimensions_l']['prop_id'])
                && !empty($export_type_object['fieldmap']['dimensions_w']['prop_id'])
                && !empty($export_type_object['fieldmap']['dimensions_h']['prop_id'])) {

                $dimensions = [];
                $dimensions[] = $this->getValue('dimensions_l', $profile, $product);
                $dimensions[] = $this->getValue('dimensions_w', $profile, $product);
                $dimensions[] = $this->getValue('dimensions_h', $profile, $product);
                $dimensions_line = preg_replace('/[^0-9\.\,\/]/', '', implode('/', $dimensions));
                $dimensions_line = str_replace(',', '.', $dimensions_line);
                $writer->writeElement('dimensions', $dimensions_line);
            } else {
                $writer->writeElement('dimensions', implode('/', [
                        $product->getDimensionsObject()->getLength(),
                        $product->getDimensionsObject()->getWidth(),
                        $product->getDimensionsObject()->getHeight()
                    ]
                ));
            }
        }
    }
}
