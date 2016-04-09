<?php
namespace Auth\Form;

use Zend\Form\Form;

class AuthForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('auth');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'email',
            'attributes' => array('type' => 'email', 'class' => 'form-control'),
            'options' => array('label' => 'Email'),
        ));
        $this->add(array(
            'name' => 'password',
            'attributes' => array('type' => 'password', 'class' => 'form-control'),
            'options' => array(
                'label' => 'Password',
            ),
        ));
        $this->add(array(
            'name' => 'rememberme',
            'type' => 'checkbox',
            'attributes' => array(),
            'options' => array('label' => 'Remember me'),
        ));
        $this->add(array(
            'name' => 'submit',
            'value' => 'Sign in',
            'attributes' => array(
                'type'  => 'submit',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block',
                'value' => 'Sign in',
            ),
        )); 
    }
}