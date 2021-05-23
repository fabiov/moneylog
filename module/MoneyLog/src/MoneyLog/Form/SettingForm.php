<?php

declare(strict_types=1);

namespace MoneyLog\Form;

use Laminas\Filter\StringTrim;
use Laminas\Form\Form;

class SettingForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('settingForm');

        $this->add([
            'attributes' => ['max' => 28, 'min' => 0, 'step' => 1, 'value' => 27],
            'filters' => [['name' => StringTrim::class]],
            'name' => 'payday',
            'options' => ['label' => 'Giorno di paga'],
            'required' => true,
            'type' => 'Number',
        ]);
        $this->add([
            'attributes' => ['max' => 48, 'min' => 2, 'step' => 1, 'value' => 12],
            'filters' => [['name' => StringTrim::class]],
            'name' => 'months',
            'options' => ['label' => 'Mesi di retrospettiva'],
            'required' => true,
            'type' => 'Number',
        ]);
        $this->add([
            'filters' => [['name' => StringTrim::class]],
            'name' => 'provisioning',
            'options' => [
                'checked_value' => 1,
                'label' => 'Abilita accantonamento',
                'unchecked_value' => 0,
                'use_hidden_element' => true,
            ],
            'required' => true,
            'type' => 'Checkbox',
        ]);
    }
}
