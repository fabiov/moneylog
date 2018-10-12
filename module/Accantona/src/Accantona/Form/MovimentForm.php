<?php

namespace Accantona\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;

class MovimentForm extends Form
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var int
     */
    private $userId;

    /**
     * MovimentForm constructor.
     * @param string $name
     * @param EntityManager $em
     */
    public function __construct($name = 'moviment', EntityManager $em, $userId)
    {
        parent::__construct($name);

        $this->em     = $em;
        $this->userId = $userId;

        $this->add([
            'attributes'    => ['class' => 'form-control', 'value' => date('Y-m-d')],
            'name'          => 'date',
            'options'       => ['label' => 'Data'],
            'required'      => true,
            'type'          => 'date',
        ]);
        $this->add([
            'attributes'    => ['class' => 'form-control text-right', 'min' => 0.01, 'placeholder' => '0.00', 'step' => 0.01],
            'name'          => 'amount',
            'options'       => ['label' => 'Importo'],
            'required'      => true,
            'type'          => 'number',
        ]);
        $this->add([
            'attributes'    => ['class' => 'form-control', 'placeholder' => 'Descrizione'],
            'filters'       => [['name' => 'Zend\Filter\StringTrim']],
            'name'          => 'description',
            'options'       => ['label' => 'Descrizione'],
            'required'      => true,
            'type'          => 'text',
        ]);
        $this->add([
            'name'     => 'category',
            'options'  => [
                'disable_inarray_validator' => true,
                'display_empty_item'        => true,
                'find_method'               => [
                    'name'   => 'findBy',
                    'params' => ['criteria' => ['userId' => $userId], 'orderBy'  => ['descrizione' => 'ASC']]
                ],
                'label'                     => 'Categoria',
                'object_manager'            => $this->em,
                'target_class'              => 'Application\Entity\Category',
                'property'                  => 'descrizione',
            ],
            'required' => false,
            'type'     => 'DoctrineModule\Form\Element\ObjectSelect',
        ]);
        $this->get('category')->setDisableInArrayValidator(true);
    }
}