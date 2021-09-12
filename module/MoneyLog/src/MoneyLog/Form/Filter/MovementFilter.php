<?php

declare(strict_types=1);

namespace MoneyLog\Form\Filter;

use Laminas\Filter\StringTrim;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;

class MovementFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'filters' => [['name' => ToInt::class]],
            'name' => 'account',
            'required' => true,
        ]);
        $this->add([
            'filters'  => [],
            'name'     => 'date',
            'required' => true,
        ]);
        $this->add([
            'filters'  => [['name' => StringTrim::class]],
            'name'     => 'amount',
            'required' => true,
        ]);
        $this->add([
            'filters'  => [['name' => StringTrim::class]],
            'name'     => 'description',
            'required' => true,
        ]);
        $this->add([
            'filters'  => [['name' => ToInt::class]],
            'name'     => 'category',
            'required' => false,
        ]);
    }
}
