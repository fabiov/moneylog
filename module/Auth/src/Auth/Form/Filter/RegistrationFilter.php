<?php
namespace Auth\Form\Filter;

use Laminas\InputFilter\InputFilter;

class RegistrationFilter extends InputFilter
{
    public function __construct($sm)
    {
        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                ['name' => 'EmailAddress'],
                [
                    'name' => 'Laminas\Validator\Db\NoRecordExists',
                    'options' => [
                        'table' => 'user', 'field' => 'email', 'adapter' => $sm->get('Laminas\Db\Adapter\Adapter')
                    ],
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