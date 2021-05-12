<?php

namespace MoneyLog\Form;

use Doctrine\ORM\EntityManager;
use Laminas\Form\Form;

class MovementForm extends Form
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
     * MovementForm constructor.
     * @param string $name
     * @param \Doctrine\ORM\EntityManager $em
     * @param $userId
     */
    public function __construct(string $name = 'movement', EntityManager $em, $userId)
    {
        parent::__construct($name);

        $this->em     = $em;
        $this->userId = $userId;

        $this->add([
            'name'     => 'type',
            'options'  => [
                'disable_inarray_validator' => false,
                'display_empty_item'        => false,
                'label'                     => 'Tipo',
                'property'                  => 'descrizione',
                'value_options'             => ['-1' => 'Uscita', '1' => 'Entrata'],
            ],
            'required' => true,
            'type'     => \Laminas\Form\Element\Select::class,
        ]);
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
            'filters'       => [['name' => 'Laminas\Filter\StringTrim']],
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
                    'params' => [
                        'criteria' => ['userId' => $userId, 'status' => \Application\Entity\Category::STATUS_ACTIVE],
                        'orderBy'  => ['descrizione' => 'ASC']
                    ]
                ],
                'label'                     => 'Categoria',
                'object_manager'            => $this->em,
                'target_class'              => \Application\Entity\Category::class,
                'property'                  => 'descrizione',
            ],
            'required' => false,
            'type'     => 'DoctrineModule\Form\Element\ObjectSelect',
        ]);
        $this->get('category')->setDisableInArrayValidator(true);
    }
}
