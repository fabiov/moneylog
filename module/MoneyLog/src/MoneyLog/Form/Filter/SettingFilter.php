<?php

declare(strict_types=1);

namespace MoneyLog\Form\Filter;

use Laminas\Filter\ToInt;
use Laminas\InputFilter\InputFilter;

class SettingFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'filters' => [['name' => ToInt::class]],
            'name' => 'payday',
            'required' => true,
        ]);
        $this->add([
            'filters' => [['name' => ToInt::class]],
            'name' => 'months',
            'required' => true,
        ]);
        $this->add([
            'filters' => [['name' => ToInt::class]],
            'name' => 'provisioning',
            'required' => true,
        ]);
    }
}
