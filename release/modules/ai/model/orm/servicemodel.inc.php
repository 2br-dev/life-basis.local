<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Orm;

use RS\Orm\Type;
use RS\Orm\OrmObject;

/**
 * Справочник моделей для GPT-сервисов
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $service_type Тип API
 * @property string $title Название модели
 * @property string $model_key ID модели в сервисе
 * --\--
 */
class ServiceModel extends OrmObject
{
    protected static $table = 'ai_service_model';

    function _init()
    {
        parent::_init()->append([
            'service_type' => (new Type\Varchar())
                ->setDescription(t('Тип API'))
                ->setMaxLength(50),
            'title' => (new Type\Varchar())
                ->setDescription(t('Название модели')),
            'model_key' => (new Type\Varchar())
                ->setMaxLength(100)
                ->setDescription(t('ID модели в сервисе'))
        ]);

        $this->addIndex(['service_type', 'model_key'], self::INDEX_UNIQUE);
    }
}