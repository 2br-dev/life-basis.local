<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Crm\Model\Autotask;

use Crm\Model\Links\Type\LinkTypeFeedback;
use Feedback\Model\FormApi;

class RuleIfFeedback extends AbstractIfRule
{
    /**
     * Возвращает идентификатор класса условия
     *
     * @return string
     */
    public function getId()
    {
        return 'resultitem';
    }

    /**
     * Возвращает публичное название класса условия
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Форма обратной связи');
    }

    /**
     * Возвращает тип связи для объекта
     *
     * @return string
     */
    public function getLinkedTypeId()
    {
        return LinkTypeFeedback::getId();
    }

    /**
     * Возвращает массив действий над объектом по типу
     *
     * @return array
     */
    public function getOperationsByType($type)
    {
        if ($type == 'insert') {
            return ['create'];
        }
        return [];
    }

    /**
     * Возвращает действия, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getActions()
    {
        return parent::getActions() + [
            'create' => t('Получила ответ'),
        ];
    }

    /**
     * Возвращает дополнительные параметры, которые будут учитываться при выполнении условия
     *
     * @return array
     */
    public function getParams($action = null)
    {
        return [
            'form_id' => t('Форма')
        ];
    }

    /**
     * Воздращает доступные для условия действия
     *
     * @return array
     */
    public function getAvailableActions()
    {
        return ['create'];
    }

    /**
     * Возвращает переменные, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceVarTitles()
    {
        return [
            'title' => t('Наименование сообщения'),
            'field.<псевдоним поля формы>' => t('Значение указанного поля'),
            'form_name' => t('Название формы')
        ];
    }

    /**
     * Возвращает значение поля $alias среди данных $fields_result
     *
     * @param $fields_result
     * @param $alias
     * @return bool|string
     */
    private function findFieldValue($fields_result, $alias)
    {
        foreach($fields_result as $item) {
            if ($item['field']['alias'] == $alias) {
                $value = $item['value'];

                if ($item['field']['show_type'] == 'file') {
                    if (!isset($value['real_file_name'])) {
                        return t('Файл не загружен');
                    } else {
                        return $value['real_file_name'];
                    }
                }
                elseif ($item['field']['show_type'] == 'list') {
                    if (is_array($value)) {
                        return implode(', ', $value);
                    } else {
                        return $value;
                    }
                }
                else {
                    return $value;
                }
            }
        }

        return false;
    }

    /**
     * Возвращает значения переменных, которые будут заменены в строковых полях задачи.
     *
     * @return array
     */
    public function getReplaceValues($entity)
    {
        $form = $entity->getFormObject();

        $for_replace = [
            '{title}' => $entity['title'],
            '{form_name}' => $form['title'],
        ];

        $fields_result = $entity->tableDataUnserialized();

        foreach($form->getFields() as $field_item) {
            $alias = $field_item['alias'];
            $value = $this->findFieldValue($fields_result, $alias);
            if ($value !== false) {
                $for_replace['{field.' . $alias . '}'] = $value;
            }
        }

        return $for_replace;
    }

    /**
     * Возвращает формы
     *
     * @return array
     */
    public function getFormId()
    {
        return FormApi::staticSelectList();
    }
}