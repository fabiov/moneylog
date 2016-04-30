<?php

namespace Accantona\Controller;

use Accantona\Form\AccountForm;
use Application\Entity\Account;
use Zend\Captcha\Dumb;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Categoria;
use Accantona\Form\CategoriaForm;

class AccountController extends AbstractActionController
{

    public function addAction()
    {
        $form = new AccountForm();

        $request = $this->getRequest();
        if ($request->isPost()) {

            $account = new Account();
            $form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['userId'] = $this->getUser()->id;
                $account->exchangeArray($data);
                $this->getEntityManager()->persist($account);
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('accantonaAccount');
            }
        }
        return array('form' => $form);
    }

    public function indexAction()
    {
        $em = $this->getEntityManager();
        return new ViewModel(array(
//            'rows' => $this->getCategoriaTable()->fetchAll(array('userId' => $this->getUser()->id)),
            'rows' => $em->getRepository('Application\Entity\Account')->findAll(array('userId' => $this->getUser()->id)),
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

        $user = $this->getUser();
        $form = new CategoriaForm();
        $form->bind($categoria);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost() && $user->id == $categoria->userId) {

            $form->setInputFilter($categoria->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $categoria->userId = $user->id;
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
