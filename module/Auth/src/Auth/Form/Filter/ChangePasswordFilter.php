<?php
namespace Auth\Form\Filter;

use Zend\InputFilter\InputFilter;
use Zend\Validator\Regex;

/**
 * Class ChangePasswordFilter
 * @package Auth\Form\Filter
 */
class ChangePasswordFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'filters'  => [['name' => 'StringTrim']],
            'name'     => 'current',
            'required' => true,
        ]);
        $this->add([
            'filters'    => array(array('name' => 'StringTrim')),
            'name'       => 'password',
            'required'   => true,
            'validators' => [[
                'name'    => 'Regex',
                'options' => [
                    'messages' => [
                        Regex::INVALID => 'Minimo 8 caratteri ed almeno 1 lettera maiuscola, 1 una minuscola ed 1 numero',
                    ],
                    'pattern'  => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
                ],
            ]],
        ]);
        $this->add([
            'filters'    => [['name' => 'StringTrim']],
            'name'       => 'password_confirm',
            'required'   => true,
            'validators' => [['name' => 'Identical', 'options' => ['token' => 'password']]],
        ]);
    }
}