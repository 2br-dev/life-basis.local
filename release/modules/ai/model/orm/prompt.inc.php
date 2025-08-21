<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Orm;
use Ai\Model\ServiceApi;
use Ai\Model\Transformer\AbstractTransformer;
use RS\Exception;
use RS\Orm\OrmObject;
use RS\Orm\Request;
use RS\Orm\Type;

/**
 * ORM объект, который хранит промпты для каждого поля
 * --/--
 * @property integer $id Уникальный идентификатор (ID)
 * @property integer $site_id ID сайта
 * @property string $transformer_id Объект трансформирования
 * @property string $field Поле объекта
 * @property string $note Примечание
 * @property integer $service_id GPT-Сервис
 * @property double $temperature Креативность (temperature)
 * @property string $prompt Текст запроса к ИИ
 * @property integer $sortn Порядковый номер
 * --\--
 */
class Prompt extends OrmObject
{
    protected static $table = 'ai_prompt';

    function _init()
    {
        parent::_init()->append([
            'site_id' => (new Type\CurrentSite()),
            'transformer_id' => (new Type\Varchar())
                ->setDescription(t('Объект трансформирования'))
                ->setList(['Ai\Model\TransformerApi', 'staticSelectList'], ['' => t('Не выбрано')])
                ->setChecker('ChkEmpty', t('Выберите объект трансформирования'))
                ->setMaxLength(50),
            'field' => (new Type\Varchar())
                ->setDescription(t('Поле объекта'))
                ->setTemplate('%ai%/admin/prompt/field_wrapper.tpl')
                ->setHint(t('Каждое поле у объекта должно иметь свой промпт для его заполнения'))
                ->setMaxLength(50),
            'note' => (new Type\Varchar())
                ->setDescription(t('Примечание'))
                ->setMaxLength(120)
                ->setIndex(true)
                ->setHint(t('Используйте его, чтобы различать промпты, которые созданы для одного поля. Если у вас один промпт на одно поле, можете не заполнять примечание.')),
            'service_id' => (new Type\Integer())
                ->setDescription(t('GPT-Сервис'))
                ->setList(['Ai\Model\ServiceApi', 'staticSelectList'], [
                    0 => t('- По умолчанию -'),
                    Service::SERVICE_READYSCRIPT_ID => t('ReadyScript')]),
            'temperature' => (new Type\Real())
                ->setDescription(t('Креативность (temperature)'))
                ->setHint(t('Данный параметр отвечает за то, насколько ИИ может отклоняться от прямого ответа в сторону креативных размышлений. 0% - только прямые строгие ответы, 100% и выше - наиболее сильно может отклоняться от прямого ответа.'))
                ->setDefault(0.8)
                ->setListFromArray([
                    '0' => '0%',
                    '0.1' => '10%',
                    '0.2' => '20%',
                    '0.3' => '30%',
                    '0.4' => '40%',
                    '0.5' => '50%',
                    '0.6' => '60%',
                    '0.7' => '70%',
                    '0.8' => '80%',
                    '0.9' => '90%',
                    '1'   => '100%',
                    '1.1' => '110%',
                    '1.2' => '120%',
                    '1.3' => '130%',
                    '1.4' => '140%',
                    '1.5' => '150%',
                    '1.6' => '160%',
                    '1.7' => '170%',
                    '1.8' => '180%',
                    '1.9' => '190%',
                    '2' => '200%',
                ]),
            'prompt' => (new Type\Text())
                ->setDescription(t('Текст запроса к ИИ'))
                ->setAttr([
                    'rows' => 15,
                    'cols' => 150
                ])
                ->setHint(t('Опишите как можно подробнее запрос, который будет отправлен ИИ для генерации текста для данного поля. <br><br>Если вы желаете, чтобы часть текста включалась в запрос только, если переменные используемые в этой части не пустые, то вы можете обернуть такую часть текста квадратными скобками.<br><br> Пример: Придумай описание услуги {$product.title} [опираясь на текст: {$product.short_description}]')),
            'sortn' => (new Type\Integer())
                ->setDescription(t('Порядковый номер'))
                ->setVisible(false),
        ]);

        $this->addIndex(['site_id', 'transformer_id', 'field', 'note'], self::INDEX_UNIQUE);
    }

    /**
     * Возвращает объект трансформера
     *
     * @return AbstractTransformer
     */
    public function getTransformer()
    {
        try {
            $transformer = AbstractTransformer::getTransformerById($this['transformer_id']);
            $transformer->setSiteId($this['__site_id']->get());
            return $transformer;
        } catch (Exception $e) {
            return null;
        }
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
                    ->exec()->getOneField('max', 0) + 1;
        }
    }

    /**
     * Возвращает объект сервиса
     *
     * @return Service
     */
    public function getService()
    {
        return ServiceApi::getServiceById($this['service_id']);
    }

    /**
     * Возвращает подпись к текущему запросу
     *
     * @return string
     */
    public function getTitle()
    {
        return $this['note'] ?: t('Базовый');
    }
}