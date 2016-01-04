<?php

namespace Accantona\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Spesa;
use Accantona\Model\CategoriaTable;
use Accantona\Form\SpesaForm;
use Zend\Debug\Debug;

class SpesaController extends AbstractActionController
{

    protected $spesaTable;

    public function addAction()
    {
        $form = new SpesaForm();

        $request = $this->getRequest();
        if ($request->isPost()) {

            $spesa = new Spesa();
            $form->setInputFilter($spesa->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $spesa->exchangeArray($form->getData());
                $this->getSpesaTable()->save($spesa);
                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_spesa');
            }
            Debug::dump($form->getMessages());
            echo 'NO';die;
        }
        return array('form' => $form);
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
