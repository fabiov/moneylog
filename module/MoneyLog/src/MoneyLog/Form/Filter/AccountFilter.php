<?php

declare(strict_types=1);

namespace MoneyLog\Form\Filter;

use Laminas\Filter\StringTrim;
use Laminas\InputFilter\InputFilter;

class AccountFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'name',
            'required' => true,
            'filters' => [['name' => StringTrim::class]],
        ]);
        $this->add([
            'name' => 'status',
            'required' => false,
        ]);
    }
}
