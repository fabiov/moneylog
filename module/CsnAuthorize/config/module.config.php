<?php
return array(
    'acl' => array(
        'roles' => array(
            'guest' => null,
            'user' => 'guest', // member estende guest
        ),
        'resources' => array(
            'allow' => array(
                'Accantona\Controller\Accantona' => array(
                    'index' => 'user',
                    'add' => 'user',
                ),
                'Accantona\Controller\Recap' => array(
                    'index' => 'user',
                    'add' => 'user',
                    'edit' => 'user',
                ),
                'Auth\Controller\Index' => array(
                    'index' => 'guest',
                    'login' => 'guest',
                ),
                'Accantona\Controller\Spesa' => array(
                    'add' => 'user',
                    'index' => 'user',
                ),
                'Accantona\Controller\Categoria' => array(
                    'add' => 'user',
                    'index' => 'user',
                ),
                'Accantona\Controller\Accantonato' => array(
                    'add' => 'user',
                    'index' => 'user',
                ),
            ),
        ),
    ),
);