<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Export\Model\ExportType\Avito;

use \RS\Orm\Type as Type;

class Avito extends \Export\Model\ExportType\AbstractType
{
    public function _init()
    {
        return parent::_init()->append([
            '__help__' => new Type\MixedType([
                'description' => t(''),
                'visible' => true,
                'template' => '%export%/form/exporttype/avito/help.tpl'
            ]),
            t('Произвольные поля'),
                'custom_fields' => (new Type\VariableList())
                    ->setDescription(t('Произвольные поля'))
                    ->setArrayWrap('data')
                    ->setTableFields([
                        new Type\VariableList\TextVariableListField('name', t('Имя переменной поля')),
                        new Type\VariableList\SelectVariableListField(
                            'prop_id',
                            t('Характеристика'),
                            ['0' => t('- Не выводить -'), '-1' => t('- Задано вручную -')],
                            ['\Catalog\Model\PropertyApi', 'staticSelectList']
                        ),
                        new Type\VariableList\TextVariableListField('value', t('Значение по умолчанию')),
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
        return t('Avito');
    }
    
    /**
    * Возвращает описание типа экспорта для администратора. Возможен HTML
    * 
    * @return string
    */
    public function getDescription()
    {
        return t('Экспорт для сервиса "Avito Автозагрузка"');
    }
    
    /**
    * Возвращает идентификатор данного типа экспорта. (только англ. буквы)
    * 
    * @return string
    */
    public function getShortName()
    {
        return 'avito';
    }
    
    /**
    * Возвращает корневой тэг документа
    * 
    * @return string
    */
    protected function getRootTag()
    {
        return "Ads";
    }
    
    /**
    * Возвращает список классов типов описания
    * 
    * @param string $export_type_name - идентификатор типа экспорта
    * @return \Export\Model\ExportType\AbstractOfferType[]
    */
    protected function getOfferTypesClasses()
    {
        return [
            new OfferType\ConsumerElectronics(),
            new OfferType\PersonalItems(),
        ];
    }

    /**
     * Возвращает экспортированные данные (XML)
     *
     * @return string
     * @throws \RS\Db\Exception
     * @throws \RS\Event\Exception
     * @throws \RS\Exception
     * @throws \RS\Orm\Exception
     */
    public function export()
    {
        $profile = $this->getExportProfile();
        $writer = new \Export\Model\MyXMLWriter();
        $writer->openURI($profile->getTypeObject()->getCacheFilePath());
        $writer->setIndent(true);
        $writer->setIndentString("    ");
        $writer->startElement($this->getRootTag());
            $writer->writeAttribute('formatVersion', '3');
            $writer->writeAttribute('target', 'Avito.ru');
            
            $this->exportOffers($profile, $writer);
            $this->fireAfterAllOffersEvent('afteroffersexport',$profile,$writer);
        $writer->endElement();
        $writer->endDocument();
        $writer->flush();
        return $profile->getTypeObject()->getCacheFilePath();
    }
}
