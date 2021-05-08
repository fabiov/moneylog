<?php

declare(strict_types=1);

namespace Auth\Form;

use Laminas\Form\Form;

class ForgottenPasswordForm extends Form
{
    public function __construct()
    {
        parent::__construct('forgotten-password');
        $this->setAttribute('method', 'post');

        $this->add([
            'attributes'    => ['class' => 'form-control', 'required' => true, 'type' => 'email'],
            'name'          => 'email',
            'options'       => ['label' => 'E-mail'],
        ]);

        $this->add([
            'name' => 'submit',
            'attributes' => [
                'class' => 'btn btn-lg btn-primary btn-block',
                'id'    => 'submitbutton',
                'type'  => 'submit',
                'value' => 'Invia',
            ],
        ]);
    }
}
