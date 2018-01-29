<?php
return [
    'controllers' => [
        'factories' => array(
            Auth\Controller\User::class => function (Zend\ServiceManager\ServiceManager $controllerManager) {
                return new Auth\Controller\UserController(
                    $controllerManager->get(Zend\Authentication\AuthenticationService::class)->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get(Auth\Service\AuthManager::class)
                );
            },
            Auth\Controller\Registration::class => function (Zend\ServiceManager\ServiceManager $controllerManager) {
                return new Auth\Controller\RegistrationController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager,
                    $controllerManager->get(Auth\Model\UserTable::class)
                );
            },
        ),
    ],
    'router' => [
        'routes' => array(
            'auth' => array(
                'type' => 'Literal',
                'options' => array(
                    'defaults' => ['__NAMESPACE__' => 'Auth\Controller', 'controller' => 'User', 'action' => 'index'],
                    'route'    => '/auth',
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'         => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => ['controller' => 'user', 'action' => 'index'],
                        ),
                    ),
                ),
            ),
        ),
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
                    $container->get(Zend\Authentication\AuthenticationService::class),
                    $container->get(Zend\Session\SessionManager::class)
                );
            },
            Zend\Authentication\AuthenticationService::class => \Auth\Service\Factory\AuthenticationServiceFactory::class,
        ],
        'invokables' => [
            'user_data' => 'Auth\Service\UserData',
        ],
    ],
    'view_manager' => [
        'display_exceptions'  => true,
        'template_path_stack' => ['auth' => __DIR__ . '/../view'],
    ],
];