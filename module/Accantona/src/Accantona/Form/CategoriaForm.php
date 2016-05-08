<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 *
 * @author fabio.ventura
 */

namespace Accantona\Form;

use Zend\Form\Form;

class CategoriaForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('categoria');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'name' => 'descrizione',
            'type' => 'Text',
            'options' => array('label' => 'Descrizione'),
        ));
        $this->add(array(
            'attributes' => array('value' => 1),
            'name' => 'status',
            'options' => array(
                'label' => 'Attivo',
                'use_hidden_element' => true,
                'checked_value' => 1,
                'unchecked_value' => 0,
            ),
            'type' => 'Checkbox',
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }

}
