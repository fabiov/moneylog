<?php

namespace Accantona\Controller;

use Accantona\Form\MoveForm;
use Application\Entity\Account;
use Application\Entity\Moviment;
use Application\Repository\AccountRepository;
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
            'rows' => $em->getRepository('Application\Entity\Moviment')->findBy(array('accountId' => $accountId), array('date' => 'DESC')),
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

    public function moveAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $user = $this->getUser();

        /* @var AccountRepository $accountRepo */
        $accountRepo = $em->getRepository('Application\Entity\Account');

        /* @var $sourceAccount Account */
        $sourceAccount = $accountRepo->find($id);

        if (!$sourceAccount || $sourceAccount->userId != $user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $accountOptions = array('' => '');
        foreach ($accountRepo->getUserAccounts($user->id) as $account) {
            if ($account->id != $sourceAccount->id) {
                $accountOptions[$account->id] = $account->name;
            }
        }
        $form = new MoveForm();
        $form->setAccountOptions($accountOptions);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {

                $data = $form->getData();

                /* @var Account $targetAccount */
                $targetAccount = $accountRepo->find($data['targetAccountId']);

                if (!$targetAccount || $targetAccount->userId != $user->id) {
                    return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
                }

                $outcoming = new Moviment();
                $outcoming->exchangeArray(array(
                    'date'        => $data['date'],
                    'amount'      => $data['amount'] * -1,
                    'description' => $data['description'],
                ));
                $outcoming->account = $sourceAccount;
                $em->persist($outcoming);

                $incoming = new Moviment();
                $incoming->exchangeArray(array(
                    'accountId'   => $targetAccount->id,
                    'date'        => $data['date'],
                    'amount'      => $data['amount'],
                    'description' => $data['description'],
                ));
                $incoming->account = $targetAccount;
                $em->persist($incoming);

                $em->flush();

                return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
            }
        }

        return array('sourceAccount' => $sourceAccount, 'form' => $form);
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