<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer;

use Ai\Model\Orm\Prompt;
use RS\Exception;
use RS\Helper\Tools;
use RS\Orm\AbstractObject;
use RS\Orm\FormObject;
use RS\Orm\PropertyIterator;
use RS\Orm\Type\AbstractType;
use RS\Orm\Type;

/**
 * Базовый класс для поля(свойства), которое будет автоматически заполняться с помощью ИИ
 * Каждый объект одного поля способен сформировать промпт и обработать результат
 * Запрос автоматически дополняется, характерными для типа поля уточнениями в запросе
 */
abstract class AbstractField
{
    const SYSTEM_PROMPT_BASE = 'base';
    const SYSTEM_PROMPT_FORMAT = 'format';

    private $max_length = 180;
    private $enable = true;
    private Prompt $prompt;
    /**
     * @var Prompt[]|null
     */
    private $prompts;

    /**
     * Конструктор
     *
     * @param AbstractTransformer $transformer Объект, который отвечает за транформацию какого-либо объекта (Товар, Статья, ...)
     * @param string $field_name Имя поля у трансформируемого объекта
     * @param string $title Название поля у трансформируемого объекта
     * @param array $options Дополнительные параметры. Будут устанавливаться с помощью методов setКЛЮЧ(ЗНАЧЕНИЕ)
     * @throws Exception
     */
    function __construct(
        protected AbstractTransformer $transformer,
        protected string              $field_name,
        protected string              $title,
        array                         $options = [])
    {
        foreach($options as $key => $value) {
            $method = 'set' . $key;
            if (method_exists($this, $method)) {
                $this->$method($value);
            } else {
                throw new Exception(t('Не найден метод `%0` в классе `%1`', [$method, get_class($this)]));
            }
        }
    }

    /**
     * Возвращает строковое название типа
     *
     * @return string
     */
    abstract public function getTypeTitle();

    /**
     * Возвращает объект свойства ORM объекта, с помощью которого можно
     * отобразить форму с генерируемым значением
     *
     * @return AbstractType
     */
    abstract public function getOrmProperty();

    /**
     * Устанавливает максимальную длину значения
     *
     * @param integer $max_length
     * @return $this
     */
    public function setMaxLength($max_length)
    {
        $this->max_length = $max_length;
        return $this;
    }

    /**
     * Устанавливает, нужно ли генерировать данное поле для всего объекта
     *
     * @param bool $bool Если true, то включено, если false - выключено
     * @return $this
     */
    public function setEnable($bool)
    {
        $this->enable = $bool;
        return $this;
    }

    /**
     * Устанавливает конкретный prompt ID, который нужно использовать в запросах к ИИ
     *
     * @param Prompt $id
     *
     * @return $this
     */
    public function setPrompt(Prompt $prompt)
    {
        $this->prompt = $prompt;
        return $this;
    }

    /**
     * Возвращает конкретный prompt ID, который нужно использовать в запросах к ИИ.
     * Если промпт явно не задан, то будет использован первый в списке для данного поля
     *
     * @return Prompt
     */
    public function getPrompt()
    {
        if (!isset($this->prompt)) {
            if ($prompts = $this->getPrompts()) {
                $this->prompt = reset($prompts);
            }
        }
        return $this->prompt;
    }

    /**
     * Возвращает, нужно ли генерировать данное поле для всего объекта
     *
     * @return bool
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Возвращает максимально возможную длину значения в поле
     *
     * @return integer
     */
    public function getMaxLength()
    {
        return $this->max_length;
    }


    /**
     * Возвращает запрос на заполнение данного поля для ИИ.
     * Данный метод может добавлять какие-либо уточнения к запросу, задаваемому пользователем
     *
     * @return string
     */
    public function getPromptText()
    {
        $prompt = Tools::unEntityString($this->getPrompt()->prompt);

        $prompt_processor = new PromptProcessor();
        return $prompt_processor->process($prompt, $this->transformer->getVariables());
    }

