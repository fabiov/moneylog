<?php

namespace Accantona\Controller;

use Accantona\Form\AccountForm;
use Application\Entity\Account;
use Application\Entity\Moviment;
use Zend\Captcha\Dumb;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Categoria;
use Accantona\Form\CategoriaForm;
use Accantona\Form\MovimentForm;

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
            'rows' => $em->getRepository('Application\Entity\Account')->findBy(array('userId' => $this->getUser()->id)),
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $em = $this->getEntityManager();
        $account = $em->getRepository('Application\Entity\Account')
            ->findOneBy(array('id' => $id, 'userId' => $this->getUser()->id));

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $form = new AccountForm();
        $form->bind($account);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $em->flush();
                return $this->redirect()->toRoute('accantonaAccount'); // Redirect to list
            }
        }

        return array('id' => $id, 'form' => $form);
    }

//    public function deleteAction()
//    {
//        $id = (int) $this->params()->fromRoute('id', 0);
//        if (!$id) {
//            return $this->redirect()->toRoute('accantona_categoria');
//        }
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//            $del = $request->getPost('del', 'No');
//
//            if ($del == 'Yes') {
//                $this->getCategoriaTable()->deleteByAttributes(array('id' => $id, 'userId' => $this->getUser()->id));
//            }
//
//            // Redirect to list of categories
//            return $this->redirect()->toRoute('accantona_categoria');
//        }
//
//        return array(
//            'id' => $id,
//            'category' => $this->getCategoriaTable()->getCategoria($id)
//        );
//    }

    public function movimentAction()
    {
        $request = $this->getRequest();
        $params = $this->params();
        $user = $this->getUser();
        $em = $this->getEntityManager();

        $accountId = (int) $params->fromRoute('id', 0);
        $months = (int) $params->fromQuery('monthsFilter', 0);

        $account = $em->getRepository('Application\Entity\Account')
            ->findOneBy(array('id' => $accountId, 'userId' => $user->id));

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $form = new MovimentForm();
        if ($request->isPost()) {

            $moviment = new Moviment();
            $data = $request->getPost();
            $form->setInputFilter($moviment->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $data['accountId'] = $accountId;
                $moviment->exchangeArray($data);
                $em->persist($moviment);
                $em->flush();

                return $this->redirect()->toRoute('accantonaAccount', array('action' => 'moviment', 'id' => $accountId));
            }
        }

        return new ViewModel(array(
            'account' => $account,
            'form' => $form,
            'months' => $months,
            'rows' => $em->getRepository('Application\Entity\Moviment')->findBy(array('accountId' => $accountId)),
        ));
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
