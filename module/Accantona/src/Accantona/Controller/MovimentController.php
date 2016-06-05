<?php

namespace Accantona\Controller;

use Application\Entity\Moviment;
use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Accantona\Form\MovimentForm;
use Zend\View\Model\ViewModel;

class MovimentController extends AbstractActionController
{

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $user = $this->getUser();

        /* @var \Application\Entity\Moviment $item */
        $item = $em->getRepository('Application\Entity\Moviment')->findOneBy(array('id' => $id));

        if (!$item || $item->account->userId != $user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $form = new MovimentForm();
        $form->bind($item);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($item->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                return $this->redirect()
                    ->toRoute('accantonaMoviment', array('action' => 'account', 'id' => $item->accountId));
            }
        }

        return array('item' => $item, 'form' => $form);
    }

    public function accountAction()
    {
        $request = $this->getRequest();
        $params = $this->params();
        $user = $this->getUser();
        $em = $this->getEntityManager();

        $accountId = (int) $params->fromRoute('id', 0);
        $months = (int) $params->fromQuery('monthsFilter', 1);

        $oDateTime = new \DateTime("-$months month");

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
                $moviment->exchangeArray($data);
                $moviment->account = $account;

                $em->persist($moviment);
                $em->flush();

                return $this->redirect()
                    ->toRoute('accantonaMoviment', array('action' => 'account', 'id' => $accountId));
            }
        }

        $movimentRepository = $em->getRepository('Application\Entity\Moviment');
        return new ViewModel(array(
            'account' => $account,
            'form' => $form,
            'months' => $months,
            'rows' => $em->getRepository('Application\Entity\Moviment')->findBy(array('accountId' => $accountId)),
            'balanceEnd' => $movimentRepository->getBalance($accountId),
            'balanceStart' => $months ? $movimentRepository->getBalance($accountId, $oDateTime->sub(new \DateInterval('P1D'))) : 0,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $user = $this->getUser();

        /* @var $item \Application\Entity\Moviment */
        $item = $em->getRepository('Application\Entity\Moviment')->findOneBy(array('id' => $id));

        if ($item && $item->account->userId == $user->id) {
            $em->remove($item);
            $em->flush();
        }
        return $this->redirect()->toRoute('accantonaMoviment', array('action' => 'account', 'id' => $item->accountId));
    }

    /**
     * @return \Application\Entity\User
     */
    public function getUser()
    {
        return $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
    }

    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

}