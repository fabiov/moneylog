<?php
namespace Auth\Form;

use Zend\Form\Form;

class ForgottenPasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('forgotten-password');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'attributes'    => array('class' => 'form-control', 'required' => true, 'type' => 'email'),
            'name'          => 'email',
            'options'       => array('label' => 'E-mail'),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'class' => 'btn btn-lg btn-primary btn-block',
                'id'    => 'submitbutton',
                'type'  => 'submit',
                'value' => 'Invia',
            ),
        )); 
    }
}