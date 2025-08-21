<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace RS\Helper\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;
use RS\File\Tools;
use RS\View\Engine;

/**
 * Обертка над DOMPDF для ReadyScript
 */
class PDFGenerator extends Dompdf
{
    public function __construct($options = null)
    {
        if (!$options) {
            $options = new Options();
            $options->setChroot(\Setup::$PATH);

            $tmp_dir = \Setup::$TMP_DIR . '/dompdf';
            Tools::makePath($tmp_dir);
            $options->setTempDir($tmp_dir);
            $options->setLogOutputFile(false);
            $options->set('isRemoteEnabled', true);
        }

        parent::__construct($options);
    }
    /**
     * Рендерит SMarty Шаблон и возвращает содержимое PDF документа
     *
     * @param string $template - путь к шаблону который надо воспроизвести
     * @param array $vars - массив переменнных
     * @return string
     */
    function renderTemplate($template, $vars)
    {
        $view = new Engine();
        $html = $view->assign($vars)->fetch($template);
        $this->loadHtml($html);
        $this->render();
        $pdf_content = $this->output();

        return $pdf_content;
    }
}