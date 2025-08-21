<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Shop\Config;

use RS\AccessControl\DefaultModuleRights;
use RS\AccessControl\Right;
use RS\AccessControl\RightGroup;

class ModuleRights extends DefaultModuleRights
{
    const RIGHT_ADD_FUNDS = 'add_funds',

        RIGHT_STATISTIC_SHOW_IN_APP = 'statistic_show_in_app',
        RIGHT_ORDER_SHOW_IN_APP = 'order_show_in_app',

        RIGHT_TRANSACTION_SHOW_IN_APP = 'transaction_show_in_app',
        RIGHT_TRANSACTION_ACTIONS = 'transaction_actions',

        RIGHT_RESERVATION_SHOW_IN_APP = 'reservation_show_in_app',
        RIGHT_RESERVATION_CHANGING = 'reservation_changing',
        RIGHT_RESERVATION_ACTIONS = 'reservation_actions',
        RIGHT_RESERVATION_DELETE = 'reservation_delete',

        RIGHT_SEND_RECEIPT = 'send_receipt',
        RIGHT_CORRECTION_RECEIPT = 'correction_receipt',
        RIGHT_REFUND_RECEIPT = 'refund_receipt',

        RIGHT_MAKE_SHIPMENT = 'make_shipment',

        RIGHT_DOCUMENTS_PRINTING = 'documents_printing',

        RIGHT_EXPORT_REPORT = 'export_report',

        RIGHT_STATUS_READING = 'status_reading',
        RIGHT_STATUS_CHANGING = 'status_changing',

        RIGHT_CUSTOMER_READING = 'customer_reading',
        RIGHT_CUSTOMER_CHANGING = 'customer_changing',

        RIGHT_CUSTOMER_FULLNAME_READING = 'customer_fullname_reading',
        RIGHT_CUSTOMER_EMAIL_READING = 'customer_email_reading',
        RIGHT_CUSTOMER_PHONE_READING = 'customer_phone_reading',
        RIGHT_CUSTOMER_USERFIELD_READING = 'customer_userfield',

        RIGHT_INFORMATION_READING = 'information_reading',
        RIGHT_INFORMATION_CHANGING = 'information_changing',

        RIGHT_ADMIN_COMMENT_READING = 'admin_comment_reading',
        RIGHT_USER_COMMENT_READING = 'user_comment_reading',
        RIGHT_LAST_UPDATE_READING = 'last_update_reading',
        RIGHT_SHIPMENT_DATE_READING = 'shipment_date_reading',
        RIGHT_CREATE_PLATFORM_READING = 'create_platform_reading',
        RIGHT_MANAGER_READING = 'manager_reading',

        RIGHT_ADDRESS_READING = 'address_reading',
        RIGHT_ADDRESS_CHANGING = 'address_changing',

        RIGHT_DELIVERY_READING = 'delivery_reading',
        RIGHT_DELIVERY_CHANGING = 'delivery_changing',

        RIGHT_COURIER_READING = 'courier_reading',
        RIGHT_TRACK_NUMBER_READING = 'track_number_reading',
        RIGHT_CONTACT_PERSON_READING = 'contact_person_reading',
        RIGHT_WAREHOUSE_READING = 'warehouse_reading',

        RIGHT_PAY_READING = 'pay_reading',
        RIGHT_PAY_CHANGING = 'pay_changing',

        RIGHT_IS_PAY_READING = 'is_pay_reading',
        RIGHT_PAY_DOCS_READING = 'pay_docs_reading',

        RIGHT_PROFIT_READING = 'profit_reading',
        RIGHT_EXTRA_INFO_READING = 'extra_info_reading',
        RIGHT_USERFIELDS_READING = 'userfields_reading',

        RIGHT_CRM_READING = 'crm_reading',

        RIGHT_PRODUCTS_CHANGING = 'products_changing',
        RIGHT_PRODUCTS_ADD = 'products_add',
        RIGHT_PRODUCTS_DELETE = 'products_delete',

