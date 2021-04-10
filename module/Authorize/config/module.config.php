<?php
return [
    'acl' => [
        'roles' => [
            'guest' => null,
            'user'  => 'guest', // member extendes guest
        ],
        'resources' => [
            'allow' => [
                MoneyLog\Controller\RecapController::class => [
                    'add'   => 'user',
                    'edit'  => 'user',
                    'index' => 'user',
                ],
                MoneyLog\Controller\CategoriaController::class => [
                    'add'       => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'index'     => 'user',
                ],
                MoneyLog\Controller\AccantonatoController::class => [
                    'add'       => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'index'     => 'user',
                ],
                'MoneyLog\Controller\Settings' => [
                    'index' => 'user',
                ],
                'MoneyLog\Controller\Account' => [
                    'add'       => 'user',
                    'balance'   => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'index'     => 'user',
                    'movement'  => 'user',
                ],
                'MoneyLog\Controller\Movement' => [
                    'account'   => 'user',
                    'add'       => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'export'    => 'user',
                    'index'     => 'user',
                    'move'      => 'user',
                    'movement'  => 'user',
                ],
                'Auth\Controller\User' => [
                    'change-password' => 'user',
                    'login'           => 'guest',
                    'logout'          => 'guest',
                    'update'          => 'user',
                ],
                'Auth\Controller\Registration' => [
                    'confirm-email'             => 'guest',
                    'forgotten-password'        => 'guest',
                    'index'                     => 'guest',
                    'password-change-success'   => 'guest',
                    'registration-success'      => 'guest',
                ],
                'Application\Controller\Page' => [
                    'all' => 'guest',
                ],
            ],
        ],
    ],
];
