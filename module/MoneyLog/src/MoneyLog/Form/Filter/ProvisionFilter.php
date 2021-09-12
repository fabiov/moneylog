<?php

declare(strict_types=1);

namespace MoneyLog\Form\Filter;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\InputFilter;

class ProvisionFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'date',
            'required' => true,
            'filters' => [['name' => StringTrim::class]],
        ]);
        $this->add([
            'name' => 'amount',
            'required' => true,
            'filters' => [['name' => StringTrim::class]],
        ]);
        $this->add([
            'name' => 'description',
            'required' => true,
            'filters' => [['name' => StringTrim::class]],
        ]);
    }
}
