<?php

declare(strict_types=1);

namespace MoneyLog\Form;

use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineModule\Form\Element\ObjectSelect;
use Laminas\Form\Element\Select;
use Laminas\Form\Form;

class MovementForm extends Form
{
    private EntityManagerInterface $em;

    public function __construct(string $name, EntityManagerInterface $em, int $userId)
    {
        parent::__construct($name);

        $this->em = $em;

        $this->add([
            'name' => 'type',
            'options' => [
                'disable_inarray_validator' => false,
                'display_empty_item' => false,
                'label' => 'Tipo',
                'value_options' => [Movement::OUT => 'Uscita', Movement::IN => 'Entrata'],
            ],
            'required' => true,
            'type' => Select::class,
        ]);
        $this->add([
            'attributes'    => ['class' => 'form-control', 'value' => date('Y-m-d')],
            'name'          => 'date',
            'options'       => ['label' => 'Data'],
            'required'      => true,
            'type'          => 'date',
        ]);
        $this->add([
            'attributes' => [
                'class' => 'form-control text-right', 'min' => 0.01, 'placeholder' => '0.00', 'step' => 0.01
            ],
            'name' => 'amount',
            'options' => ['label' => 'Importo'],
            'required' => true,
            'type' => 'number',
        ]);
        $this->add([
            'attributes'    => ['class' => 'form-control', 'placeholder' => 'Descrizione'],
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
                        'criteria' => ['user' => $userId, 'active' => true],
                        'orderBy'  => ['description' => 'ASC']
                    ]
                ],
                'label'                     => 'Categoria',
                'object_manager'            => $this->em,
                'target_class'              => Category::class,
                'property'                  => 'description',
            ],
            'required' => false,
            'type' => ObjectSelect::class,
        ]);
        $this->add([
            'name' => 'account',
            'options' => [
                'disable_inarray_validator' => true,
                'display_empty_item' => false,
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['user' => $userId, 'closed' => false],
                        'orderBy' => ['name' => 'ASC']
                    ]
                ],
                'label' => 'Conto',
                'object_manager' => $this->em,
                'target_class' => Account::class,
                'property' => 'name',
            ],
            'required' => true,
            'type' => ObjectSelect::class,
        ]);
        $this->get('category')->setDisableInArrayValidator(true);
    }
}
