<?php

namespace Accantona\Controller;

use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Categoria;
use Accantona\Form\CategoriaForm;

class CategoriaController extends AbstractActionController
{

    protected $categoriaTable;

    public function addAction()
    {
        $form = new CategoriaForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $category = new Categoria();
            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['userId'] = $this->getUser()->id;
                $category->exchangeArray($data);
                $this->getCategoriaTable()->save($category);

                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_categoria');
            }
        }
        return array('form' => $form);
    }

    public function indexAction()
    {
        return new ViewModel(array(
            'rows' => $this->getCategoriaTable()->fetchAll(array('userId' => $this->getUser()->id)),
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $user = $this->getUser();

        $category = $this->getEntityManager()->getRepository('Application\Entity\Category')
            ->findOneBy(array('id' => $id, 'userId' => $user->id));
        if (!$category) {
            return $this->redirect()->toRoute('accantona_categoria', array('action' => 'index'));
        }

        $form = new CategoriaForm();
        $form->bind($category);

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('accantona_categoria');
            }
        }
        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('accantona_categoria');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $this->getCategoriaTable()->deleteByAttributes(array('id' => $id, 'userId' => $this->getUser()->id));
            }

            // Redirect to list of categories
            return $this->redirect()->toRoute('accantona_categoria');
        }

        return array(
            'id' => $id,
            'category' => $this->getCategoriaTable()->getCategoria($id)
        );
    }

    public function getCategoriaTable()
    {
        if (!$this->categoriaTable) {
            $sm = $this->getServiceLocator();
            $this->categoriaTable = $sm->get('Accantona\Model\CategoriaTable');
        }
        return $this->categoriaTable;
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
