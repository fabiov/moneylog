<?php

return [
    'acl' => [
        'roles' => [
            'guest' => null,
            'user'  => 'guest', // member extends guest
        ],
        'resources' => [
            'allow' => [
                Application\Controller\PageController::class => [
                    'offline' => 'guest',
                    'privacy-policy' => 'guest',
                ],
                Auth\Controller\RegistrationController::class => [
                    'confirm-email' => 'guest',
                    'forgotten-password' => 'guest',
                    'index' => 'guest',
                    'password-change-success' => 'guest',
                    'registration-success' => 'guest',
                ],
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
                MoneyLog\Controller\ProvisionController::class => [
                    'add'       => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'index'     => 'user',
                ],
                MoneyLog\Controller\SettingsController::class => [
                    'index' => 'user',
                ],
                MoneyLog\Controller\AccountController::class => [
                    'add'       => 'user',
                    'balance'   => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'index'     => 'user',
                    'movement'  => 'user',
                ],
                MoneyLog\Controller\MovementController::class => [
                    'account'   => 'user',
                    'add'       => 'user',
                    'delete'    => 'user',
                    'edit'      => 'user',
                    'export'    => 'user',
                    'index'     => 'user',
                    'move'      => 'user',
                    'movement'  => 'user',
                ],
                Auth\Controller\UserController::class => [
                    'change-password' => 'user',
                    'login'           => 'guest',
                    'logout'          => 'guest',
                    'update'          => 'user',
                ],
            ],
        ],
    ],
];
