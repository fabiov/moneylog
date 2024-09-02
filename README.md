# MoneyLog

## Introduction

This is a web applycation to track your money.

## Set hooks
```shell
git config core.hooksPath .githooks
```

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
$ php-cs-fixer fix --rules='{"array_syntax": {"syntax": "short"}, "ordered_imports": true}' <file>
```

## PHP Stan
```shell
$ vendor/bin/phpstan analyze --level=0 module
```

## crontab
```
# m h  dom mon dow   command
0   0   *   *   *   mysqldump moneylog --no-tablespaces | bzip2 -c > /home/fabio/backup/moneylog_`date +\%Y-\%m-\%d_\%H`.sql.bz2
0   0   *   *   *   find /home/fabio/backup -mtime +7 -delete
1   0   *   *   *   /home/fabio/dropbox_uploader.sh -f /home/fabio/.dropbox_uploader -q upload backup/* .
```
