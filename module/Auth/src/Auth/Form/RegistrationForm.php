<?php

namespace Auth\Form;

use Laminas\Form\Form;

class RegistrationForm extends Form
{
    public function __construct()
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');

        $this->add([
            'name' => 'name',
            'attributes' => ['class' => 'form-control', 'required' => true, 'type' => 'text'],
            'options' => ['label' => 'Nome'],
        ]);

        $this->add([
            'name' => 'surname',
            'attributes' => ['class' => 'form-control', 'required' => true, 'type' => 'text'],
            'options' => ['label' => 'Cognome'],
        ]);

        $this->add([
            'name' => 'email',
            'attributes' => [
                'type' => 'email',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'E-mail',
            ],
        ]);

        $this->add([
            'name' => 'password',
            'attributes' => [
                'type' => 'password',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Password',
            ],
        ]);

        $this->add([
            'name' => 'password_confirm',
            'attributes' => [
                'type' => 'password',
                'class' => 'form-control',
            ],
            'options' => [
                'label' => 'Confirm Password',
            ],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'type' => 'submit',
                'value' => 'Registra',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block',
            ],
        ]);
    }
}
