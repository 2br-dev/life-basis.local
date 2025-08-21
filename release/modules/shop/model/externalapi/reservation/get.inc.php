<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Shop\Model\ExternalApi\Reservation;

use Catalog\Model\ApiUtils as CatalogApiUtils;
use Shop\Model\ApiUtils as ShopApiUtils;
use Catalog\Model\CurrencyApi;
use ExternalApi\Model\AbstractMethods\AbstractGet;
use RS\Helper\CustomView;
use RS\Orm\Type;
use Shop\Model\Orm\Reservation;

/**
* Возвращает объект предварительного заказа по ID
*/
class Get extends AbstractGet
{
    const RIGHT_LOAD = 1;
    
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
            self::RIGHT_LOAD => t('Загрузка объекта')
        ];
    }
    
    /**
    * Возвращает название секции ответа, в которой должен вернуться список объектов
    * 
    * @return string
    */
    public function getObjectSectionName()
    {
        return 'reservation';
    }
    
    /**
    * Возвращает объект с которым работает
    * 
    */
    public function getOrmObject()
    {
        return new Reservation();
    }

    /**
     * Подготавливает объект ($this->object) после его загрузки и перед отдачей
     *
     * @return void
     */
    protected function prepareObject()
    {
        self::appendReservationProperties($this->object);
        self::appendReservationDynamicValues($this->object);
    }

    /**
     * Добавляет необходимые динамические свойства предварительному заказу
     *
     * @param Reservation $reservation
     * @return void
     */
    public static function appendReservationProperties($reservation)
    {
        $reservation
            ->getPropertyIterator()->append([
                'offer_title' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'status_color' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'status_title' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'unit' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'single_cost' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'single_cost_formatted' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'total_cost' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'total_cost_formatted' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'image' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'multioffer' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'user_fio' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
                'can_create_order' => (new Type\MixedType())
                    ->setVisible(true, 'app'),
            ]);
    }

    /**
     * Дополняет предварительный заказ необходимыми дополнительными полями
     *
     * @param Reservation $reservation
     * @return void
     */
    public static function appendReservationDynamicValues($reservation)
    {
        $currency = CurrencyApi::getByUid($reservation['currency']);

        $product = $reservation->getProduct();
        $offer = $reservation->getOffer();

        $unit = $offer['unit'] ? $offer->getUnit() : $product->getUnit();
        $reservation['unit'] = $unit->stitle;

        $reservation['single_cost'] = $reservation->getProductSingleCost();
        $reservation['single_cost_formatted'] = CustomView::cost($reservation['single_cost'], $currency['stitle'] ?? '');

        $reservation['total_cost'] = $reservation->getTotalCost();
        $reservation['total_cost_formatted'] = CustomView::cost($reservation['total_cost'], $currency['stitle'] ?? '');

        $reservation['image'] = CatalogApiUtils::prepareImagesSection($product->getMainImage());
        $reservation['status_color'] = $reservation->getStatusColor();
        $reservation['status_title'] = $reservation->getStatusTitle();
        $reservation['offer_title'] = $offer['title'];
        $reservation['user_fio'] = $reservation->getUser()->getFio();
        $reservation['can_create_order'] = $reservation->canCreateOrder();

        $multioffers_titles = $reservation->getArrayMultiOffer();
        $multioffer = [];
        if ($multioffers_titles) {
            foreach($reservation->getArrayMultiOffer() as $title => $value) {
                $multioffer[] = [
                    'title' => $title,
                    'value' => $value
                ];
            }
        }
        $reservation['multioffer'] = $multioffer;
    }

    /**
     * Возвращает предзаказ по ID
     *
     * @param string $token Авторизационный токен
     * @param integer $reservation_id ID предварительного заказа
     *
     * @example GET api/methods/reservation.get?token=2bcbf947f5fdcd0f77dc1e73e73034f5735de486&id=1
     * Ответ
     * <pre>
     *       {
     *           "response": {
     *               "reservation": {
     *                   "id": "90",
     *                   "product_id": "78688",
     *                   "product_barcode": "a078688-4",
     *                   "product_title": "Многомерные комплектации",
     *                   "offer_id": "10708",
     *                   "currency": "RUB",
     *                   "multioffer": [
     *                       {
     *                           "title": "Форм-фактор",
     *                           "value": "Нетбук"
     *                       },
     *                       {
     *                           "title": "test",
     *                           "value": "2"
     *                       }
     *                   ],
     *                   "amount": 1,
     *                   "phone": "+79280000001",
     *                   "email": "demo@example.com",
     *                   "is_notify": "1",
     *                   "dateof": "2024-08-29 11:49:59",
     *                   "user_id": "2",
     *                   "status": "open",
     *                   "comment": null,
     *                   "partner_id": "0",
     *                   "offer_title": "Нетбук, 2",
     *                   "status_color": "#ffa545",
     *                   "unit": "шт.",
     *                   "single_cost": 0,
     *                   "single_cost_formatted": "0 ₽",
     *                   "total_cost": 0,
     *                   "total_cost_formatted": "0 ₽",
     *                   "image": {
     *                       "id": null,
     *                       "title": null,
     *                       "original_url": "https://full.readyscript.local/resource/img/photostub/nophoto.jpg",
     *                       "big_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_1000x1000/nophoto_81b6f2b0.jpg",
     *                       "middle_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_600x600/nophoto_3443afef.jpg",
     *                       "small_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_300x300/nophoto_62e5d4e1.jpg",
     *                       "micro_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_100x100/nophoto_9a394c67.jpg",
     *                       "nano_url": "https://full.readyscript.local/storage/photo/stub/resized/amazing_50x50/nophoto_e7484ef.jpg"
     *                   }
     *               }
     *           }
     *       }
     * </pre>
     * @return array
     * @throws \ExternalApi\Model\Exception
     */
    function process($token, $reservation_id)
    {
        $result = parent::process($token, $reservation_id);
        $result['response']['statuses'] = ShopApiUtils::getReservationStatuses();

        return $result;
    }
}
