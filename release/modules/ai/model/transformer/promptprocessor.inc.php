<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Model\Transformer;

/**
 * Класс, позволяет обрабатывать запрос к ИИ с учетом допустимого синтаксиса ReadyScript
 * с заменой переменных и конструкций в квадратных скобках.
 */
class PromptProcessor
{
    protected $open_char = '[';
    protected $close_char = ']';

    /**
     * Подготавливает текст Запроса к ИИ, с учетом допустимого синтаксиса ReadyScript
     *
     * @param string $text Исходный текст запроса
     * @param ReplaceVariable[] $variables Переменные для замены
     * @return string
     */
    public function process($text, $variables)
    {
        if (str_contains($text, $this->open_char)) {
            $text = $this->hideEmptyParts($text, $variables);
        }

        return $this->replaceVars($text, $variables);
    }

    /**
     * Заменяет переменные в строке
     *
     * @param string $text Исходный текст запроса
     * @param ReplaceVariable[] $variables Переменные для замены
     * @param bool $some_is_empty Флаг того, что какая-то из переменных была пуста или отсутствовала
     * @return string
     */
    protected function replaceVars($text, $variables, &$some_is_empty = false)
    {
        $some_is_empty = false;
        return preg_replace_callback('/\{\$(.*?)}/', function($vars) use ($variables, &$some_is_empty) {
            if (!isset($variables[$vars[1]]) || trim($variables[$vars[1]]->value) == '') {
                $some_is_empty = true;
                return '';
            } else {
                return $variables[$vars[1]]->value;
            }
        }, $text);
    }

    /**
     * Скрывает из исходного текста блоки [ .... ], в которых хотя бы одна переменная была пуста
     *
     * @param string $text Исходный текст запроса
     * @param ReplaceVariable[] $variables Переменные для замены
     * @return string
     */
    protected function hideEmptyParts($text, $variables)
    {
        $open = preg_quote($this->open_char);
        $close = preg_quote($this->close_char);

        do {
            //Находим все куски текста в скобках.
            $text = preg_replace_callback("/$open([^$open]*?)$close/", function ($matches) use ($variables) {
                //Проверяем, что внутри есть пеменные
                $some_is_empty = false;
                $result = $this->replaceVars($matches[1], $variables, $some_is_empty);

                if ($some_is_empty) {
                    return '';
                }

                return $result;
            }, $text, -1, $count);
        } while($count > 0);

        return $text;
    }
}