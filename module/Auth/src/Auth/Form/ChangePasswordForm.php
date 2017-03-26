<?php
namespace Auth\Form;

use Zend\Form\Form;

/**
 * ATTENZIONE: filtri e forn devono avere esattamente gli stessi campi
 *
 * Class ChangePasswordForm
 * @package Auth\Form
 */
class ChangePasswordForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');

        $this
            ->add([
                'attributes' => ['id' => 'current', 'maxlength' => 16, 'required' => true, 'type'  => 'password'],
                'name'       => 'current',
                'options'    => ['label' => 'Password corrente'],
            ])
            ->add([
                'attributes' => ['id' => 'password_confirm', 'maxlength' => 16, 'required' => true, 'type' => 'password'],
                'name'       => 'password',
                'options'    => ['label' => 'Nuova password'],
            ])
            ->add([
                'attributes' => ['id' => 'password_confirm', 'maxlength' => 16, 'required' => true, 'type' => 'password'],
                'name'       => 'password_confirm',
                'options'    => ['label' => 'Conferma nuova password'],
            ])
        ;
    }
}