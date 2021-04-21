<?php
namespace MoneyLog\Form\Filter;

use Zend\InputFilter\InputFilter;

class MovementFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'filters'  => [],
            'name'     => 'date',
            'required' => true,
        ]);
        $this->add([
            'filters'  => [['name' => 'StringTrim']],
            'name'     => 'amount',
            'required' => true,
        ]);
        $this->add([
            'filters'  => [['name' => 'StringTrim']],
            'name'     => 'description',
            'required' => true,
        ]);
        $this->add([
            'filters'  => [['name' => 'Int']],
            'name'     => 'category',
            'required' => false,
        ]);
    }
}