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
    protected $accantonatoTable;

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $spesaTable = $this->getSpesaTable();
        $avgPerCategory = $spesaTable->getAvgPerCategories();
        usort($avgPerCategory, function ($a, $b) {
            return $a['average'] == $b['average'] ? 0 : ($a['average'] < $b['average'] ? 1 : -1);
        });

        $variables = array();
        foreach ($this->getVariabileTable()->fetchAll() as $variable) {
            $variables[$variable->nome] = $variable->valore;
        }

        $payDay = 4;
        $currentDay = date('j');
        return new ViewModel(array(
            'avgPerCategory' => $avgPerCategory,
            'variables' => $variables,
            'stored' => $this->getAccantonatoTable()->getSum() - $spesaTable->getSum(),
            'remainingDays' => $currentDay < $payDay ? $payDay - $currentDay : date('t') - $currentDay + $payDay,
        ));
    }

    public function editAction()
    {

    }

    /**
     * @return Accantona\Model\SpesaTable
     */
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

    public function getAccantonatoTable()
    {
        if (!$this->accantonatoTable) {
            $sm = $this->getServiceLocator();
            $this->accantonatoTable = $sm->get('Accantona\Model\AccantonatoTable');
        }
        return $this->accantonatoTable;
    }

}
