<?php
declare(strict_types=1);

namespace Auth\Form;

use Laminas\Form\Form;

/**
 * ATTENZIONE: filtri e forn devono avere esattamente gli stessi campi
 *
 * Class UserForm
 * @package Auth\Form
 */
class UserForm extends Form
{
    public function __construct()
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');

        $this
            ->add([
                'attributes' => ['id' => 'name', 'maxlength' => 50, 'required' => true, 'type'  => 'text'],
                'name'       => 'name',
                'options'    => ['label' => 'Nome'],
            ])
            ->add([
                'attributes' => ['id' => 'surname', 'maxlength' => 128, 'required' => true, 'type' => 'text'],
                'name'       => 'surname',
                'options'    => ['label' => 'Cognome'],
            ])
        ;
    }
}
