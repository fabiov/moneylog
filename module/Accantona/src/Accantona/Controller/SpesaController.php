<?php

namespace Accantona\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Spesa;
use Accantona\Model\CategoriaTable;
//use Accantona\Form\AnagraficaForm;

class SpesaController extends AbstractActionController
{

    protected $spesaTable;

    public function addAction()
    {
    }

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $categoryTable = $sm->get('Accantona\Model\CategoriaTable');

        $where = array();

        if (($categoryId = (int) $this->params()->fromQuery('categoryId', 0)) != false) {
            $where[] = "categorie.id=$categoryId";
        }
        if (($months = (int) $this->params()->fromQuery('months', 1)) != false) {
            $where[] = 'spese.valuta>"' . date('Y-m-d', strtotime("-$months month")) .'"';
        }

        return new ViewModel(array(
            'categoryId' => $categoryId,
            'months'     => $months,
            'rows'       => $this->getSpesaTable()->joinFetchAll($where),
            'categories' => $categoryTable->fetchAll(array(), 'descrizione')->toArray(),
        ));
    }

    public function editAction()
    {
    }

    public function deleteAction()
    {
    }

    public function getSpesaTable()
    {
        if (!$this->spesaTable) {
            $sm = $this->getServiceLocator();
            $this->spesaTable = $sm->get('Accantona\Model\SpesaTable');
        }
        return $this->spesaTable;
    }

}
