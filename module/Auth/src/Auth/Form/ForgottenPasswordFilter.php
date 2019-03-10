<?php
namespace Auth\Form;

use Zend\InputFilter\InputFilter;

class ForgottenPasswordFilter extends InputFilter
{
    public function __construct($sm)
    {
        $this->add(array(
            'name'          => 'email',
            'required'      => true,
            'validators'    => array(
                array('name' => 'EmailAddress'),
                array(
                    'name'      => 'Zend\Validator\Db\RecordExists',
                    'options'   => [
                        'adapter' => $sm->get('Zend\Db\Adapter\Adapter'), 'field' => 'email', 'table' => 'user'
                    ],
                ),
            ),
        ));
    }
}