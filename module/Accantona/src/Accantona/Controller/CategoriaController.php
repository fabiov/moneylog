<?php

namespace Accantona\Controller;

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
                $category->exchangeArray($form->getData());
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
            'rows' => $this->getCategoriaTable()->fetchAll(),
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('accantona_categoria', array('action' => 'add'));
        }

        try {
            $categoria = $this->getCategoriaTable()->getCategoria($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('accantona_categoria', array('action' => 'index'));
        }

        $form = new CategoriaForm();
        $form->bind($categoria);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($categoria->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $this->getCategoriaTable()->save($categoria);

                // Redirect to list
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
                $id = (int) $request->getPost('id');
                $this->getCategoriaTable()->delete($id);
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

}
