<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return [
    'controllers' => [
        'invokables' => [
            Application\Controller\IndexController::class,
            Application\Controller\PageController::class,
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Laminas\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => ['controller' => Auth\Controller\UserController::class, 'action' => 'login'],
                ],
            ],
            // The following is a route to simplify getting started creating new controllers and actions without needing
            // to create a new module.
            // Simply drop new controllers in, and you can access them using the path /application/:controller/:action
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'defaults'    => [],
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*', 'action' => '[a-zA-Z][a-zA-Z0-9_-]*'
                            ],
                            'route'       => '/[:controller[/:action]]',
                        ],
                    ],
                ],
            ],
            'privacy_policy' => [
                'type' => Laminas\Router\Http\Literal::class,
                'options' => [
                    'route'    => '/privacy-policy',
                    'defaults' => [
                        'controller' => Application\Controller\PageController::class, 'action' => 'privacy-policy'
                    ],
                ],
            ],
            'offline' => [
                'type' => Laminas\Router\Http\Literal::class,
                'options' => [
                    'route'    => '/offline',
                    'defaults' => [
                        'controller' => Application\Controller\PageController::class, 'action' => 'offline'
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => [
            'Laminas\Cache\Service\StorageCacheAbstractServiceFactory',
            'Laminas\Log\LoggerAbstractServiceFactory',
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
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
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
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
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
            'helpTooltip'      => 'Application\ViewHelper\HelpTooltip',
            'richInlineScript' => 'Application\ViewHelper\RichInlineScript',
            'sbaFormRow'       => 'Application\ViewHelper\SbaFormRow',
            'userData'         => 'Application\ViewHelper\UserData',
        ],
    ],
];
