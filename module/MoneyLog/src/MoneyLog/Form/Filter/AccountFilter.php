<?php

declare(strict_types=1);

namespace MoneyLog\Form\Filter;

use Laminas\Filter\StringTrim;
use Laminas\Filter\ToInt;
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
            'name' => 'recap',
            'required' => false,
            'filters' => [['name' => ToInt::class]],
        ]);
        // with following filter the validation fails
//            $this->inputFilter->add([
//                'name' => 'closed',
//                'required' => false,
//                'filters' => [['name' => Boolean::class]],
//            ]);
    }
}
