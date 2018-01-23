<?php
return [
    'controllers' => [
        'factories' => array(
            'Auth\Controller\User' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new Auth\Controller\UserController(
                    $controllerManager->get('Zend\Db\Adapter\Adapter'),
                    $controllerManager->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get('user_data')
                );
            },
            'Auth\Controller\Registration' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new Auth\Controller\RegistrationController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager,
                    $controllerManager->get('Auth\Model\UserTable')
                );
            },
        ),
    ],
    'router' => [
        'routes' => array(
            'auth' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/auth',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller' => 'User',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array('controller' => 'user', 'action' => 'index'),
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
            \Auth\Service\AuthAdapter::class => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new Auth\Service\AuthAdapter($controllerManager->get('doctrine.entitymanager.orm_default'));
            },
        ],
        'invokables' => [
            'my_auth_service' => 'Zend\Authentication\AuthenticationService',
            'user_data'       => 'Auth\Service\UserData',
        ],
    ],
    'view_manager' => [
        'display_exceptions'  => true,
        'template_path_stack' => ['auth' => __DIR__ . '/../view'],
    ],
];