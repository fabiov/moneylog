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
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Page'  => 'Application\Controller\PageController',
        ],
    ],
    'router' => [
        'routes' => [
            'home' => [
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => ['controller' => 'Auth\Controller\User', 'action' => 'login'],
                ],
            ],
            // The following is a route to simplify getting started creating new controllers and actions without needing
            // to create a new module.
            // Simply drop new controllers in, and you can access them using the path /application/:controller/:action
            'application' => array(
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
            ),
            'privacy_policy' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/privacy-policy',
                    'defaults' => ['controller' => 'Application\Controller\Page', 'action' => 'privacyPolicy'],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ],
    'translator' => [
        'locale' => 'it_IT',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => [
            'application' => __DIR__ . '/../view'
        ],
    ],

    // Placeholder for console routes
    'console' => array(
        'router' => [
            'routes' => [],
        ],
    ),

    // Doctrine config
    'doctrine' => array(
        'driver' => array(
            'application_entities' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Application/Entity'],
            ),
            'orm_default' => array(
                'drivers' => ['Application\Entity' => 'application_entities'],
            ),
        )
    ),
    'view_helpers' => [
        'invokables' => [
            'helpTooltip'      => 'Application\ViewHelper\HelpTooltip',
            'richInlineScript' => 'Application\ViewHelper\RichInlineScript',
            'sbaFormRow'       => 'Application\ViewHelper\SbaFormRow',
            'userData'         => 'Application\ViewHelper\UserData',
        ],
    ],
];