<?php

namespace Auth\Form;

use Laminas\Form\Form;

class AuthForm extends Form
{
    public function __construct()
    {
        parent::__construct('auth');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'email',
            'attributes' => ['type' => 'email', 'class' => 'form-control'],
            'options' => ['label' => 'Email'],
        ]);
        $this->add([
            'name' => 'password',
            'attributes' => ['type' => 'password', 'class' => 'form-control'],
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name' => 'rememberme',
            'type' => 'checkbox',
            'attributes' => [],
            'options' => ['label' => 'Remember me'],
        ]);
        $this->add([
            'name' => 'submit',
            'value' => 'Sign in',
            'attributes' => [
                'type'  => 'submit',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block',
                'value' => 'Sign in',
            ],
        ]);
    }
}
