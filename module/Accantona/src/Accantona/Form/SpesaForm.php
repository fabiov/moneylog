<?php
namespace Accantona\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class SpesaForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('spesa');

        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'name' => 'id_categoria',
            'options' => array(
                'label' => 'Categoria',
                'value_options' => array(
                    '0' => 'French',
                    '1' => 'English',
                    '2' => 'Japanese',
                    '3' => 'Chinese',
                ),
            ),
            'required' => true,
            'type' => 'Select',
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'name' => 'valuta',
            'required' => true,
            'type' => 'Date',
            'options' => array(
                'label' => 'Descrizione',
            ),
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control', 'min'  => 0, 'step' => 0.01),
            'name' => 'importo',
            'required' => true,
            'type' => 'Number',
            'options' => array('label' => 'Importo'),
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'filters'  => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'name' => 'descrizione',
            'required' => true,
            'type' => 'Text',
            'options' => array(
                'label' => 'Descrizione',
            ),
        ));
    }

    

}
