Инструкция по запуску ReadyScript в Docker
==========================================

Используйте Docker, чтобы быстро запустить тестовый интернет-магазин на платформе ReadyScript 
и начать разработку собственных модулей или тем оформления. 

Docker позволяет с помощью одной команды, развернуть все необходимое LAMP окружение и запустить в нем сайт. 
Мы постарались подобрать оптимальные настройки, чтобы все функции ReadyScript работали сразу и их не 
пришлось дополнительно настраивать.

Docker-compose файл включает в себя:
- Apache
- PHP 8.1
- MariaDB 10.1 
- PHPMyAdmin
- Cron 

Инструкция для Windows
----------------------

Установите Docker из официального дистрибутива для Windows по ссылке: 
https://docs.docker.com/desktop/install/windows-install/

Скачайте дистрибутив ReadyScript необходимой редакции по одной из следующих ссылок:
- [Редакция Мегамаркет](https://readyscript.ru/downloads/readyscript-shop-mega.zip)
- [Редакция Гипермаркет](https://readyscript.ru/downloads/readyscript-shop-full.zip)
- [Редакция Маркет](https://readyscript.ru/downloads/readyscript-shop-middle.zip)
- [Редакция Витрина](https://readyscript.ru/downloads/readyscript-shop-base.zip)

Распакуйте архив в любую папку, перейдите в эту папку и выполните из коммандной строки:

    cd docker
    docker compose up

Для работы с сайтом на удобном домене rs.local, рекомендуем добавить в hosts файл (C:\Windows\System32\drivers\etc\hosts) строку:
    
    127.0.0.1 rs.local

После запуска группы контейнеров у вас будут доступны:
- http://rs.local - Сайт на платформе ReadyScript
- http://rs.local:8081 - PhpMyAdmin

Откройте в браузере: http://rs.local
Откроется мастер установки ReadyScript. Используйте следующие доступы к БД:
- Хост: mariadb
- Порт: 3306
- Имя базы данных: readyscript
- Имя пользователя БД: root
- Пароль пользователя БД: rootpwd

В качестве данных по умолчанию для авторизации в административной панели будут использованы:
- Email: demo@example.com
- Пароль: 123123

### Устранение проблем
#### Если 80 порт занят другим ПО
Поменяйте в docker-compose.yml файле порт, например на 8080  

    services:
        php-apache:
            ports:
                - 8080:80
                
В этом случае к сайту можно обращаться так:
- http://rs.local:8080 (Trial период в этом случае неограничен)
- http://localhost:8080 (В этом случае Trial-период будет 30 дней)