<?php
return array(
    'acl' => array(
        'roles' => array(
            'guest' => null,
            'user' => 'guest', // member extendes guest
        ),
        'resources' => array(
            'allow' => array(
                'Auth\Controller\Registration' => array(
                    'confirm-email' => 'guest',
                    'forgotten-password' => 'guest',
                    'index' => 'guest',
                    'registration-success' => 'guest',
                ),
                'Accantona\Controller\Recap' => array(
                    'index' => 'user',
                    'add' => 'user',
                    'edit' => 'user',
                ),
                'Auth\Controller\Index' => array(
                    'index' => 'guest',
                    'login' => 'guest',
                    'logout' => 'guest',
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
                    'index' => 'user',
                    'edit' => 'user',
                    'moviment' => 'user',
                ),
                'Accantona\Controller\Moviment' => array(
                    'account' => 'user',
                    'add' => 'user',
                    'moviment' => 'user',
                    'index' => 'user',
                    'edit' => 'user',
                    'delete' => 'user',
                ),
            ),
        ),
    ),
);