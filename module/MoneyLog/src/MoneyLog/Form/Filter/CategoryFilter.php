<?php

declare(strict_types=1);

namespace MoneyLog\Form\Filter;

use Laminas\Filter\StringTrim;
use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;

class CategoryFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'description',
            'required' => true,
            'filters'  => [['name' => StringTrim::class]],
        ]);
        $this->add([
            'name' => 'active',
            'filters'  => [['name' => ToInt::class]],
        ]);
    }
}
