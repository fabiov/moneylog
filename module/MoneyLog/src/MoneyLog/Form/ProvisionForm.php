<?php

namespace MoneyLog\Form;

use Laminas\Form\Form;

class ProvisionForm extends Form
{
    public function __construct($name = 'accantona')
    {
        // we want to ignore the name passed
        parent::__construct($name);

        $this->add([
            'attributes' => ['class' => 'form-control', 'value' => date('Y-m-d')],
            'name' => 'date',
            'options' => ['label' => 'Data'],
            'required' => true,
            'type' => 'Date',
        ]);
        $this->add([
            'attributes' => [
                'class' => 'form-control text-right',
                'placeholder' => '0.00',
                'step' => 0.01
            ],
            'name' => 'amount',
            'options' => ['label' => 'Importo'],
            'required' => true,
            'type' => 'Number',
        ]);
        $this->add([
            'attributes' => ['class' => 'form-control', 'maxlength' => 255, 'placeholder' => 'Descrizione'],
            'filters'  => [['name' => 'Laminas\Filter\StringTrim']],
            'name' => 'description',
            'options' => ['label' => 'Descrizione'],
            'required' => true,
            'type' => 'Text',
        ]);
    }
}
