<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Kaptcha\Model\CaptchaType;

use RS\Captcha\AbstractCaptcha;

/**
 * Простейшая капча в виде дополнительного (визуально скрытого) поля,
 * которое обычно заполняют боты в автоматическом режиме.
 */
class BotCatcher extends AbstractCaptcha
{
    protected $template = '%kaptcha%/form/bot_catcher.tpl';

    /**
     * Возвращает идентификатор класса капчи
     *
     * @return string
     */
    public function getShortName()
    {
        return 'bot-catcher';
    }

    /**
     * Возвращает внутреннее название класса капчи
     *
     * @return string
     */
    public function getTitle()
    {
        return t('Ловушка ботов (скрытое невидимое поле)');
    }

    /**
     * Возвращает название поля для клиентских форм
     *
     * @return string
     */
    public function getFieldTitle()
    {
        return '';
    }

    /**
     * Возвращает HTML капчи
     *
     * @param string $name - атрибут name для шаблона отображения
     * @param string $context - контекст капчи
     * @param array $attributes - дополнительные атрибуты для Dom элемента капчи
     * @param array|null $view_options - параметры отображения формы. если null, то отображать все
     *     Возможные элементы массива:
     *         'form' - форма,
     *         'error' - блок с ошибками,
     *         'hint' - ярлык с подсказкой,
     * @param string $template - используемый шаблон
     *
     * @return string
     */
    public function getView($name, $context = null, $attributes = [], $view_options = null, $template = null)
    {
        $view = new \RS\View\Engine();
        $view->assign([
            'name' => $name,
            'context' => $context,
            'attributes' => $this->getReadyAttributes($attributes),
            'view_options' => $view_options,
        ]);
        return $view->fetch($this->template);
    }

    /**
     * Проверяет правильность заполнения капчи
     *
     * @param mixed $data - данные для проверки
     * @param string $context - контекст капчи
     * @return bool
     */
    public function check($data, $context = null)
    {
        if (\Setup::$DISABLE_CAPTCHA) {
            return true;
        }

        //Капча верная, когда поле не заполнено
        return $data === '';
    }

    /**
     * Возвращает текст ошибки, в случае если метод check возвращает false
     *
     * @return string
     */
    function errorText()
    {
        return t('Замечена подозрительная активность');
    }
}