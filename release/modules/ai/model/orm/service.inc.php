<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Orm;

use Ai\Model\Exception;
use Ai\Model\ServiceApi;
use Ai\Model\ServiceType\AbstractServiceType;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM-Объект описывает одного поставщика GPT-сервиса (OpenAI, DeekSeek, ...)
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property string $title Название GPT-сервиса
 * @property string $type Тип API
 * @property integer $sortn Порядковый номер
 * @property array $settings Параметры GPT-сервиса
 * @property string $_settings Параметры GPT-сервиса
 * --\--
 */
class Service extends OrmObject
{
    const SERVICE_READYSCRIPT_ID = -1;

    protected static $table = 'ai_service';
    private $service_type;

    function _init()
    {
        parent::_init()->append([
            'title' => (new Type\Varchar())
                ->setDescription(t('Название GPT-сервиса'))
                ->setHint(t('Придумайте его. Используется внутри системы.')),
            'type' => (new Type\Varchar())
                ->setDescription(t('Тип API'))
                ->setHint(t('Модуль (протокол), который будет взаимодействовать с сервисом. Установить модули для различных gpt-сервисов, вы можете в маркетплейсе ReadyScript.'))
                ->setMaxLength(50)
                ->setChecker('ChkEmpty', t('Выберите тип API. Расширить список типов API вы можете устанавливая дополнительные модули из маркетплейса ReadyScript.'))
                ->setList('\Ai\Model\ServiceApi::getServiceTypeTitles', ['' => t('- Не выбрано -')], true)
                ->setTemplate('%ai%/admin/service/type.tpl'),
            'sortn' => (new Type\Integer())
                ->setVisible(false)
                ->setDescription(t('Порядковый номер')),
            'settings' => (new Type\ArrayList())
                ->setVisible(false)
                ->setDescription(t('Параметры GPT-сервиса'))
                ->setChecker([__CLASS__, 'settingsChecker']),
            '_settings' => (new Type\Text())
                ->setVisible(false)
                ->setDescription(t('Параметры GPT-сервиса')),
        ]);
    }

    /**
     * Проверяет параметры сервиса
     *
     * @param $_this
     * @param $value
     * @return bool|string
     */
    public static function settingsChecker($_this, $value)
    {
        $service_type = ServiceApi::getServiceTypeById($_this['type'], $_this);
        if ($service_type) {
            $form_object = $service_type->getSettingsFormObject();
            $form_object->getFromArray((array)$_this['settings']);
            if (!$form_object->validate()) {
                foreach($form_object->getErrorsByForm() as $form => $errors) {
                    foreach($errors as $error) {
                        $_this->addError($error, 'settings');
                    }
                }
            }
        }
        return true;
    }

    /**
     * Возвращает объект типа сервиса, связанного с данным профилем
     *
     * @return AbstractServiceType
     * @throws Exception
     */
    public function getServiceTypeObject()
    {
        $types = ServiceApi::getServiceTypes();
        if (isset($types[$this['type']])) {
            if (!isset($this->service_type) || $this->service_type->getId() != $this['type']) {
                $this->service_type = new ($types[$this['type']])($this);
            }
            return $this->service_type;
        }

        throw new Exception(t('Модуль GPT-сервиса `%type` не найден. Выберите другой сервис для выполнения запросов.', [
            'type' => $this['type']
        ]));
    }

    /**
     * Обработчик перед сохранением
     *
     * @return void
     */
    public function beforeWrite($save_flag)
    {
        if ($save_flag == self::INSERT_FLAG) {
            //Устанавливаем максимальный сортировочный индекс
            $this['sortn'] = Request::make()
                    ->select('MAX(sortn) as max')
                    ->from($this)
                    ->exec()
                    ->getOneField('max', 0) + 1;
        }

        $this['_settings'] = json_encode($this['settings'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Обработчик после загрузки
     *
     * @return void
     */
    public function afterObjectLoad()
    {
       $this['settings'] = json_decode((string)$this['_settings'], true) ?: [];
    }
}