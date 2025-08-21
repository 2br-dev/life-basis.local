<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Language\Plugin;
/**
* Обеспечивает корректное отображение слова во множественном числе
*/
class Plural implements PluginInterface
{
    /**
     * Возвращает правильную форму слова во множественном числе для значения $param_value
     *
     * @param integer $param_value Число
     * @param string $value Слова, разделенные "|", которые можно поставить после чисел 1,2,5. Например "товар|товара|товаров"
     * @param array $params Дополнительные параметры. Зарезервировано
     * @param string $lang Язык. Для английского языка используется только две формы для единственного числа и для множественного
     * @return string
     */
    public function process($param_value, $value, $params, $lang)
    {
        $values = explode('|', $value);
        if ($lang == 'ru') {
            list($first, $second, $five) = $values;
            
            $prepare = abs( intval( $param_value ) );
            if( $prepare !== 0 ) 
            {
                $result = $prepare % 10 == 1 && $prepare % 100 != 11 ? $first :
                    ($prepare % 10 >= 2
                    && $prepare % 10 <= 4
                    && ($prepare % 100 < 10 || $prepare % 100 >= 20) ? $second : $five);
            }
            else {
                $result = $five;
            }
        } elseif ($lang == 'en') {
            $result = $param_value == 1 ? $values[0] : $values[1];
        } else {
            $result = $values[0];
        }
        return $result;
    }
}

