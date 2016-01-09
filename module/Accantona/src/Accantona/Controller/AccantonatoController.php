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
//        $where = array();
        if (($months = (int)$this->params()->fromQuery('monthsFilter', 1)) != false) {
            $where[] = 'spese.valuta>"' . date('Y-m-d', strtotime("-$months month")) . '"';
        }
//
        return new ViewModel(array(
            'months' => $months,
            'rows' => $this->getAccantonatoTable()->fetchAll($where),
        ));
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