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
            'name' => 'valuta',
            'required' => true,
            'type' => 'Date',
            'options' => array(
                'label' => 'Descrizione',
            ),
        ));
        $this->add(array(
            'name' => 'importo',
            'required' => true,
            'type' => 'Number',
            'attributes' => array(
                'label' => 'Importo',
            ),
        ));
        $this->add(array(
            'filters'  => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'name' => 'descrizione',
            'required' => true,
            'type' => 'Text',
            'attributes' => array(
                'label' => 'Descrizione',
            ),
        ));
    }

}
