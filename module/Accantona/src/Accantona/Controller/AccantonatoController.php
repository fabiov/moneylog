<?php
namespace Accantona\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Spesa;
use Accantona\Model\CategoriaTable;
use Accantona\Form\SpesaForm;
use Zend\Debug\Debug;

class AccantonatoController extends AbstractActionController
{

    protected $accantonatoTable;

    public function indexAction()
    {
//        $sm = $this->getServiceLocator();
//        $categoryTable = $sm->get('Accantona\Model\CategoriaTable');
//
//        $where = array();
//
//        if (($categoryId = (int)$this->params()->fromQuery('categoryIdFilter', 0)) != false) {
//            $where[] = "categorie.id=$categoryId";
//        }
        if (($months = (int)$this->params()->fromQuery('monthsFilter', 1)) != false) {
//            $where[] = 'spese.valuta>"' . date('Y-m-d', strtotime("-$months month")) . '"';
        }
//
        return new ViewModel(array(
//            'categoryId' => $categoryId,
            'months' => $months,
//            'rows' => $this->getSpesaTable()->joinFetchAll($where),
//            'categories' => $categoryTable->fetchAll(array(), 'descrizione')->toArray(),
//            'avgPerCategory' => $this->getSpesaTable()->getAvgPerCategories(),
        ));
    }

}