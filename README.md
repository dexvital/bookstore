# bookstore (Symfony 4.1)

1. Requirements:
    1. composer
    2. php >= 7.1
    3. webpack
    4. mysql >= 5.6

2. To use project:
    1. git clone or copy
    2. composer install
    3. rename .env.dist file to .env and type your database connection data
    4. to fill some data: php bin/console doctrine:fixtures:load
    5. php bin/console doctrine:migrations:migrate to add database tables
    6. php bin/console doctrine:fixtures:load to load default data
    7. map host to public folder
    