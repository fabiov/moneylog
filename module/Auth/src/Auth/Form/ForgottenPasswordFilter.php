<?php

namespace Auth\Form;

use Laminas\Db\Adapter\Adapter;
use Laminas\InputFilter\InputFilter;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\Db\RecordExists;

class ForgottenPasswordFilter extends InputFilter
{
    public function __construct(ServiceManager $sm)
    {
        $this->add([
            'name'          => 'email',
            'required'      => true,
            'validators'    => [
                ['name' => 'EmailAddress'],
                [
                    'name' => RecordExists::class,
                    'options' => ['adapter' => $sm->get(Adapter::class), 'field' => 'email', 'table' => 'user'],
                ],
            ],
        ]);
    }
}
