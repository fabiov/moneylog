<?php

namespace Accantona\Form;

use Zend\Form\Form;

class MovimentForm extends Form
{

    public function __construct($name = 'moviment')
    {
        parent::__construct($name);

        $this->add(array(
            'attributes' => array('class' => 'form-control', 'value' => date('Y-m-d')),
            'name' => 'date',
            'options' => array('label' => 'Data'),
            'required' => true,
            'type' => 'Date',
        ));
        $this->add(array(
            'attributes' => array(
                'class' => 'form-control text-right',
                'min' => 0.01,
                'placeholder' => '0.00',
                'step' => 0.01
            ),
            'name' => 'amount',
            'options' => array('label' => 'Importo'),
            'required' => true,
            'type' => 'Number',
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control', 'placeholder' => 'Descrizione'),
            'filters'  => array(array('name' => 'Zend\Filter\StringTrim')),
            'name' => 'description',
            'options' => array('label' => 'Descrizione'),
            'required' => true,
            'type' => 'Text',
        ));
    }

}