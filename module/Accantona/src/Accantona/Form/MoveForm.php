<?php

namespace Accantona\Form;

use Zend\Form\Form;

class MoveForm extends Form
{

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);

        $this->add(array(
            'attributes' => array('class' => 'form-control', 'value' => date('Y-m-d')),
            'name' => 'date',
            'options' => array('label' => 'Data'),
            'required' => true,
            'type' => 'Date',
        ));
        $this->add(array(
            'attributes' => array(
                'class' => 'form-control text-right',
                'placeholder' => '0.00',
                'step' => 0.01,
                'min' => 0.01,
            ),
            'name' => 'amount',
            'options' => array('label' => 'Importo'),
            'required' => true,
            'type' => 'Number',
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control', 'placeholder' => 'Descrizione'),
            'filters'  => array(array('name' => 'Zend\Filter\StringTrim')),
            'name' => 'description',
            'options' => array('label' => 'Descrizione'),
            'required' => true,
            'type' => 'Text',
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'name' => 'targetAccountId',
            'options' => array(
                'label' => 'Conto di destinazione',
//                'value_options' => array(),
            ),
            'required' => true,
            'type' => 'Select',
        ));
//        $this->add(
//            array(
//                'type' => 'DoctrineModule\Form\Element\ObjectSelect',
//                'name' => 'targetAccountId',
//                'options' => array(
//                    'label'          => 'Descrizione',
//                    'object_manager' => $this->getObjectManager(),
//                    'target_class'   => 'Application\Entity\Account',
//                    'property'       => 'name',
//                    'is_method'      => true,
//                    'find_method'    => array(
//                        'name'   => 'findBy',
//                        'params' => array(
//                            'criteria' => array('recap' => 1),
//
//                            // Use key 'orderBy' if using ORM
//                            'orderBy'  => array('name' => 'ASC'),
//
//                            // Use key 'sort' if using ODM
////                            'sort'  => array('lastname' => 'ASC')
//                        ),
//                    ),
//                ),
//            )
//        );
    }

    public function setAccountOptions($accounts)
    {
        $this->get('targetAccountId')->setValueOptions($accounts);
        return $this;
    }

}