<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Accantona\Controller\Spesa' => 'Accantona\Controller\SpesaController',
            'Accantona\Controller\Categoria' => 'Accantona\Controller\CategoriaController',
            'Accantona\Controller\Recap' => 'Accantona\Controller\RecapController',
            'Accantona\Controller\Accantonato' => 'Accantona\Controller\AccantonatoController',
            'Accantona\Controller\Settings' => 'Accantona\Controller\SettingsController',
            'Accantona\Controller\Account' => 'Accantona\Controller\AccountController',
            'Accantona\Controller\Moviment' => 'Accantona\Controller\MovimentController',
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
                    'route' => '/recap[/:action]',
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
            'dateForma' => 'Accantona\View\Helper\DateForma',
            'currencyForma' => 'Accantona\View\Helper\CurrencyForma',
        ),
    ),

);
