<?php

namespace Auth\Form;

use Laminas\InputFilter\InputFilter;

class ForgottenPasswordFilter extends InputFilter
{
    public function __construct($sm)
    {
        $this->add([
            'name'          => 'email',
            'required'      => true,
            'validators'    => [
                ['name' => 'EmailAddress'],
                [
                    'name'      => 'Laminas\Validator\Db\RecordExists',
                    'options'   => [
                        'adapter' => $sm->get('Laminas\Db\Adapter\Adapter'), 'field' => 'email', 'table' => 'user'
                    ],
                ],
            ],
        ]);
    }
}
