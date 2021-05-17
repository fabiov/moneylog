<?php

declare(strict_types=1);

use Laminas\Code\Exception\RuntimeException;

return [
    'controllers' => [
        'invokables' => [
            Application\Controller\PageController::class,
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => Laminas\Router\Http\Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => ['controller' => Auth\Controller\UserController::class, 'action' => 'login'],
                ],
            ],
            'page' => [
                'type' => Laminas\Router\Http\Segment::class,
                'options' => [
                    'route'    => '/page[/:action]',
                    'defaults' => [
                        'controller' => Application\Controller\PageController::class
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            Laminas\Cache\Service\StorageCacheAbstractServiceFactory::class,
            Laminas\Log\LoggerAbstractServiceFactory::class,
        ],
        'aliases' => [
            'translator' => 'MvcTranslator',
        ],
    ],
    'translator' => [
        'locale' => 'it_IT',
        'translation_file_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            'application' => __DIR__ . '/../view'
        ],
    ],

    // Placeholder for console routes
    'console' => [
        'router' => [
            'routes' => [],
        ],
    ],

    // Doctrine config
    'doctrine' => [
        'driver' => [
            'application_entities' => [
                'class' => Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Application/Entity'],
            ],
            'orm_default' => [
                'drivers' => ['Application\Entity' => 'application_entities'],
            ],
        ]
    ],
    'view_helpers' => [
        'invokables' => [
            'commonJavascript' => Application\ViewHelper\CommonJavascript::class,
            'footer' => Application\ViewHelper\Footer::class,
            'helpTooltip' => Application\ViewHelper\HelpTooltip::class,
            'richInlineScript' => Application\ViewHelper\RichInlineScript::class,
            'sbaFormRow' => Application\ViewHelper\SbaFormRow::class,
            'userData' => Application\ViewHelper\UserData::class,
        ],
    ],
];
