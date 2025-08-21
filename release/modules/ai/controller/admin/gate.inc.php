<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Ai\Controller\Admin;

use Ai\Model\Orm\Prompt;
use Ai\Model\StreamOutput;
use RS\Controller\Admin\Front;

/**
 * Шлюз для взаимодействия с ИИ в административной панели ReadyScript
 */
class Gate extends Front
{
    public function init()
    {
        $this->wrapOutput(false);
        session_write_close();
    }

    /**
     * Возвращает сгенерированный ответ для одного промпта
     *
     * @return \RS\Controller\Result\Standard|void
     */
    public function actionGenerate()
    {
        $prompt_id = $this->url->get('prompt_id', TYPE_INTEGER);

        $prompt = new Prompt($prompt_id);
        if (!$prompt['id']) {
            return $this->result->addEMessage(t('Запрос с ID %0 не найден', [$prompt_id]));
        }

        $transformer = $prompt->getTransformer();
        $transformer->fillSourceObjectPromPost($this->url->getSource(POST));

        try {
            $stream = $transformer->requestGenerationByPrompt($prompt);

            $stream_output = new StreamOutput($stream);
            $stream_output->output();

        } catch (\Exception $e) {
            return $this->result
                ->setSuccess(false)
                ->addEMessage($e->getMessage());
        }
    }
}