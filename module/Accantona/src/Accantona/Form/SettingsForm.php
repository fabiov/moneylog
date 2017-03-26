<?php
namespace Accantona\Form;

use Zend\Form\Form;

class SettingsForm extends Form
{

    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('settingForm');

        $this->add([
            'attributes' => ['class' => 'form-control', 'max' => 28, 'min' => 0, 'step' => 1, 'value' => 27],
            'filters'    => [['name' => 'Zend\Filter\StringTrim']],
            'name'       => 'payDay',
            'options'    => ['label' => 'Giorno di paga'],
            'required'   => true,
            'type'       => 'Number',
        ]);
        $this->add([
            'attributes' => ['class' => 'form-control', 'max'   => 48, 'min'   => 2, 'step'  => 1, 'value' => 12],
            'filters'    => [['name' => 'Zend\Filter\StringTrim']],
            'name'       => 'monthsRetrospective',
            'options'    => ['label' => 'Mesi di retrospettiva'],
            'required'   => true,
            'type'       => 'Number',
        ]);
    }
}