    /**
     * Выполняет запрос к ИИ и возвращает результат в виде стрима
     *
     * @return \Traversable
     */
    public function makeCompletionRequest($statistic_params = [])
    {
        //Получаем подготовленный запрос
        $prompt_text = $this->getPromptText();
        $temperature = (float)$this->getPrompt()->temperature;

        //Выполняем запрос на генерацию к ИИ
        $original_stream = $this
            ->getPrompt()
            ->getService()
            ->getServiceTypeObject()
            ->setStatisticParams($statistic_params + [
                'transformer_id' => $this->transformer->getId(),
                'prompt_id' => $this->getPrompt()->id,
                'field' => $this->getTitle(),
            ])
            ->createChatStream([
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->getSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt_text
                    ]
                ],
                'temperature' => $temperature
            ]);

        //Очищаем результат, при необходимости
        return $this->getFilterResultGenerator($original_stream);
    }

    /**
     * Возвращает генератор, который может предварительно
     * обрабатывать полученный исходный поток данных
     *
     * @param \Traversable $source_stream
     * @return \Generator
     */
    protected function getFilterResultGenerator(\Traversable $source_stream)
    {
        foreach($source_stream as $chunk) {
            //Здесь может быть некоторая предварительная обработка кусочка результата (потока)
            yield $chunk;
        }
    }

    /**
     * Возвращает имя поля
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * Возвращает название поля
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Возвращает массив кусков сисистемного промпта, в котором должен быть описан контекст и формат возвращаемых данных.
     * Данный массив будет объединен в единый текст.
     *
     * @return array
     */
    protected function getSystemPromptParts()
    {
        return [
            self::SYSTEM_PROMPT_BASE => 'Ты отвечаешь всегда простой неформатированной строкой, без кавычек, без Markdown и без каких-либо оберток.',
            self::SYSTEM_PROMPT_FORMAT => 'Только чистый текст.'
        ];
    }

    /**
     * Возвращает единую строку системного текста
     *
     * @return string
     */
    final public function getSystemPrompt()
    {
        return implode(' ', $this->getSystemPromptParts());
    }

    /**
     * Возвращает список, связанных с данным полем промптов
     *
     * @param bool $cache = true
     * @return Prompt[]
     */
    public function getPrompts($cache = true)
    {
        if (!isset($this->prompts)) {
            $this->prompts = $this->transformer->getPromptsByField($this->getFieldName());
        }
        return $this->prompts;
    }

    /**
     * Добавляет атрибуты к полю ORM-объекта,
     * необходимые для активации возможностей в административной панели
     *
     * @param AbstractObject $orm_object
     * @return void
     */
    public function addFieldAttributes(AbstractObject $orm_object)
    {
        $prompts = [];
        foreach($this->getPrompts() as $prompt) {
            $prompts[] = [
                'id' => $prompt['id'],
                'note' => $prompt['note'] ?: t('Базовый запрос')
            ];
        }

        $orm_object->getProp($this->getFieldName())->setAttr([
            'data-ai-button' => htmlspecialchars(json_encode([
                'transformer' => $this->transformer->getId(),
                'field_name' => $this->getFieldName(),
                'prompts' => $prompts
            ], JSON_UNESCAPED_UNICODE))
        ]);
    }

    /**
     * Возвращает объект, содержащий форму настроек для диалога массового заполнения полей объектов
     *
     * @return FormObject
     */
    public function getSettingFormObject()
    {
        $properties = new PropertyIterator([
            'prompt_id' => (new Type\Integer)
                ->setDescription(t('Запрос'))
                ->setHint(t('Вы можете управлять шаблонами запросов к ИИ в разделе Разное -> AI-ассистент -> Шаблоны запросов к ИИ'))
                ->setList(function() {
                    $result = [];
                    foreach($this->getPrompts() as $prompt) {
                        $result[$prompt['id']] = $prompt->getTitle();
                    }
                    return $result;
                }),
            'overwrite' => (new Type\Integer())
                ->setDescription(t('Режим перезаписи'))
                ->setCheckboxView(1, 0)
                ->setHint(t('В обычном режиме генерация происходит только, если данное поле не заполнено у объекта. В режиме перезаписи новое значение будет сгенерировано в любом случае.'))
        ]);
        $properties->arrayWrap('settings[fields]['.$this->getFieldName().']');
        return new FormObject($properties);
    }
}