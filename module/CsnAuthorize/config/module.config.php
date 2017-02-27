<?php
return array(
    'acl' => array(
        'roles' => array(
            'guest' => null,
            'user' => 'guest', // member extendes guest
        ),
        'resources' => array(
            'allow' => array(
                'Accantona\Controller\Recap' => array(
                    'index' => 'user',
                    'add' => 'user',
                    'edit' => 'user',
                ),
                'Accantona\Controller\Spesa' => array(
                    'add' => 'user',
                    'index' => 'user',
                    'edit' => 'user',
                    'delete' => 'user',
                ),
                'Accantona\Controller\Categoria' => array(
                    'add' => 'user',
                    'index' => 'user',
                    'edit' => 'user',
                    'delete' => 'user',
                ),
                'Accantona\Controller\Accantonato' => array(
                    'add' => 'user',
                    'index' => 'user',
                    'edit' => 'user',
                    'delete' => 'user',
                ),
                'Accantona\Controller\Settings' => array(
                    'index' => 'user',
                ),
                'Accantona\Controller\Account' => array(
                    'add' => 'user',
                    'balance' => 'user',
                    'delete' => 'user',
                    'edit' => 'user',
                    'index' => 'user',
                    'moviment' => 'user',
                ),
                'Accantona\Controller\Moviment' => array(
                    'account' => 'user',
                    'add' => 'user',
                    'move' => 'user',
                    'moviment' => 'user',
                    'index' => 'user',
                    'edit' => 'user',
                    'delete' => 'user',
                ),
                'Auth\Controller\User' => array(
                    'login'     => 'guest',
                    'logout'    => 'guest',
                    'update'    => 'user',
                ),
                'Auth\Controller\Registration' => array(
                    'confirm-email'             => 'guest',
                    'forgotten-password'        => 'guest',
                    'index'                     => 'guest',
                    'password-change-success'   => 'guest',
                    'registration-success'      => 'guest',
                ),
                'PhlySimplePage\Controller\Page' => array(
                    'all' => 'guest',
                ),
            ),
        ),
    ),
);