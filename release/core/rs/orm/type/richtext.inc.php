<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Orm\Type;

class Richtext extends Text
{
    protected
        $editor_options = [],
        $extra_toolbar_buttons = [],
        $escape_type = self::ESCAPE_TYPE_HTML,
        $form_template = '%system%/coreobject/type/form/richtext.tpl';

    /**
     * Переустанавливает все опции визуального редактора
     *
     * @param array $options
     * @return $this
     */
    public function setEditorOptions(array $options)
    {
        $this->editor_options = $options;
        return $this;
    }

    /**
     * Добавляет опцию визуального редактора
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function addEditorOption($key, $value)
    {
        $this->editor_options['tiny_options'][$key] = $value;
        return $this;
    }

    /**
     * Удаляет опцию визуального редактора
     *
     * @param string $key
     * @return $this
     */
    public function removeEditorOption($key)
    {
        unset($this->editor_options['tiny_options'][$key]);
        return $this;
    }

    /**
     * Возращает опции визуального редактора
     *
     * @return array
     */
    public function getEditorOptions()
    {
        return $this->editor_options;
    }

    /**
     * Добавляет к существующим кнопкам в визуальном редакторе дополнительные
     *
     * @param string $button Идентификатор кнопки
     * @param integer $toolbar_number Номер панели (1,2,...)
     * @return $this
     */
    public function addEditorButtonToToolbar($button, $toolbar_number)
    {
        $this->extra_toolbar_buttons[$toolbar_number][] = $button;

        return $this;
    }

    /**
     * Удаляет дополнительную кнопку со всех панелей, если она была добавлена ранее
     *
     * @param string $button Идентификатор кнопки
     *
     * @return $this
     */
    public function removeEditorButtonToToolbar($button)
    {
        foreach($this->extra_toolbar_buttons as $toolbar_number => $buttons) {
            $key = array_search($button, $buttons);
            if ($key !== false) {
                unset($this->extra_toolbar_buttons[$toolbar_number][$key]);
            }
        }
        return $this;
    }

    /**
     * Возвращает HTML-код формы
     *
     * @param array|null $view_options
     * @param $orm_object
     * @return string
     * @throws \SmartyException
     */
    public function formView($view_options = null, $orm_object = null)
    {
        $this->tinymce = new \RS\Html\Tinymce([
            'id' => $this->getNormilizedId(),
            'name' => $this->getFormName(),
            'extra_toolbar_buttons' => $this->extra_toolbar_buttons,
            ] + $this->getEditorOptions(), $this->get());

        $this->tinymce->setAttributes($this->getAttrArray());
        
        return parent::formView($view_options, $orm_object);
    }

    /**
     * Возвращает путь к шаблону поля
     *
     * @param $multiedit
     * @return string
     */
    public function getRenderTemplate($multiedit = false)
    {
        $this->tinymce = new \RS\Html\Tinymce([
            'id' => $this->getNormilizedId(),
            'name' => $this->getFormName(),
            'extra_toolbar_buttons' => $this->extra_toolbar_buttons,
            ] + $this->getEditorOptions(), $this->get());

        $this->tinymce->setAttributes($this->getAttrArray());
        
        return parent::getRenderTemplate($multiedit);
    }

    /**
     * Возвращает ID поля с учетом замены спец.лимволов
     * @return string
     */
    protected function getNormilizedId()
    {
        return str_replace(['[',']'], '-', $this->getFormName());
    }
}  
