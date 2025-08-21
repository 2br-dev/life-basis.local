<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace RS\Orm\Type;

use RS\Config\Loader;

/**
* Тип - капча. 
* run-time тип.
*/
class Captcha extends AbstractType
{
    public
        $php_type = "", //mixed
        $vis_form = false,
        $runtime = true;
        
    protected
        $form_template = '%system%/coreobject/type/form/captcha.tpl',
        $enabled = true,
        $context = null;
    
    function __construct(array $options = null) {
        parent::__construct($options);
        $this->setChecker([$this, 'dependCheck'], \RS\Captcha\Manager::currentCaptcha()->errorText());
    }

    /**
     * Возвращает true, если капча включена
     *
     * @return bool
     */
    public function isEnabled()
    {
        if (Loader::getSystemConfig()['captcha_class'] == 'stub') {
            return false;
        }

        return $this->enabled;
    }

    /**
     * Устанавливает, включена ли капча.
     *
     * @param bool $bool
     * @return $this
     */
    public function setEnable($bool)
    {
        $this->enabled = $bool;
        return $this;
    }

    /**
     * Устанавливает контекст проверки капчи. В одном и том же контексте может существоввать только одна капча на странице
     *
     * @param string $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Возвращает установленный пользовательский контекст капчи
     *
     * @return |null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Возвращает текущий рабочий контекст капчи
     *
     * @param null $orm_object
     * @return string|null
     */
    public function getReadyContext($orm_object = null)
    {
        $context = $this->getContext();
        if ($context === null) {
            $context = ($orm_object === null) ? '' : $orm_object->getShortAlias();
        }
        return $context;
    }

    /**
     * Проверяет капчу на корректность ввода
     *
     * @param $coreobj
     * @param $value
     * @param $errortext
     * @return bool|mixed
     */
    public function dependCheck($coreobj, $value, $errortext)
    {
        if (!$this->isEnabled()) return true; //Если капча отключена, то не проверяем ее значение

        $chk = new \RS\Orm\Type\Checker();
        $callback = [$chk, 'chkcaptcha'];
        $context = $this->getReadyContext($coreobj);        
        return call_user_func_array($callback, [$coreobj, $value, $context, \RS\Captcha\Manager::currentCaptcha()->errorText()]);
    }
    
    /**
    * Возвращает HTML код формы свойства
    * 
    * @param array|null $options - параметры отображения формы. если null, то отображать все
    *     Возможные элементы массива:
    *         'form' - отображать форму,
    *         'error' - отображать блок с ошибками,
    *         'hint' - оторажать ярлык с подсказкой
    * @param object|null $orm_object - orm объект, которому принадлежит поле
    * 
    * @return string
    */
    function formView($view_options = null, $orm_object = null)
    {
        $context = $this->getReadyContext($orm_object);
        return \RS\Captcha\Manager::currentCaptcha()->getView($this->name, $context, null, $view_options);
    }
    
    /**
    * Возвращает объект текущей капчи
    * 
    * @return \RS\Captcha\AbstractCaptcha
    */
    function getTypeObject()
    {
        return \RS\Captcha\Manager::currentCaptcha();
    }
}
