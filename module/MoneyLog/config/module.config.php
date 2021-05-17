<?php

declare(strict_types=1);

return [
    'controllers' => [
        'factories' => [
            MoneyLog\Controller\RecapController::class => function ($controllerManager) {
                /* @var Laminas\Mvc\Controller\ControllerManager $controllerManager */
                return new MoneyLog\Controller\RecapController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get('Laminas\Authentication\AuthenticationService')->getIdentity()
                );
            },
            MoneyLog\Controller\SettingsController::class => function ($controllerManager) {
                /* @var Laminas\Mvc\Controller\ControllerManager $controllerManager */
                return new MoneyLog\Controller\SettingsController(
                    $controllerManager->get('doctrine.entitymanager.orm_default'),
                    $controllerManager->get('Laminas\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('user_data')
                );
            },
            MoneyLog\Controller\AccountController::class => function ($controllerManager) {
                /* @var Laminas\Mvc\Controller\ControllerManager $controllerManager */
                return new MoneyLog\Controller\AccountController(
                    $controllerManager->get('Laminas\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default')
                );
            },
            MoneyLog\Controller\ProvisionController::class => function ($controllerManager) {
                /* @var Laminas\Mvc\Controller\ControllerManager $controllerManager */
                return new MoneyLog\Controller\ProvisionController(
                    $controllerManager->get('Laminas\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default')
                );
            },
            MoneyLog\Controller\CategoriaController::class => function ($controllerManager) {
                /* @var Laminas\Mvc\Controller\ControllerManager $controllerManager */
                return new MoneyLog\Controller\CategoriaController(
                    $controllerManager->get('Laminas\Authentication\AuthenticationService')->getIdentity(),
                    $controllerManager->get('doctrine.entitymanager.orm_default')
                );
            },
            MoneyLog\Controller\MovementController::class => function ($controllerManager) {
                /* @var Laminas\Mvc\Controller\ControllerManager $controllerManager */
                return new MoneyLog\Controller\MovementController(
                    $controllerManager->get('Laminas\Authentication\AuthenticationService')->getIdentity(),
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
                    'defaults'      => [
                        'controller' => MoneyLog\Controller\CategoriaController::class, 'action' => 'index'
                    ],
                ],
            ],
            'accantona_recap'       => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/dashboard[/:action]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults'      => [
                        'controller' => MoneyLog\Controller\RecapController::class, 'action' => 'index'
                    ],
                ],
            ],
            'accantona_accantonato' => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/accantonato[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults' => [
                        'controller' => MoneyLog\Controller\ProvisionController::class, 'action' => 'index'
                    ],
                ],
            ],
            'accantonaSettings'     => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/settings[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults' => ['controller' => MoneyLog\Controller\SettingsController::class, 'action' => 'index'],
                ],
            ],
            'accantonaAccount'      => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/account[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults' => ['controller' => MoneyLog\Controller\AccountController::class, 'action' => 'index'],
                ],
            ],
            'accantonaMovement'     => [
                'type'      => 'segment',
                'options'   => [
                    'route'         => '/movement[/:action][/:id]',
                    'constraints'   => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*', 'id' => '[0-9]+'],
                    'defaults' => ['controller' => MoneyLog\Controller\MovementController::class, 'action' => 'index'],
                ],
            ],
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'balanceModalForm'  => MoneyLog\View\Helper\BalanceModalForm::class,
            'currencyForma'     => MoneyLog\View\Helper\CurrencyForma::class,
            'dataTable'         => MoneyLog\View\Helper\DataTable::class,
            'dateForma'         => MoneyLog\View\Helper\DateForma::class,
            'floatingButtons'   => MoneyLog\View\Helper\FloatingButtons::class,
            'morris'            => MoneyLog\View\Helper\Morris::class,
            'pageHeader'        => MoneyLog\View\Helper\PageHeader::class,
            'synopsisFilters'   => MoneyLog\View\Helper\SynopsisFilters::class,
            'widgetText'        => MoneyLog\View\Helper\WidgetText::class,
        ],
    ],
    'view_manager' => ['template_path_stack' => ['accantona' => __DIR__ . '/../view']],
];
