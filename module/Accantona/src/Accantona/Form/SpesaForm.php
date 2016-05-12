<?php
namespace Accantona\Form;

use Zend\Form\Element;
use Zend\Form\Form;

class SpesaForm extends Form
{

    private $entityManager;
    private $userId;

    public function __construct($name = 'spesa', array $options, $entityManager, $userId)
    {
        // we want to ignore the name passed
        parent::__construct('spesa', $options = array());

        $this->entityManager = $entityManager;
        $this->userId = $userId;

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden',
        ));
//        $this->add(array(
//            'name' => 'userId',
//            'type' => 'Hidden',
//        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'name' => 'valuta',
            'required' => true,
            'type' => 'Date',
            'options' => array(
                'label' => 'Valuta',
            ),
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'name' => 'id_categoria',
            'options' => array(
                'label' => 'Categoria',
                'value_options' => $this->getCategoriesOptions(),
            ),
            'required' => true,
            'type' => 'Select',
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control', 'min'  => 0, 'step' => 0.01),
            'name' => 'importo',
            'required' => true,
            'type' => 'Number',
            'options' => array('label' => 'Importo'),
        ));
        $this->add(array(
            'attributes' => array('class' => 'form-control'),
            'filters'  => array(
                array('name' => 'Zend\Filter\StringTrim'),
            ),
            'name' => 'descrizione',
            'required' => true,
            'type' => 'Text',
            'options' => array(
                'label' => 'Descrizione',
            ),
        ));
    }

    private function getCategoriesOptions()
    {
        $rs = $this->entityManager->getRepository('Application\Entity\Category')->findBy(array('userId' => $this->userId));
        $options = array();
        foreach ($rs as $row) {
            $options[$row->id] = $row->descrizione;
        }
        return $options;
    }

}
