<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Receipt;
use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use ExternalApi\Model\AbstractMethods\AbstractGet;
use \ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils as ApiUtils;
use RS\Config\Loader;
use RS\Helper\CustomView;
use RS\Http\Request;
use Shop\Model\Orm\Payment;
use Shop\Model\Orm\Receipt;
use Shop\Model\Orm\Transaction;
use Shop\Model\ReceiptApi;
use RS\Orm\Type;
use Shop\Model\TransactionApi;
use Users\Model\Orm\User;

/**
 * Возвращает информацию об успешно выбитом чеке
 */
class GetSuccessInfo extends AbstractGet
{
    const RIGHT_LOAD = 1;
    const RIGHT_SELF_LOAD = 2;

    /**
     * Возвращает комментарии к кодам прав доступа
     *
     * @return [
     *     КОД => КОММЕНТАРИЙ,
     *     КОД => КОММЕНТАРИЙ,
     *     ...
     * ]
     */
    public function getRightTitles()
    {
        return [
            self::RIGHT_LOAD => t('Загрузка информации по любым чекам'),
            self::RIGHT_SELF_LOAD => t('Загрузка информации по своим чекам')
        ];
    }


    /**
     * Возвращает ORM объект, который следует загружать
     */
    public function getOrmObject()
    {
        return new Receipt();
    }

    /**
     * Добавляет к ответу стандартизированную информацию по кассовому чеку
     *
     * @param $response
     * @param $receipt
     * @return array
     */
    protected function addReceiptInfoData($response, $receipt)
    {
        $extra_info = $receipt->getExtraInfo('success_info');
        if (!empty($extra_info)) {
            $info = [];
            $receipt_info = $receipt->getReceiptInfo();
            if ($value=$receipt_info->getFiscalReceiptNumber()) {
                $info['fiscal_receipt_number'] = [
                    'type' => 'info',
                    'title' => t('Номер чека в смене'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getShiftNumber()) {
                $info['shift_number'] = [
                    'type' => 'info',
                    'title' => t('Номер смены'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getReceiptDatetime()) {
                $info['receipt_datetime'] = [
                    'type' => 'info',
                    'title' => t('Дата и время регистрации чека'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getTotal()) {
                $info['total'] = [
                    'type' => 'info',
                    'title' => t('Сумма'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getFnNumber()) {
                $info['fn_number'] = [
                    'type' => 'info',
                    'title' => t('Номер фискального накопителя'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getEcrRegistrationNumber()) {
                $info['ecr_registration_number'] = [
                    'type' => 'info',
                    'title' => t('Рег.номер ККТ'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getFiscalDocumentNumber()) {
                $info['fiscal_document_number'] = [
                    'type' => 'info',
                    'title' => t('Фискальный номер документа'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getFiscalDocumentAttribute()) {
                $info['fiscal_document_attribute'] = [
                    'type' => 'info',
                    'title' => t('Фискальный признак документа'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getReceiptOfdUrl()) {
                $info['ofd_receipt_url'] = [
                    'type' => 'link',
                    'title' => t('Ссылка на чек в ОФД'),
                    'value' => $value
                ];
            }
            if ($value=$receipt_info->getQrCodeImageUrl(400,400,true)) {
                $info['qr_code_data'] = [
                    'type' => 'image',
                    'title' => t('QR код для проверки'),
                    'value' => $value
                ];
            }
            $response['response'][$this->getObjectSectionName()]['receipt_info'] = $info;
        }
        return $response;
    }

    /**
     * Возвращает объект
     */
    public function getResult($dao)
    {

        $dao->getPropertyIterator()->append([
            'total_formatted' => new Type\Varchar([
                'appVisible' => true
            ]),
            'receipt_info' => new Type\ArrayList([
                'appVisible' => true
            ]),
        ]);

        return ApiUtils::extractOrm($dao);
    }

    /**
     * Возвращает информацию об успешно выбитом чеке
     *
     * @param string $token Авторизационный token
     * @param integer $receipt_id ID чека
     *
     * @return array Возвращает объект чека
     * @example GET /api/methods/receipt.getSuccessInfo?token=894b9df5ebf40531d560235d7379a8cff50f930f&receipt_id=27
     * Ответ:
     * <pre>
     *
     * {
     *      "response": {
     *          "receipt": {
     *              "id": "27",
     *              "sign": "dff72c8c5abfcd54986526c7415e3bb219f5591c",
     *              "uniq_id": "00a21133-39aa-4b01-ac1e-da9fd7d953f9",
     *              "type": "sell_refund",
     *              "provider": "atolonline",
     *              "url": null,
     *              "dateof": "2024-05-14 16:43:54",
     *              "transaction_id": "25",
     *              "total": "432.00",
     *              "status": "success",
     *              "error": "",
     *              "total_formatted": null,
     *              "receipt_info": {
     *                  "fiscal_receipt_number": {
     *                      "type": "info",
     *                      "title": "Номер чека в смене",
     *                      "value": 855
     *                      },
     *                  "shift_number": {
     *                      "type": "info",
     *                      "title": "Номер смены",
     *                      "value": 37
     *                      },
     *                  "receipt_datetime": {
     *                      "type": "info",
     *                      "title": "Дата и время регистрации чека",
     *                      "value": "14.05.2024 16:43:54"
     *                      },
     *                  "total": {
     *                      "type": "info",
     *                      "title": "Сумма",
     *                      "value": "432.00"
     *                      },
     *                  "fn_number": {
     *                      "type": "info",
     *                      "title": "Номер фискального накопителя",
     *                      "value": "9999078900006369"
     *                      },
     *                  "ecr_registration_number": {
     *                      "type": "info",
     *                      "title": "Регистрационный номер ККТ",
     *                      "value": "0000000005038476"
     *                      },
     *                  "fiscal_document_number": {
     *                      "type": "info",
     *                      "title": "Фискальный номер документа",
     *                      "value": 43009
     *                      },
     *                  "fiscal_document_attribute": {
     *                      "type": "info",
     *                      "title": "Фискальный признак документа",
     *                      "value": 3316237237
     *                      },
     *                  "ofd_receipt_url": {
     *                      "type": "link",
     *                      "title": "Ссылка на чек в ОФД",
     *                      "value": "https://link..."
     *                      },
     *                  "qr_code_data": {
     *                      "type": "image",
     *                      "title": "QR код для проверки",
     *                      "value": "https://link..."
     *                  }
     *              }
     *          }
     *      }
     * }
     * </pre>
     *
     */
    public function process($token, $receipt_id)
    {
        $this->object = $this->getOrmObject();
        if ($this->object->load($receipt_id)) {

            if ($this->object->getTransaction()->user_id != $this->token['user_id']
                && ($error = $this->checkAccessError(self::RIGHT_LOAD))) {
                throw new ApiException($error, ApiException::ERROR_METHOD_ACCESS_DENIED);
            }

            $response = [
                'response' => [
                    $this->getObjectSectionName() => $this->getResult($this->object)
                ]
            ];
            return $this->addReceiptInfoData($response, $this->object);
        }

        throw new ApiException(t('Объект с таким ID не найден'), ApiException::ERROR_OBJECT_NOT_FOUND);
    }

}