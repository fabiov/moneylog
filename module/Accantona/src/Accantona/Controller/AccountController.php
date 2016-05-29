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
        /* @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('a.id', 'a.name', 'a.recap', 'COALESCE(SUM(m.amount), 0) AS total')
            ->from('Application\Entity\Account', 'a')
            ->leftJoin('a.moviments', 'm')
            ->where('a.userId=?1')
            ->setParameter(1, 1)
            ->orderBy('a.name', 'ASC')
            ->groupBy('a.id');

        return new ViewModel(array('rows' => $qb->getQuery()->getResult()));
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

    public function balanceAction()
    {
        // check if the user is account owner
        $amount = $this->params()->fromQuery('amount');
        $id = (int) $this->params()->fromRoute('id', 0);

        $em = $this->getEntityManager();
        $account = $em->getRepository('Application\Entity\Account')
            ->findOneBy(array('id' => $id, 'userId' => $this->getUser()->id));

        if (!$account || !preg_match('/^[\-\+]?\d+(,\d+)?$/', $amount)) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        /* @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(m.amount), 0) AS total')
            ->from('Application\Entity\Moviment', 'm')
            ->where('m.accountId=:accountId')
            ->setParameter(':accountId', $id);
        $r = $qb->getQuery()->getOneOrNullResult();

        $moviment = new Moviment();
        $moviment->account = $account;
        $moviment->date = new \DateTime();
        $moviment->amount = str_replace(',', '.', $amount) - $r['total'];
        $moviment->description = 'Conguaglio';
        $em->persist($moviment);
        $em->flush();

        return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
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
