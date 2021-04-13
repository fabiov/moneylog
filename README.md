# MoneyLog

## Introduction

This is a web applycation to track your money.

## Update data schema
```shell
php vendor/bin/doctrine-module orm:schema-tool:update --force
```

## Genetare migrations
```shell
php vendor/bin/doctrine-module migrations:generate
```

## Migrate
```shell
./vendor/bin/doctrine-module migrations:diff
./vendor/bin/doctrine-module migrations:migrate
```

## Connessione al database del container
```shell
mysql -h localhost -P 3306 -u dbuser -pdbpass --protocol=tcp moneylog
```

## Code Style
```shell
$ php-cs-fixer fix --rules='{"array_syntax": {"syntax": "short"}}' module/Authorize/config/module.config.php
```
