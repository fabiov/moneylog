# MoneyLog

## Introduction

This is a web applycation to track your money.

## Update data schema
```
php vendor/bin/doctrine-module orm:schema-tool:update --force
```

## Genetare migrations
```
php vendor/bin/doctrine-module migrations:generate
```

## Migrate
```
./vendor/bin/doctrine-module migrations:diff
./vendor/bin/doctrine-module migrations:migrate
```