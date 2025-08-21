<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace MobileManagerApp\Model\ExternalApi\ManagerApp;

use Catalog\Config\File as CatalogFile;
use ExternalApi\Model\AbstractMethods\AbstractAuthorizedMethod;
use Main\Config\File;
use RS\AccessControl\Rights;
use RS\Application\Auth;
use RS\Module\Manager;
use Shop\Config\File as ShopFile;
use Support\Config\File as SupportFile;
use Users\Config\File as UsersFile;

/**
 * Метод API возвращает настройки магазина для приложения ReadyScript
 */
class GetAppSettings extends AbstractAuthorizedMethod
{
    const RIGHT_LOAD = 1;

    const SECTION_STATISTIC = 'statistic';
    const SECTION_ORDER = 'order';
    const SECTION_ONECLICK = 'oneclick';
    const SECTION_RESERVATION = 'reservation';
    const SECTION_TRANSACTION = 'transaction';
    const SECTION_SUPPORT = 'support';


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
            self::RIGHT_LOAD => t('Доступ к настройкам приложения')
        ];
    }

    /**
     * Возвращает значения прав доступа
     *
     * @param $config
     * @return array
     */
    protected function getModuleRights($config)
    {
        $rights = [];
        $module_rights = $config->getModuleRightObject();

        foreach($module_rights->getRights() as $right) {
            $alias = $right->getAlias();
            $rights[$alias] = [
                'title' => $right->getTitleWithPath(),
                'value' => Rights::hasRight($config, $alias)
            ];
        }

        return $rights;
    }

    /**
     * Возвращает настройки пользователя для приложения ReadyScript
     *
     * @param string $token Авторизационный токен
     * @return array
     * @example GET /api/methods/managerApp.getAppSettings?token=5012d66bbf868da9c0c54889cd246db0e14e1232
     *
     * Ответ:
     * <pre>
     * {
            "response": {
                "allowed_sections": [
                    "statistic",
                    "order",
                    "transaction",
                    "reservation",
                    "oneclick",
                    "support"
                ],
                "rights": {
                    "shop": {
                        "read": {
                            "title": "Чтение",
                            "value": true
                        },
                        "create": {
                            "title": "Создание",
                            "value": true
                        },
                        "update": {
                            "title": "Изменение",
                            "value": true
                        },
                        "delete": {
                            "title": "Удаление",
                            "value": true
                        },
                        "add_funds": {
                            "title": "Начисление средств",
                            "value": true
                        },
                        "transaction_show_in_app": {
                            "title": "Операции с транзакциями - Доступ из приложения",
                            "value": true
                        },
                        "transaction_actions": {
                            "title": "Операции с транзакциями - Выполнение действий",
                            "value": true
                        },
                        "reservation_show_in_app": {
                            "title": "Операции с предзаказами - Доступ из приложения",
                            "value": true
                        },
                        "reservation_actions": {
                            "title": "Операции с предзаказами - Выполнение действий",
                            "value": true
                        },
                        "send_receipt": {
                            "title": "Операции с чеками - Отправка чека",
                            "value": true
                        },
                        "correction_receipt": {
                            "title": "Операции с чеками - Отправка чека коррекции",
                            "value": true
                        },
                        "refund_receipt": {
                            "title": "Операции с чеками - Отправка чека возврата",
                            "value": true
                        },
                        "make_shipment": {
                            "title": "Отгрузка - Разрешить производить отгрузку",
                            "value": true
                        },
                        "statistic_show_in_app": {
                            "title": "Операции с заказами - Доступ из приложения к статистике по продажам",
                            "value": true
                        },
                        "order_show_in_app": {
                            "title": "Операции с заказами - Доступ из приложения к заказам",
                            "value": true
                        },
                        "documents_printing": {
                            "title": "Операции с заказами - Печать документов",
                            "value": true
                        },
                        "status_reading": {
                            "title": "Операции с заказами - Статус заказа - Чтение",
                            "value": true
                        },
                        "status_changing": {
                            "title": "Операции с заказами - Статус заказа - Изменение",
                            "value": true
                        },
                        "customer_reading": {
                            "title": "Операции с заказами - Покупатель - Чтение",
                            "value": true
                        },
                        "customer_changing": {
                            "title": "Операции с заказами - Покупатель - Изменение ",
                            "value": true
                        },
                        "information_reading": {
                            "title": "Операции с заказами - Информация о заказе - Чтение",
                            "value": true
                        },
                        "information_changing": {
                            "title": "Операции с заказами - Информация о заказе - Изменение",
                            "value": true
                        },
                        "address_reading": {
                            "title": "Операции с заказами - Адрес - Чтение",
                            "value": true
                        },
                        "address_changing": {
                            "title": "Операции с заказами - Адрес - Изменение",
                            "value": true
                        },
                        "delivery_reading": {
                            "title": "Операции с заказами - Доставка - Чтение",
                            "value": true
                        },
                        "delivery_changing": {
                            "title": "Операции с заказами - Доставка - Изменение",
                            "value": true
                        },
                        "pay_reading": {
                            "title": "Операции с заказами - Оплата - Чтение",
                            "value": true
                        },
                        "pay_changing": {
                            "title": "Операции с заказами - Оплата - Изменение",
                            "value": true
                        },
                        "crm_reading": {
                            "title": "Операции с заказами - CRM - Чтение",
                            "value": true
                        },
                        "products_changing": {
                            "title": "Операции с заказами - Состав заказа - Изменение",
                            "value": true
                        },
                        "products_add": {
                            "title": "Операции с заказами - Состав заказа - Добавление",
                            "value": true
                        },
                        "products_delete": {
                            "title": "Операции с заказами - Состав заказа - Удаление",
                            "value": true
                        }
                    },
                    "catalog": {
                        "read": {
                            "title": "Чтение",
                            "value": true
                        },
                        "create": {
                            "title": "Создание",
                            "value": true
                        },
                        "update": {
                            "title": "Изменение",
                            "value": true
                        },
                        "delete": {
                            "title": "Удаление",
                            "value": true
                        },
                        "oneclick_show_in_app": {
                            "title": "Покупки в 1 клик - Доступ из приложения",
                            "value": true
                        }
                    },
                    "support": {
                        "read": {
                            "title": "Чтение",
                            "value": true
                        },
                        "create": {
                            "title": "Создание",
                            "value": true
                        },
                        "update": {
                            "title": "Изменение",
                            "value": true
                        },
                        "delete": {
                            "title": "Удаление",
                            "value": true
                        },
                        "support_show_in_app": {
                            "title": "Доступ из приложения",
                            "value": true
                        }
                    }
                }
            }
        }
     * </pre>
     *
     * response.allowed_sections - список разрешенных разделов в приложении
     * response.rights - список прав доступа в разрезе модулей
     */
    function process($token)
    {
        $allowed_sections = [];
        $rights = [];

        $user = Auth::getCurrentUser();
        $user->setIgnoreClientSideGroups();

        if (Manager::staticModuleEnabled('shop')) {
            $shop_config = ShopFile::config();
            $rights['shop'] = $this->getModuleRights($shop_config);

            if (!empty($rights['shop']['statistic_show_in_app']['value'])) {
                $allowed_sections[] = self::SECTION_STATISTIC;
            }

            if (!empty($rights['shop']['order_show_in_app']['value'])) {
                $allowed_sections[] = self::SECTION_ORDER;
            }

            if (!empty($rights['shop']['transaction_show_in_app']['value'])) {
                $allowed_sections[] = self::SECTION_TRANSACTION;
            }

            if (!empty($rights['shop']['reservation_show_in_app']['value'])) {
                $allowed_sections[] = self::SECTION_RESERVATION;
            }
        }

        if (Manager::staticModuleEnabled('catalog')) {
            $catalog_config = CatalogFile::config();
            $rights['catalog'] = $this->getModuleRights($catalog_config);

            if (!empty($rights['catalog']['oneclick_show_in_app']['value'])) {
                $allowed_sections[] = self::SECTION_ONECLICK;
            }
        }

        if (Manager::staticModuleEnabled('support')) {
            $support_config = SupportFile::config();
            $rights['support'] = $this->getModuleRights($support_config);

            if (!empty($rights['support']['support_show_in_app']['value'])) {
                $allowed_sections[] = self::SECTION_SUPPORT;
            }
        }

        if (Manager::staticModuleEnabled('users')) {
            $users_config = UsersFile::config();
            $rights['users'] = $this->getModuleRights($users_config);
        }

        $configs = [];
        if (Manager::staticModuleEnabled('main')) {
            $configs['yandex_api_geocoder'] = File::config()['yandex_js_api_geocoder'];
        }

        return [
            'response' => [
                'allowed_sections' => $allowed_sections,
                'rights' => $rights,
                'configs' => $configs,
            ]
        ];
    }
}