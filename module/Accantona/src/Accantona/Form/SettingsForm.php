<?php
namespace Accantona\Form;

use Zend\Form\Element;
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
                'label' => 'Salary day',
                'min'  => 1,
                'max'  => 28,
                'step' => 1,
                'class' => 'form-control',
                'value' => 27,
            ),
            'options' => array('label' => 'Salary day'),
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
