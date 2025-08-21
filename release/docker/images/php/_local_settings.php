<?php
//Параметры установки ReadyScript по умолчанию. Эти данные будут использованы в модуле install
\Setup::$INSTALL_DB_HOST = 'mariadb';
\Setup::$INSTALL_DB_PORT = 3306;
\Setup::$INSTALL_DB_NAME = 'readyscript';
\Setup::$INSTALL_DB_USERNAME = 'root';
\Setup::$INSTALL_DB_PASSWORD = 'rootpwd';
\Setup::$INSTALL_ADMIN_LOGIN = 'demo@example.com';
\Setup::$INSTALL_ADMIN_PASSWORD = '123123';
\Setup::$INSTALL_SET_DEMO_DATA = true;