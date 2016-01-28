<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

// set main configurations
$conf = array(
    'db' => array(
        'driver' => 'Pdo',
        'dsn' => 'mysql:dbname=accantona_prod;host=localhost',
        'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
        'username' => 'root',
        'password' => 'root',
//        'driver'   => 'Pdo_Sqlite',
//        'database' => __DIR__ . '/../../data/accantona.sqlite',
    ),
    'service_manager' => array(
        'factories' => array('Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory'),
    ),
);

// set configuration for specific environment
switch (APP_ENV) {
    case 'development':
        $conf['db'] = array(
            'driver' => 'Pdo',
            'dsn' => 'mysql:dbname=accantona_dev;host=localhost',
            'driver_options' => array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''),
            'username' => 'root',
            'password' => 'root',
        );
        break;
}

return $conf;