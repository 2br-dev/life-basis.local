<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Sbermegamarket;

use Catalog\Model\CostApi;
use Export\Model\ExportType\AbstractOfferType;
use Export\Model\ExportType\Yandex\Yandex;
use Export\Model\MyXMLWriter;
use \RS\Orm\Type;

class Sbermegamarket extends Yandex
{
    public function _init()
    {
        return $this->getPropertyIterator()->append([
            t('Основные'),
            'export_cost_id' => new Type\Integer([
                'description' => t('Выгружаемый тип цен'),
                'list' => [['\Catalog\Model\CostApi', 'staticSelectList'], [0 => t('Не выбрано')]],
                'default' => CostApi::getDefaultCostId()
            ]),
            'products' => new Type\ArrayList([
                'description' => t('Список товаров'),
                'template' => '%export%/form/profile/products.tpl'
            ]),
            'only_available' => new Type\Integer([
                'description' => t('Выгружать только товары, которые в наличии?'),
                'checkboxView' => [1, 0],
            ]),
            'consider_warehouses' => new Type\ArrayList([
                'description' => t('Какие склады учитывать при определении наличия товара?'),
                'list' => [['\Catalog\Model\WareHouseApi', 'staticSelectList'], [0 => t('- Все -')]],
                'attr' => [[
                    'multiple' => true,
                    'size' => 10,
                ]],
            ]),
            'min_cost' => new Type\Integer([
                'description' => t('Выгружать товары с ценой из диапазона'),
                'maxLength' => 11,
                'template' => '%export%/form/profile/cost_range.tpl',
                'hint' => t('Диапазон цен указывайте в базовой валюте.')
            ]),
            'max_cost' => new Type\Integer([
                'description' => t('Максимальная цена.'),
                'maxLength' => 11,
                'visible' => false
            ]),
            'no_export_offers' => new Type\Integer([
                'description' => t('Не выгружать комплектации товаров'),
                'checkboxview' => [1, 0],
            ]),
            'export_photo_originals' => new Type\Integer([
                'description' => t('Выгружать оригиналы фото (без водяного знака)'),
                'checkboxview' => [1, 0],
            ]),
            'use_full_description' => new Type\Integer([
                'description' => t('Использовать Полное описание (с разрешенными СберМегаМаркетом HTML тегами) вместо короткого'),
                'checkboxview' => [1,0],
            ]),
            'no_export_offers_title' => new Type\Integer([
                'description' => t('Не выгружать названия комплектаций товаров'),
                'checkboxview' => [1,0],
            ]),
            'no_export_offers_props' => new Type\Integer([
                'description' => t('Не выгружать характеристики комплектаций товаров'),
                'checkboxview' => [1,0],
            ]),
            'vendor_code_from_barcode' => new Type\Integer([
                'description' => t('Выгружать артикул товара в теге "vendor_code"'),
                'checkboxview' => [1, 0],
            ]),
            'delivery_options_days' => new Type\Varchar([
                'description' => t('Срок доставки по вашему региону (delivery-options)'),
                'hint' => t('Указывается в днях, например: 1 или 1-3<br>
                                элемент delivery-options попадает в выгрузку только при указании обоих его полей<br>
                                эти 2 поля являются обязательными, если вы хотите указывазать delivery-options у товаров')
            ]),
            'delivery_options_order_before' => new Type\Varchar([
                'description' => t('Время, до которого нужно сделать заказ, чтобы получить его в этот срок (delivery-options)'),
                'hint' => t('Необязательный. Указывается в часах, например: 1 это 01:00, а 18 это 18:00<br>
                                    элемент delivery-options попадает в выгрузку только при указании двух предыдущих полей указанных выше')
            ]),

            t('Поля данных'),
            'offer_type' => new Type\Varchar([
                'description' => t('Тип описания'),
                'ListFromArray' => [$this->getOfferTypeNames()],
            ]),
            'fieldmap' => new Type\MixedType([
                'description' => t(''),
                'visible' => true,
                'template' => '%export%/form/profile/fieldmap.tpl'
            ])
        ]);

    }


    /**
     * Возвращает название типа экспорта
     *
     * @return string
     */
    public function getTitle()
    {
        return t('СберМегаМаркет');
    }

    /**
     * Возвращает описание типа экспорта для администратора. Возможен HTML
     *
     * @return string
     */
    public function getDescription()
    {
        return t('Экспорт в формате XML для СберМегаМаркет');
    }

    /**
     * Возвращает идентификатор данного типа экспорта. (только англ. буквы)
     *
     * @return string
     */
    public function getShortName()
    {
        return 'sbermegamarket';
    }

    /**
     * Возвращает список классов типов описания
     *
     * @return AbstractOfferType[]
     */
    protected function getOfferTypesClasses()
    {
        return [
            new OfferType\Xml(),
        ];
    }

    public function export()
    {
        $profile = $this->getExportProfile();

        $writer = new MyXMLWriter();
        $writer->openURI($profile->getTypeObject()->getCacheFilePath());
        $writer->startDocument('1.0', self::CHARSET);
        $writer->setIndent(true);
        $writer->setIndentString("    ");
        $writer->startElement($this->getRootTag());
        $writer->writeAttribute('date', date('Y-m-d H:i'));
        $writer->startElement("shop");
        $writer->writeElement('name', \RS\Helper\Tools::teaser(\RS\Site\Manager::getSite()->title, 20, false));
        $writer->writeElement('company', \RS\Config\Loader::getSiteConfig()->firm_name);
        $writer->writeElement('url', \RS\Http\Request::commonInstance()->getDomain(true));
        $this->exportCurrencies($profile, $writer);
        $this->exportCategories($profile, $writer);

        if ($profile->data['delivery_options_days'] != '' ) {
            $writer->startElement('shipment-options');
            $writer->startElement('option');
            $writer->writeAttribute('days', $profile->data['delivery_options_days']);
            if (!empty($profile->data['delivery_options_order_before'])){
                $writer->writeAttribute('order-before', $profile->data['delivery_options_order_before']);
            }
            $writer->endElement();
            $writer->endElement();
        }

        $writer->startElement('offers');
        $this->exportOffers($profile, $writer);
        $writer->endElement();
        $this->fireAfterAllOffersEvent('afteroffersexport',$profile,$writer);
        $writer->endElement();
        $writer->endElement();
        $writer->endDocument();
        $writer->flush();
        return file_get_contents($profile->getTypeObject()->getCacheFilePath());
    }
}
