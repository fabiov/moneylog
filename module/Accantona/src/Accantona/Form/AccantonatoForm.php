<?php
namespace Accantona\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class AccantonatoForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('categoria');

        $this->add(array(
            'attributes' => array('class' => 'form-control', 'value' => date('Y-m-d')),
            'name' => 'valuta',
            'options' => array('label' => 'Valuta'),
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
            'name' => 'importo',
            'options' => array('label' => 'Importo'),
            'required' => true,
            'type' => 'Number',
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control', 'placeholder' => 'Descrizione'),
            'filters'  => array(array('name' => 'Zend\Filter\StringTrim')),
            'name' => 'descrizione',
            'options' => array('label' => 'Descrizione'),
            'required' => true,
            'type' => 'Text',
        ));
    }

}
