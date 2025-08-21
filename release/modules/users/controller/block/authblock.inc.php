<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
namespace Users\Controller\Block;

use RS\Controller\StandartBlock;

/**
* Блок авторизации
*/
class AuthBlock extends StandartBlock
{
    protected static
        $controller_title = 'Блок авторизации',        //Краткое название контроллера
        $controller_description = 'Отображает ссылки войти/зарегистрироваться или имя текущего пользователя и персональным меню';  //Описание контроллера        
        
    protected
        $default_params = [
            'indexTemplate' => 'blocks/authblock/authblock.tpl', //Должен быть задан у наследника
    ];
    
    function actionIndex()
    {
        if (\RS\Module\Manager::staticModuleExists('shop')) {
            $shop_config = \RS\Config\Loader::byModule('shop');
            $this->view->assign([
                'use_personal_account' => $shop_config->use_personal_account,
                'return_enable' => $shop_config->return_enable
            ]);
        }

        $current_route = $this->router->getCurrentRoute();
        if ($current_route
            && !in_array($current_route->getId(), [
                'users-front-auth',
                'users-front-register']))
        {
            $referer = urlencode($this->url->server('REQUEST_URI'));
        } else {
            $referer = null;
        }

        $this->view->assign([
            'referer' => $referer,
            'authorization_url' => $this->getModuleConfig()->getAuthorizationUrl([
                'referer' => $referer
            ]),
            'registration_url' => $this->router->getUrl('users-front-register', ['referer' => $referer]),
            'users_config' => $this->getModuleConfig(),
        ]);

        return $this->result->setTemplate( $this->getParam('indexTemplate') );
    }
}