<?php

namespace Accantona\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Spesa;
use Accantona\Model\CategoriaTable;
use Accantona\Form\SpesaForm;
use Zend\Debug\Debug;

class RecapController extends AbstractActionController
{

    protected $spesaTable;
    protected $variabileTable;


    public function indexAction()
    {
        $avgPerCategory = $this->getSpesaTable()->getAvgPerCategories();
        usort($avgPerCategory, function ($a, $b) {
            return $a['average'] == $b['average'] ? 0 : ($a['average'] < $b['average'] ? 1 : -1);
        });

        return new ViewModel(array(
            'avgPerCategory' => $avgPerCategory,
            'variabili' => $this->getVariabileTable()->fetchAll(),
        ));
    }

    public function getSpesaTable()
    {
        if (!$this->spesaTable) {
            $sm = $this->getServiceLocator();
            $this->spesaTable = $sm->get('Accantona\Model\SpesaTable');
        }
        return $this->spesaTable;
    }

    public function getVariabileTable()
    {
        if (!$this->variabileTable) {
            $sm = $this->getServiceLocator();
            $this->variabileTable = $sm->get('Accantona\Model\VariabileTable');
        }
        return $this->variabileTable;
    }

}
