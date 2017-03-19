<?php
namespace Accantona\Form;

use Zend\Form\Form;

class SettingsForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('settingForm');

        $this->add(array(
            'filters'  => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'name' => 'payDay',
            'required' => true,
            'type' => 'Number',
            'attributes' => array(
                'class' => 'form-control',
                'label' => 'Salary day',
                'max'   => 28,
                'min'   => 0,
                'step'  => 1,
                'value' => 27,
            ),
            'options' => array('label' => 'Giorno di paga'),
        ));
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Save',
                'id' => 'submitbutton',
            ),
        ));
    }
}