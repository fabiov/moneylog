<?php

namespace MoneyLog\Form;

use Laminas\Form\Form;

class AccountForm extends Form
{
    /**
     * AccountForm constructor.
     * @param string $name
     */
    public function __construct($name = 'account')
    {
        parent::__construct($name);

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);
        $this->add([
            'attributes' => ['type' => 'text', 'class' => 'form-control'],
            'name' => 'name',
            'options' => ['label' => 'Name'],
            'type' => 'Text',
        ]);
        $this->add([
            'attributes' => [],
            'name' => 'recap',
            'options' => [
                'label' => 'Includi nel riepilogo',
                'checked_value' => 1,
                'unchecked_value' => 0,
                'use_hidden_element' => true,
            ],
            'type' => 'checkbox',
        ]);
        $this->add([
            'attributes' => [],
            'name' => 'closed',
            'options' => [
                'label' => 'Chiudi il conto',
                'checked_value' => 1,
                'unchecked_value' => 0,
                'use_hidden_element' => true,
            ],
            'type' => 'checkbox',
        ]);
        $this->add([
            'attributes' => ['class' => 'btn btn-primary', 'id' => 'submitbutton', 'value' => 'Salva'],
            'name' => 'submit',
            'type' => 'Submit',
        ]);
    }
}
