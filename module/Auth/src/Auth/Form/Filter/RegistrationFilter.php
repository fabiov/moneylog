<?php

namespace Auth\Form\Filter;

use Laminas\Db\Adapter\Adapter;
use Laminas\InputFilter\InputFilter;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Validator\Db\NoRecordExists;

class RegistrationFilter extends InputFilter
{
    public function __construct(ServiceManager $sm)
    {
        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                ['name' => 'EmailAddress'],
                [
                    'name' => NoRecordExists::class,
                    'options' => ['table' => 'user', 'field' => 'email', 'adapter' => $sm->get(Adapter::class)],
                ],
            ],
        ]);

        $this->add([
            'name' => 'password',
            'required' => true,
            'filters' => [['name' => 'StringTrim']],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['encoding' => 'UTF-8', 'min' => 6, 'max' => 12]],
            ],
        ]);

        $this->add([
            'name' => 'password_confirm',
            'required' => true,
            'filters' => [['name' => 'StringTrim']],
            'validators' => [
                ['name' => 'StringLength', 'options' => ['encoding' => 'UTF-8', 'min' => 6, 'max' => 12]],
                ['name' => 'Identical', 'options' => ['token' => 'password']],
            ],
        ]);
    }
}
