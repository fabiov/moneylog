<?php

namespace MoneyLog\Form;

use Laminas\Form\Form;

class MoveForm extends Form
{
    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $this->add([
            'attributes'    => ['class' => 'form-control', 'value' => date('Y-m-d')],
            'name'          => 'date',
            'options'       => ['label' => 'Data'],
            'required'      => true,
            'type'          => 'Date',
        ]);
        $this->add([
            'attributes'    => ['class' => 'form-control text-right', 'placeholder' => '0.00', 'step' => 0.01, 'min' => 0.01],
            'name'          => 'amount',
            'options'       => ['label' => 'Importo'],
            'required'      => true,
            'type'          => 'Number',
        ]);
        $this->add([
            'attributes'    => ['class' => 'form-control', 'placeholder' => 'Descrizione'],
            'filters'       => [['name' => 'Laminas\Filter\StringTrim']],
            'name'          => 'description',
            'options'       => ['label' => 'Descrizione'],
            'required'      => true,
            'type'          => 'Text',
        ]);
        $this->add([
            'attributes'    => ['class' => 'form-control'],
            'name'          => 'targetAccountId',
            'options'       => ['label' => 'Conto di destinazione'],
            'required'      => true,
            'type'          => 'Select',
        ]);
    }

    public function setAccountOptions($accounts)
    {
        $this->get('targetAccountId')->setValueOptions($accounts);
        return $this;
    }
}
