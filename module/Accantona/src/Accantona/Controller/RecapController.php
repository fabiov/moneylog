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

    public function indexAction()
    {
        return new ViewModel(array(
            'avgPerCategory' => $this->getSpesaTable()->getAvgPerCategories(),
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

}
