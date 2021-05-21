<?php

declare(strict_types=1);

namespace MoneyLog\Form;

use Laminas\Form\Form;

class CategoriaForm extends Form
{
    public function __construct()
    {
        // we want to ignore the name passed
        parent::__construct('categoria');

        $this->add([
            'name' => 'id',
            'type' => 'Hidden',
        ]);
        $this->add([
            'attributes' => ['class' => 'form-control'],
            'name' => 'description',
            'type' => 'Text',
            'options' => ['label' => 'Descrizione'],
        ]);
        $this->add([
            'attributes' => ['value' => 1, 'id' => 'categotyStatus'],
            'name' => 'status',
            'options' => [
                'label' => 'Attivo',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ],
            'type' => 'Checkbox',
        ]);
        $this->add([
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => [
                'value' => 'Go',
                'id' => 'submitbutton',
            ],
        ]);
    }
}
