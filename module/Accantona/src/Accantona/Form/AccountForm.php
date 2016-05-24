<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Accantona\Form;

use Zend\Form\Form;

class AccountForm extends Form
{

    /**
     * AccountForm constructor.
     * @param string $name
     */
    public function __construct($name = 'account')
    {
        parent::__construct($name);

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden'
        ));
        $this->add(array(
            'attributes' => array('type' => 'text', 'class' => 'form-control'),
            'name' => 'name',
            'options' => array('label' => 'Name'),
            'type' => 'Text',
        ));
        $this->add(array(
            'attributes' => array('class' => 'btn btn-primary', 'id' => 'submitbutton', 'value' => 'Salva'),
            'name' => 'submit',
            'type' => 'Submit',
        ));
    }

}
