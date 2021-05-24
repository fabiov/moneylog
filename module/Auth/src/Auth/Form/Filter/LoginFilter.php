<?php

namespace Auth\Form\Filter;

use Laminas\InputFilter\InputFilter;

class LoginFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'email',
            'required' => true,
            'filters' => [],
            'validators' => [['name' => 'StringLength', 'options' => ['encoding' => 'UTF-8', 'min' => 1]]],
        ]);

        $this->add([
            'name' => 'password',
            'required' => true,
            'filters' => [],
            'validators' => [['name' => 'StringLength', 'options' => ['encoding' => 'UTF-8', 'min' => 1]]],
        ]);
    }
}
