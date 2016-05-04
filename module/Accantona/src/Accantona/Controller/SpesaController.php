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
                $spesa->userId = $this->getUser()->id;
                $this->getSpesaTable()->save($spesa);
                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_spesa');
            }
            Debug::dump($form->getMessages());
        }
        return array('form' => $form);
    }

    public function indexAction()
    {
        $sm = $this->getServiceLocator();
        $categoryTable = $sm->get('Accantona\Model\CategoriaTable');
        $user = $this->getUser();
        $where = array('spese.userId' => $user->id);

        if (($categoryId = (int) $this->params()->fromQuery('categoryIdFilter', 0)) != false) {
            $where[] = "categorie.id=$categoryId";
        }
        if (($months = (int) $this->params()->fromQuery('monthsFilter', 1)) != false) {
            $where[] = 'spese.valuta>"' . date('Y-m-d', strtotime("-$months month")) .'"';
        }

        return new ViewModel(array(
            'categoryId' => $categoryId,
            'months'     => $months,
            'rows'       => $this->getSpesaTable()->joinFetchAll($where),
            'categories' => $categoryTable->fetchAll(array('userId' => $user->id), 'descrizione')->toArray(),
            'avgPerCategory' => $this->getSpesaTable()->getAvgPerCategories($user->id),
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $spend = $em->find('Application\Entity\Spese', $id);
        if (!$spend) {
            return $this->redirect()->toRoute('accantona_spesa', array('action' => 'index'));
        }

        $form = new SpesaForm('spesa', array(), $em);
        $form->bind($spend);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($spend->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('accantona_spesa');
            }
        }

        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $spend = $this->getEntityManager()->find('Application\Entity\Spese', $id);
        if ($spend) {
            $this->getEntityManager()->remove($spend);
            $this->getEntityManager()->flush();
        }
        return $this->redirect()->toRoute('accantona_spesa');
    }

    public function getSpesaTable()
    {
        if (!$this->spesaTable) {
            $sm = $this->getServiceLocator();
            $this->spesaTable = $sm->get('Accantona\Model\SpesaTable');
        }
        return $this->spesaTable;
    }

    public function getUser()
    {
        return $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
    }

    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

}
