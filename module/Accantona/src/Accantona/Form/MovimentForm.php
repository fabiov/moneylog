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
                'step' => 0.01
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
            'name'     => 'category',
            'options'  => ['label' => 'Categoria', 'value_options' => $this->getCategoriesOptions()],
            'required' => false,
            'type'     => 'Select',
        ));
    }

    private function getCategoriesOptions()
    {
        $rs = $this->em->getRepository('Application\Entity\Category')
            ->findBy(['userId' => $this->userId], ['descrizione' => 'ASC']);
        $options = [0 => ''];
        foreach ($rs as $row) {
            $options[$row->id] = $row->descrizione;
        }
        return $options;
    }
}