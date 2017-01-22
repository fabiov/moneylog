<?php
namespace Auth\Form;

use Zend\Form\Form;

class RegistrationForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');

        $this->add(array(
            'name' => 'name',
            'attributes' => array('class' => 'form-control', 'required' => true, 'type' => 'text'),
            'options' => array('label' => 'Nome'),
        ));

        $this->add(array(
            'name' => 'surname',
            'attributes' => array('class' => 'form-control', 'required' => true, 'type' => 'text'),
            'options' => array('label' => 'Cognome'),
        ));

        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type' => 'email',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'E-mail',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Password',
            ),
        ));

        $this->add(array(
            'name' => 'password_confirm',
            'attributes' => array(
                'type' => 'password',
                'class' => 'form-control',
            ),
            'options' => array(
                'label' => 'Confirm Password',
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Captcha',
            'name' => 'captcha',
            'options' => array(
                'label' => 'Please verify you are human',
                'captcha' => new \Zend\Captcha\ReCaptcha(array(
                    'pubKey'    => '6LdjpBIUAAAAAGDwoUOVWGSZSudz1U2EYWDOafD7',
                    'privKey'   => '6LdjpBIUAAAAANHJptlPZIB5Ty3kXKPS6tmSbc1j',
                )),
                'class' => 'form-control',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Registra',
                'id' => 'submitbutton',
                'class' => 'btn btn-lg btn-primary btn-block',
            ),
        ));
    }
}