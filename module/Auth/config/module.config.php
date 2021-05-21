<?php

declare(strict_types=1);

use Auth\Controller\UserController;
use Laminas\Authentication\AuthenticationService;
use Auth\Controller\RegistrationController;

return [
    'controllers' => [
        'factories' => [
            UserController::class => function (Laminas\ServiceManager\ServiceManager $controllerManager) {
                return new UserController(
                    $controllerManager->get(AuthenticationService::class)->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get(Auth\Service\AuthManager::class)
                );
            },
            RegistrationController::class => function (Laminas\ServiceManager\ServiceManager $controllerManager) {
                return new RegistrationController(
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
                    'defaults' => ['controller' => UserController::class, 'action' => 'index'],
                    'route' => '/auth/user[/:action][/:id]',
                ],
            ],
            'auth_registration' => [
                'type' => 'segment',
                'options' => [
                    'constraints' => ['action' => '[a-zA-Z][a-zA-Z0-9_-]+', 'id' => '[\w]+'],
                    'defaults' => ['controller' => RegistrationController::class, 'action' => 'index'],
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
                    $container->get(Auth\Service\UserData::class)
                );
            },
            AuthenticationService::class => function (Interop\Container\ContainerInterface $container): AuthenticationService {
                return new AuthenticationService(
                    new Laminas\Authentication\Storage\Session(),
                    $container->get(Auth\Service\AuthAdapter::class)
                );
            },
        ],
        'invokables' => [
            'user_data' => Auth\Service\UserData::class,
        ],
    ],
    'view_manager' => [
        'display_exceptions' => true,
        'template_path_stack' => [
            'auth' => __DIR__ . '/../view',
        ],
    ],
];
