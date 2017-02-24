<?php

namespace Accantona\Controller;

use Accantona\Form\MoveForm;
use Accantona\Form\MovimentForm;
use Application\Entity\Account;
use Application\Entity\Moviment;
use Application\Repository\AccountRepository;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MovimentController extends AbstractActionController
{

    /**
     * @var \stdClass
     */
    private $user;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(\stdClass $user, EntityManager $em)
    {
        $this->user = $user;
        $this->em   = $em;
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /* @var \Application\Entity\Moviment $item */
        $item = $this->em->getRepository('Application\Entity\Moviment')->findOneBy(array('id' => $id));

        if (!$item || $item->account->userId != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $form = new MovimentForm();
        $form->bind($item);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($item->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();

                return $this->redirect()
                    ->toRoute('accantonaMoviment', array('action' => 'account', 'id' => $item->accountId));
            }
        }

        return array('item' => $item, 'form' => $form);
    }

    public function accountAction()
    {
        $accountId      = $this->params()->fromRoute('id', 0);
        $dateMax        = $this->params()->fromQuery('dateMax', date('Y-m-d'));
        $dateMin        = $this->params()->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months')));
        $description    = $this->params()->fromQuery('description');

        $account = $this->em->getRepository('Application\Entity\Account')->findOneBy([
            'id'        => $accountId,
            'userId'    => $this->user->id,
        ]);

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $movimentRepository = $this->em->getRepository('Application\Entity\Moviment');
        return new ViewModel(array(
            'account'       => $account,
            'dateMax'       => $dateMax,
            'dateMin'       => $dateMin,
            'description'   => $description,
            'rows'          => $movimentRepository->search([
                'accountId'     => $accountId,
                'dateMax'       => $dateMax,
                'dateMin'       => $dateMin,
                'description'   => $description,
            ]),
            'balanceEnd'    => $movimentRepository->getBalance($accountId),
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /* @var $item \Application\Entity\Moviment */
        $item = $this->em->getRepository('Application\Entity\Moviment')->findOneBy(array('id' => $id));

        if ($item && $item->account->userId == $this->user->id) {
            $this->em->remove($item);
            $this->em->flush();
        }
        return $this->redirect()->toRoute('accantonaMoviment', array('action' => 'account', 'id' => $item->accountId));
    }

    public function moveAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /* @var AccountRepository $accountRepo */
        $accountRepo = $this->em->getRepository('Application\Entity\Account');

        /* @var $sourceAccount Account */
        $sourceAccount = $accountRepo->find($id);

        if (!$sourceAccount || $sourceAccount->userId != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $accountOptions = array('' => '');
        foreach ($accountRepo->getUserAccounts($this->user->id) as $account) {
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

                if (!$targetAccount || $targetAccount->userId != $this->user->id) {
                    return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
                }

                $outcoming = new Moviment();
                $outcoming->exchangeArray(array(
                    'date'        => $data['date'],
                    'amount'      => $data['amount'] * -1,
                    'description' => $data['description'],
                ));
                $outcoming->account = $sourceAccount;
                $this->em->persist($outcoming);

                $incoming = new Moviment();
                $incoming->exchangeArray(array(
                    'accountId'   => $targetAccount->id,
                    'date'        => $data['date'],
                    'amount'      => $data['amount'],
                    'description' => $data['description'],
                ));
                $incoming->account = $targetAccount;
                $this->em->persist($incoming);

                $this->em->flush();

                return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
            }
        }

        return array('sourceAccount' => $sourceAccount, 'form' => $form);
    }

    public function addAction()
    {
        $accountId = (int) $this->params()->fromRoute('id');

        /* @var $account Account */
        $account = $this->em->getRepository('Application\Entity\Account')->find($accountId);

        if (!$account || $account->userId != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $request = $this->getRequest();
        $form = new MovimentForm();
        if ($request->isPost()) {

            $moviment = new Moviment();
            $data = $request->getPost();
            $form->setInputFilter($moviment->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $moviment->exchangeArray($data);
                $moviment->account = $account;

                $this->em->persist($moviment);
                $this->em->flush();

                return $this->redirect()
                    ->toRoute('accantonaMoviment', array('action' => 'account', 'id' => $accountId));
            }
        }

        return array('sourceAccount' => $account, 'form' => $form);
    }
}