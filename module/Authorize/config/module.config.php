<?php
return [
    'acl' => [
        'roles' => [
            'guest' => null,
            'user'  => 'guest', // member extendes guest
        ],
        'resources' => [
            'allow' => [
                'MoneyLog\Controller\Recap' => [
                    'add'   => 'user',
                    'edit'  => 'user',
                    'index' => 'user',
                ],
                'MoneyLog\Controller\Categoria' => [
                    'add'       => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'index'     => 'user',
                ],
                'MoneyLog\Controller\Accantonato' => [
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
                    'moviment'  => 'user',
                ],
                'MoneyLog\Controller\Movement' => [
                    'account'   => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'expense'   => 'user',
                    'export'    => 'user',
                    'income'    => 'user',
                    'index'     => 'user',
                    'move'      => 'user',
                    'moviment'  => 'user',
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