        RIGHT_DISCOUNT_ADD = 'discount_add',
        RIGHT_DISCOUNT_DELETE = 'discount_delete',

        RIGHT_USER_TEXT_READING = 'user_text_reading',
        RIGHT_USER_TEXT_CHANGING = 'user_text_changing',

        RIGHT_CARGO_READING = 'cargo_reading',
        RIGHT_CARGO_CHANGING = 'cargo_changing',

        RIGHT_FILES_CHANGING = 'files_changing';


    /**
     * Возвращает древовидный список собственных прав модуля
     *
     * @return (Right|RightGroup)[]
     */
    protected function getSelfModuleRights()
    {
        return [
            new Right(self::RIGHT_READ, t('Чтение')),
            new Right(self::RIGHT_CREATE, t('Создание')),
            new Right(self::RIGHT_UPDATE, t('Изменение')),
            new Right(self::RIGHT_DELETE, t('Удаление')),
            new RightGroup('group_transaction', t('Операции с транзакциями'), [
                new Right(self::RIGHT_TRANSACTION_SHOW_IN_APP, t('Доступ из приложения')),
                new Right(self::RIGHT_TRANSACTION_ACTIONS, t('Выполнение действий')),
                new Right(self::RIGHT_ADD_FUNDS, t('Изменение баланса пользователя')),
            ]),
            new RightGroup('group_reservation', t('Операции с предзаказами'), [
                new Right(self::RIGHT_RESERVATION_SHOW_IN_APP, t('Доступ из приложения')),
                new Right(self::RIGHT_RESERVATION_CHANGING, t('Изменение предзаказа')),
                new Right(self::RIGHT_RESERVATION_ACTIONS, t('Выполнение действий')),
                new Right( self::RIGHT_RESERVATION_DELETE, t('Удаление предзаказа')),
            ]),
            new RightGroup('group_receipt', t('Операции с чеками'), [
                new Right(self::RIGHT_SEND_RECEIPT, t('Отправка чека')),
                new Right(self::RIGHT_CORRECTION_RECEIPT, t('Отправка чека коррекции')),
                new Right(self::RIGHT_REFUND_RECEIPT, t('Отправка чека возврата')),
            ]),
            new RightGroup('group_shipment', t('Отгрузка'), [
                new Right(self::RIGHT_MAKE_SHIPMENT, t('Разрешить производить отгрузку'))
            ]),
            new RightGroup('group_orders', t('Операции с заказами'), [
                new Right(self::RIGHT_STATISTIC_SHOW_IN_APP, t('Доступ из приложения к статистике по продажам')),
                new Right(self::RIGHT_ORDER_SHOW_IN_APP, t('Доступ из приложения к заказам')),
                new Right(self::RIGHT_DOCUMENTS_PRINTING, t('Печать документов')),
                new Right(self::RIGHT_EXPORT_REPORT, t('Экспорт/Отчёт заказов')),
                new RightGroup('group_order_status', t('Статус заказа'), [
                    new Right(self::RIGHT_STATUS_READING, t('Чтение')),
                    new Right(self::RIGHT_STATUS_CHANGING, t('Изменение')),
                ]),
                new RightGroup('group_order_customer', t('Покупатель'), [
                    new Right(self::RIGHT_CUSTOMER_READING, t('Чтение')),
                    new Right(self::RIGHT_CUSTOMER_CHANGING, t('Изменение ')),

                    new Right(self::RIGHT_CUSTOMER_FULLNAME_READING, t('Чтение полного имени')),
                    new Right(self::RIGHT_CUSTOMER_EMAIL_READING, t('Чтение Email')),
                    new Right(self::RIGHT_CUSTOMER_PHONE_READING, t('Чтение номера телефона')),
                    new Right(self::RIGHT_CUSTOMER_USERFIELD_READING, t('Чтение доп.сведений пользователя')),
                ]),
                new RightGroup('group_order_information', t('Информация о заказе'), [
                    new Right(self::RIGHT_INFORMATION_READING, t('Показывать блок "Информация о заказе"')),
                    new Right(self::RIGHT_INFORMATION_CHANGING, t('Изменение')),

                    new Right(self::RIGHT_EXTRA_INFO_READING, t('Чтение информационных полей')),
                    new Right(self::RIGHT_ADMIN_COMMENT_READING, t('Чтение комментария администратора')),
                    new Right(self::RIGHT_USER_COMMENT_READING, t('Чтение комментария пользователя')),
                    new Right(self::RIGHT_LAST_UPDATE_READING, t('Чтение даты последнего изменения')),
                    new Right(self::RIGHT_SHIPMENT_DATE_READING, t('Чтение даты отгрузки')),
                    new Right(self::RIGHT_CREATE_PLATFORM_READING, t('Чтение платформы создания')),
                    new Right(self::RIGHT_MANAGER_READING, t('Чтение менеджера')),
                    new Right(self::RIGHT_PROFIT_READING, t('Чтение доходности')),
                    new Right(self::RIGHT_USERFIELDS_READING, t('Чтение пользовательских полей')),
                ]),
                new RightGroup('group_order_address', t('Адрес'), [
                    new Right(self::RIGHT_ADDRESS_READING, t('Показывать блок "Адрес"')),
                    new Right(self::RIGHT_ADDRESS_CHANGING, t('Изменение')),
                ]),
                new RightGroup('group_order_delivery', t('Доставка'), [
                    new Right(self::RIGHT_DELIVERY_READING, t('Показывать блок "Доставка"')),
                    new Right(self::RIGHT_DELIVERY_CHANGING, t('Изменение')),

                    new Right(self::RIGHT_COURIER_READING, t('Чтение курьера')),
                    new Right(self::RIGHT_TRACK_NUMBER_READING, t('Чтение трек-номера')),
                    new Right(self::RIGHT_CONTACT_PERSON_READING, t('Чтение контактного лица')),
                    new Right(self::RIGHT_WAREHOUSE_READING, t('Чтение склада')),
                ]),
                new RightGroup('group_order_pay', t('Оплата'), [
                    new Right(self::RIGHT_PAY_READING, t('Показывать блок "Оплата"')),
                    new Right(self::RIGHT_PAY_CHANGING, t('Изменение')),

                    new Right(self::RIGHT_IS_PAY_READING, t('Чтение флага оплаты')),
                    new Right(self::RIGHT_PAY_DOCS_READING, t('Чтение документов оплаты')),
                ]),
                new RightGroup('group_order_crm', t('CRM'), [
                    new Right(self::RIGHT_CRM_READING, t('Чтение')),
                ]),
                new RightGroup('group_order_products', t('Состав заказа'), [
                    new Right(self::RIGHT_PRODUCTS_CHANGING, t('Изменение')),
                    new Right(self::RIGHT_PRODUCTS_ADD, t('Добавление товаров')),
                    new Right(self::RIGHT_PRODUCTS_DELETE, t('Удаление товаров')),
                    new Right(self::RIGHT_DISCOUNT_ADD, t('Добавление скидки')),
                    new Right(self::RIGHT_DISCOUNT_DELETE, t('Удаление скидки')),
                ]),
                new RightGroup('group_order_user_text', t('Текст для покупателя'), [
                    new Right(self::RIGHT_USER_TEXT_READING, t('Чтение')),
                    new Right(self::RIGHT_USER_TEXT_CHANGING, t('Изменение')),
                ]),
                new RightGroup('group_order_files', t('Файлы для покупателя'), [
                    new Right(self::RIGHT_FILES_CHANGING, t('Чтение и изменение')),
                ]),
                new RightGroup('group_cargo', t('Грузовые места'), [
                    new Right(self::RIGHT_CARGO_READING, t('Чтение')),
                    new Right(self::RIGHT_CARGO_CHANGING, t('Изменение')),
                ])
            ]),
        ];
    }
}
