<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer\Field;

use Ai\Model\ServiceType\ServiceChatResponse;
use Ai\Model\Transformer\AbstractField;
use RS\Orm\AbstractObject;
use RS\Orm\Type;

/**
 * Тип автозаполняемого поля - HTML
 */
class HtmlField extends AbstractField
{
    protected $max_length = 65535;
    private $toolbar_number = 2;

    /**
     * Возвращает строковое название типа
     *
     * @return string
     */
    public function getTypeTitle()
    {
        return t('Текст в формате HTML');
    }

    /**
     * Устанавливает порядковый номер панели, в которую добавлять кнопку "Генерировать"
     *
     * @param int $toolbar_number
     * @return $this
     */
    public function setToolbarNumber($toolbar_number)
    {
        $this->toolbar_number = $toolbar_number;
        return $this;
    }

    /**
     * Возвращает порядковый номер панели, в которую добавлять кнопку "Генерировать"
     *
     * @return int
     */
    public function getToolbarNumber()
    {
        return $this->toolbar_number;
    }

    /**
     * Возвращает объект свойства ORM объекта, с помощью которого можно
     * отобразить форму с генерируемым значением
     *
     * @return Type\RichText
     */
    public function getOrmProperty()
    {
        return new Type\Richtext();
    }

    /**
     * Возвращает массив кусков сисистемного промпта, в котором должен быть описан контекст и формат возвращаемых данных.
     * Данный массив будет объединен в единый текст.
     *
     * @return array
     */
    protected function getSystemPromptParts()
    {
        $parts = parent::getSystemPromptParts();
        $parts[self::SYSTEM_PROMPT_FORMAT] = 'Верни ответ в формате HTML (для вставки в тег <BODY>).';

        return $parts;
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

        $field = $orm_object->getProp($this->getFieldName())
            ->setAttr([
                'data-ai-richtext' => htmlspecialchars(json_encode([
                    'transformer' => $this->transformer->getId(),
                    'field_name' => $this->getFieldName(),
                    'prompts' => $prompts
                ], JSON_UNESCAPED_UNICODE))
            ]);

        if ($field instanceof Type\Richtext) {
            $field->addEditorButtonToToolbar('rsaigenerate', $this->getToolbarNumber());
        }
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
        /**
         * @var $source_stream ServiceChatResponse[]
         */
        foreach($source_stream as $chunk) {
            $chunk->setDeltaText(
                $this->filterCodeQuotes($chunk->getDeltaText())
            );
            $chunk->setFullText(
                $this->filterCodeQuotes($chunk->getFullText())
            );

            yield $chunk;
        }
    }

    /**
     * Фильтрует обертку для отображения кода (```html   ```),
     * чтобы возвращалось то, что внутри
     *
     * @param string $text
     * @return string
     */
    protected function filterCodeQuotes($text)
    {
        $text = preg_replace('/```(\w+)?/u', '', $text);
        $text = preg_replace('/^(html)(\n)?/u', '', $text);
        return $text;
    }
}