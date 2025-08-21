<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/

namespace Cdn\Model;
use RS\Orm\PropertyIterator;
use \RS\Orm\Type;


use RS\Orm\FormObject;

class RegistrationForm extends FormObject
{
    public function __construct()
    {
        parent::__construct(new PropertyIterator([
            'company' => new Type\Varchar([
                'description' => t('Наименование компании или ФИО'),
                'default' => \RS\Config\Loader::getSiteConfig()->firm_name,
                'checker' => ['ChkEmpty', t('Не указано наименование компании или ФИО')],
            ]),
            'phone' => new Type\Varchar([
                'description' => t('Телефон для связи'),
                'default' => \RS\Config\Loader::getSiteConfig()->admin_phone,
                'checker' => ['ChkEmpty', t('Не указан телефон')],
            ]),
            'email' => new Type\Varchar([
                'description' => t('Email'),
                'default' => \RS\Config\Loader::getSiteConfig()->admin_email,
                'checker' => ['ChkEmail', t('Неверно указан Email')],
                'trimString' => true,

            ]),
            'name' => new Type\Varchar([
                'description' => t('Символьный идентификатор (a-z)'),
                'checker' => ['ChkEmpty', t('Укажите идентификатор')],
                'default' => str_replace('.', '', \Setup::$DOMAIN),
            ]),
            'cache_lifetime' => new Type\Integer([
                'description' => t('Время жизни кэша'),
                'listFromArray' => [[
                    '7' => t('Неделя'),
                    '30' => t('Месяц'),
                    '365' => t('Год'),
                ]],
            ]),
            'cache_size' => new Type\Integer([
                'description' => t('Размер кэша, Мб'),
                'default' => 500,
            ]),
        ]));
    }
    
    function sendRegistrationEmail()
    {        
        $test_url =  \RS\Site\Manager::getSite()->getRootUrl(true). 'resource/img/photostub/nophoto.jpg';
        
        $post_data = http_build_query($this->getValues() + [
            'test_url' => $test_url,
            'domain'   => \Setup::$DOMAIN,
            'https'    => Utils::isHttps(),
            'lang'     => \RS\Language\Core::getCurrentLang()
            ]);
        
        $join_url = \Setup::$RS_SERVER_PROTOCOL.'://'.\Setup::$RS_SERVER_DOMAIN.'/joincdnvideo/';
        
        $context = stream_context_create(['http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $post_data
            ]
        ]);
        
        $result = @file_get_contents($join_url, false, $context);
        if ($result) {
            $json_result = json_decode($result);
            if (empty($json_result->success)) {
                return $this->addError($json_result->error);
            }
        } else {
            return $this->addError(t('Невозможно установить соединение с сервером ReadyScript.'));
        }
    
        return true;
    }
}