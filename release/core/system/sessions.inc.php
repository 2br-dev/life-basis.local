<?php
/**
* ReadyScript (http://readyscript.ru)
*
* @copyright Copyright (c) ReadyScript lab. (http://readyscript.ru)
* @license http://readyscript.ru/licenseAgreement/
*/
session_name( \Setup::getSessionName() );
if ($session_id = \Setup::getCustomSessionId()) {
    session_id($session_id);
}
session_set_cookie_params(0, '/', null, false, true);
session_start(['use_only_cookies' => 0]);