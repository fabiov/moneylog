<?php

namespace Auth\Form;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Form;

class AuthForm extends Form
{
    public function __construct()
    {
        parent::__construct('auth');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'email',
            'type' => Email::class,
        ]);
        $this->add([
            'name' => 'password',
            'type' => Password::class,
        ]);
        $this->add([
            'name' => 'rememberMe',
            'required' => false,
            'options' => ['checked_value' => '0', 'unchecked_value' => '1'],
            'type' => Checkbox::class,
        ]);
    }
}
