<?php

return [
    'controllers' => [
        'factories' => [
            'Accantona\Controller\Recap' => function($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new \Accantona\Controller\RecapController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get('Accantona\Model\AccantonatoTable'),
                    $controllerManager->get('Zend\Authentication\AuthenticationService')->getIdentity()
                );
            },
            'Accantona\Controller\Settings' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new \Accantona\Controller\SettingsController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('user_data')
                );
            },
            'Accantona\Controller\Account' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new \Accantona\Controller\AccountController(
                    $controllerManager->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default')
                );
            },
            'Accantona\Controller\Accantonato' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new \Accantona\Controller\AccantonatoController(
                    $controllerManager->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default')
                );
            },
            'Accantona\Controller\Categoria' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new \Accantona\Controller\CategoriaController(
                    $controllerManager->get('Accantona\Model\CategoriaTable'),
                    $controllerManager->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default')
                );
            },
            'Accantona\Controller\Movement' => function ($controllerManager) {
                /* @var Zend\Mvc\Controller\ControllerManager $controllerManager */
                return new \Accantona\Controller\MovementController(
                    $controllerManager->get('Zend\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default')
                );
            },
        ],
    ],

    // The following section is new and should be added to your file
    'router' => [
        'routes' => array(
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
            'accantonaMovement' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/moviment[/:action][/:id]',
                    'constraints' => array('action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'),
                    'defaults' => array('controller' => 'Accantona\Controller\Movement', 'action' => 'index'),
                ),
            ),
        ),
    ],

    'view_manager' => [
        'template_path_stack' => array(
            'accantona' => __DIR__ . '/../view',
        ),
    ],

    'view_helpers' => [
        'invokables' => array(
            'balanceModalForm'  => Accantona\View\Helper\BalanceModalForm::class,
            'currencyForma'     => Accantona\View\Helper\CurrencyForma::class,
            'dataTable'         => Accantona\View\Helper\DataTable::class,
            'dateForma'         => Accantona\View\Helper\DateForma::class,
            'floatingButtons'   => Accantona\View\Helper\FloatingButtons::class,
            'morris'            => Accantona\View\Helper\Morris::class,
            'pageHeader'        => Accantona\View\Helper\PageHeader::class,
            'synopsisFilters'   => Accantona\View\Helper\SynopsisFilters::class,
            'widgetSelect'      => Accantona\View\Helper\WidgetSelect::class,
            'widgetText'        => Accantona\View\Helper\WidgetText::class,
        ),
    ],
];