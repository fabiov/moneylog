<?php

declare(strict_types=1);

use Laminas\Authentication\AuthenticationService;

return [
    'controllers' => [
        'factories' => [
            Auth\Controller\UserController::class => function (Laminas\ServiceManager\ServiceManager $controllerManager) {
                return new Auth\Controller\UserController(
                    $controllerManager->get(AuthenticationService::class)->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get(Auth\Service\AuthManager::class)
                );
            },
            Auth\Controller\RegistrationController::class => function (Laminas\ServiceManager\ServiceManager $controllerManager) {
                return new Auth\Controller\RegistrationController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager
                );
            },
        ],
    ],
    'router' => [
        'routes' => [
            'auth' => [
                'type' => 'segment',
                'options' => [
                    'constraints' => ['action' => '[a-zA-Z][a-zA-Z0-9_-]+', 'id' => '[\w]+'],
                    'defaults' => ['controller' => Auth\Controller\UserController::class, 'action' => 'index'],
                    'route' => '/auth/user[/:action][/:id]',
                ],
            ],
            'auth_registration' => [
                'type' => 'segment',
                'options' => [
                    'constraints' => ['action' => '[a-zA-Z][a-zA-Z0-9_-]+', 'id' => '[\w]+'],
                    'defaults' => ['controller' => Auth\Controller\RegistrationController::class, 'action' => 'index'],
                    'route' => '/auth/registration[/:action][/:id]',
                ],
            ],
        ],
    ],
    'service_manager' => [
        // added for Authentication and Authorization. Without this each time we have to create a new instance.
        // This code should be moved to a module to allow Doctrine to overwrite it
        'aliases' => [],
        'factories' => [
            Auth\Service\AuthAdapter::class => function (Interop\Container\ContainerInterface $controllerManager) {
                return new Auth\Service\AuthAdapter($controllerManager->get('doctrine.entitymanager.orm_default'));
            },
            Auth\Service\AuthManager::class => function (Interop\Container\ContainerInterface $container) {
                return new Auth\Service\AuthManager(
                    $container->get(AuthenticationService::class),
                    $container->get(Laminas\Session\SessionManager::class),
                    $container->get(Auth\Service\UserData::class)
                );
            },
            AuthenticationService::class => Auth\Service\Factory\AuthenticationServiceFactory::class,
        ],
        'invokables' => [
            'user_data' => 'Auth\Service\UserData',
        ],
    ],
    'view_manager' => [
        'display_exceptions' => true,
        'template_path_stack' => [
            'auth' => __DIR__ . '/../view',
        ],
    ],
];
