<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source control,
 * so do not include passwords or other sensitive information in this file.
 */
return [
    'db'              => ['driver' => 'Pdo'],
    'doctrine' => [
        // migrations configuration
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/Migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Migrations',
                'table'     => 'migrations',
            ],
        ],
    ],
    'service_manager' => [
        'factories' => ['Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory'],
    ],
    'session_config'  => [
        'cookie_lifetime' => 3600,      // Session cookie will expire in 1 hour.
        'gc_maxlifetime'  => 3600 * 24, // Session data will be stored on server maximum for 30 days.
    ],
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            // RemoteAddr::class,
            // HttpUserAgent::class,
        ]
    ],
    'session_storage' => [
        'type' => Laminas\Session\Storage\SessionArrayStorage::class
    ],
];
