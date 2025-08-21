<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\receipt;
use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use \ExternalApi\Model\Exception as ApiException;
use ExternalApi\Model\Utils as ApiUtils;
use RS\Helper\CustomView;
use Shop\Model\CashRegisterApi;
use Shop\Model\Orm\Receipt;
use RS\Orm\Type;

/**
 * Выбивает чек в ККТ
 */
class GetReport extends AbstractAuthorizedMethod
{
    const RIGHT_RUN = 1;


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
            self::RIGHT_RUN => t('Запуск действия')
        ];
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

        $dao['total_formatted'] = CustomView::cost($dao['total'], CurrencyApi::getCurrentCurrency()['stitle']);

        return ApiUtils::extractOrm($dao);
    }

    /**
     * Выбивает чек в ККТ
     *
     * @param string $token Авторизационный token
     * @param integer $receipt_id ID чека
     *
     * @return array Возвращает объект чека или ошибку
     * @example GET /api/methods/receipt.getReport?token=894b9df5ebf40531d560235d7379a8cff50f930f&receipt_id=27
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
     *              "receipt_info": null,
     *          }
     *      }
     * }
     * </pre>
     *
     */
    public function process($token, $receipt_id)
    {
        $receipt = new Receipt($receipt_id);

        if (!$receipt['id']) {
            throw new ApiException(t('Чек с указанным receipt_id не найден'), ApiException::ERROR_WRONG_PARAM_VALUE);
        }

        try{
            $cashregister_api = new CashRegisterApi();
            $provider = $cashregister_api->getTypeByShortName($receipt['provider']);
            if (($result = $provider->getReceiptStatus($receipt)) === true){
                $response = [
                    'response' => [
                        'receipt' => $this->getResult($receipt)
                    ]
                ];
                $response['response']['status'] = Receipt::handbookStatuses();
                $response['response']['type'] = Receipt::handbookType();
                return $response;
            }else{
                if ($result === 0){
                    if ($provider->hasError()){
                        throw new ApiException(t($provider->getErrorsStr()), ApiException::ERROR_WRITE_ERROR);
                    }

                    $response = [
                        'response' => [
                            'receipt' => $this->getResult($receipt)
                        ]
                    ];
                    $response['response']['status'] = Receipt::handbookStatuses();
                    $response['response']['type'] = Receipt::handbookType();

                    return $response;
                }
                throw new ApiException(t($result), ApiException::ERROR_WRITE_ERROR);
            }
        }
        catch(\Exception $e){
            throw new ApiException(t($e->getMessage()), ApiException::ERROR_WRITE_ERROR);
        }
    }

}