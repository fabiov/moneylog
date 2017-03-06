<?php

return array(
    'controllers' => array(
        'factories' => array(
            'Accantona\Controller\Recap' => function($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sl */
                $sl = $controllerManager->getServiceLocator();
                return new \Accantona\Controller\RecapController(
                    $sl->get('doctrine.entitymanager.orm_default'),
                    $sl->get('Accantona\Model\AccantonatoTable'),
                    $sl->get('Accantona\Model\VariabileTable'),
                    $sl->get('Zend\Authentication\AuthenticationService')->getIdentity()
                );
            },
            'Accantona\Controller\Spesa' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sl = $controllerManager->getServiceLocator();
                return new \Accantona\Controller\SpesaController(
                    $sl->get('Accantona\Model\SpesaTable'),
                    $sl->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $sl->get('doctrine.entitymanager.orm_default')
                );
            },
            'Accantona\Controller\Settings' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sl = $controllerManager->getServiceLocator();
                return new \Accantona\Controller\SettingsController(
                    $sl->get('doctrine.entitymanager.orm_default'),
                    $sl->get('Zend\Authentication\AuthenticationService')->getIdentity()
                );
            },
            'Accantona\Controller\Account' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sl = $controllerManager->getServiceLocator();
                return new \Accantona\Controller\AccountController(
                    $sl->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $sl->get('doctrine.entitymanager.orm_default')
                );
            },
            'Accantona\Controller\Accantonato' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sl = $controllerManager->getServiceLocator();
                return new \Accantona\Controller\AccantonatoController(
                    $sl->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $sl->get('doctrine.entitymanager.orm_default')
                );
            },
            'Accantona\Controller\Categoria' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sl = $controllerManager->getServiceLocator();
                return new \Accantona\Controller\CategoriaController(
                    $sl->get('Accantona\Model\CategoriaTable'),
                    $sl->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $sl->get('doctrine.entitymanager.orm_default')
                );
            },
            'Accantona\Controller\Moviment' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                /* @var Zend\ServiceManager\ServiceManager $sm */
                $sl = $controllerManager->getServiceLocator();
                return new \Accantona\Controller\MovimentController(
                    $sl->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $sl->get('doctrine.entitymanager.orm_default')
                );
            },
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'accantona_spesa' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/spesa[/:action][/:id]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Spesa', 'action' => 'index'),
                ),
            ),
            'accantona_categoria' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/categoria[/:action][/:id]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Categoria', 'action' => 'index'),
                ),
            ),
            'accantona_recap' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/dashboard[/:action]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Recap', 'action' => 'index'),
                ),
            ),
            'accantona_accantonato' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/accantonato[/:action][/:id]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Accantonato', 'action' => 'index'),
                ),
            ),
            'accantonaSettings' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/settings[/:action][/:id]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Settings', 'action' => 'index'),
                ),
            ),
            'accantonaAccount' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/account[/:action][/:id]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Account', 'action' => 'index'),
                ),
            ),
            'accantonaMoviment' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/moviment[/:action][/:id]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Moviment', 'action' => 'index'),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_path_stack' => array(
            'accantona' => __DIR__ . '/../view',
        ),
    ),

    'view_helpers' => array(
        'invokables' => array(
            'bindBalance'       => 'Accantona\View\Helper\BindBalance',
            'dateForma'         => 'Accantona\View\Helper\DateForma',
            'currencyForma'     => 'Accantona\View\Helper\CurrencyForma',
            'floatingButtons'   => 'Accantona\View\Helper\FloatingButtons',
            'morris'            => 'Accantona\View\Helper\Morris',
            'pageHeader'        => 'Accantona\View\Helper\PageHeader',
            'widgetSelect'      => 'Accantona\View\Helper\WidgetSelect',
            'widgetText'        => 'Accantona\View\Helper\WidgetText',
        ),
    ),
);
