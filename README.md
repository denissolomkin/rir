Процедура запуска:
- Скачать PHP 7.1 https://windows.php.net/download/#php-7.1-ts-VC14-x64
- Распаковать в папку, например с:/php7.1
- Добавить строку "extension=php_pdo_sqlite.dll" в с:/php7.1/php.ini
- Добавить строку "extension=php_sqlite3.dll" в с:/php7.1/php.ini
- Скачать Composer https://getcomposer.org/Composer-Setup.exe (при установке должен найти и предложить использовать скаченный до этого php)
- Сделать доступным php и сomposer https://www.forevolve.com/en/articles/2016/10/27/how-to-add-your-php-runtime-directory-to-your-windows-10-path-environment-variable/
- Скачать и установить Git Bash
* Важно, все последующие действия выполнять в Git Bash
- Удостовериться, что php и composer доступен из консоли php -v и composer about
- Скачать репозиторий приложения git clone https://github.com/cleardenissolomkin/rir
- Выполнить команду cd rir
- Скачать и установить npm и yarn
- Выполнить yarn install
- Выполнить yarn build
- Скачать и установить Symfony клиент https://get.symfony.com/cli/setup.exe
- Выполнить symfony server:ca:install
- Выполнить composer install