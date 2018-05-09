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
    'router' => [
        'routes' => [
            'accantona_categoria'   => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/categoria[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults'      => ['controller' => Accantona\Controller\Categoria::class, 'action' => 'index'],
                ],
            ],
            'accantona_recap'       => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/dashboard[/:action]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults'      => ['controller' => Accantona\Controller\Recap::class, 'action' => 'index'],
                ],
            ],
            'accantona_accantonato' => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/accantonato[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults'      => ['controller' => Accantona\Controller\Accantonato::class, 'action' => 'index'],
                ],
            ],
            'accantonaSettings'     => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/settings[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults'      => ['controller' => Accantona\Controller\Settings::class, 'action' => 'index'],
                ],
            ],
            'accantonaAccount'      => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/account[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults'      => ['controller' => Accantona\Controller\Account::class, 'action' => 'index'],
                ],
            ],
            'accantonaMovement'     => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/moviment[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults'      => ['controller' => Accantona\Controller\Movement::class, 'action' => 'index'],
                ],
            ],
        ],
    ],
    'view_helpers' => [
        'invokables' => [
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
        ],
    ],
    'view_manager' => ['template_path_stack' => ['accantona' => __DIR__ . '/../view']],
];