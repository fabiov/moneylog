<?php
return array(
    'controllers' => array(
        'factories' => array(
            'Auth\Controller\User' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sm = $controllerManager->getServiceLocator();
                return new Auth\Controller\UserController(
                    $sm->get('Zend\Db\Adapter\Adapter'),
                    $sm->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $sm->get('doctrine.entitymanager.orm_default')
                );
            },
            'Auth\Controller\Registration' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sm = $controllerManager->getServiceLocator();
                return new Auth\Controller\RegistrationController(
                    $sm->get('doctrine.entitymanager.orm_default'),
                    $sm,
                    $sm->get('Auth\Model\UserTable')
                );
            },
        ),
    ),
    'router' => array(
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
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'auth' => __DIR__ . '/../view'
        ),
        'display_exceptions' => true,
    ),
    'service_manager' => array(
        // added for Authentication and Authorization. Without this each time we have to create a new instance.
        // This code should be moved to a module to allow Doctrine to overwrite it
        'aliases' => array( // !!! aliases not alias
            'Zend\Authentication\AuthenticationService' => 'my_auth_service',
        ),
        'invokables' => array(
            'my_auth_service' => 'Zend\Authentication\AuthenticationService',
        ),
    ),
);