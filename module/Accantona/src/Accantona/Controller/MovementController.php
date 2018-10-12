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

class MovementController extends AbstractActionController
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

        $form = new MovimentForm('moviment', $this->em, $this->user->id);
        $form->bind($item);

        $request = $this->getRequest();
        $searchParams = $this->params()->fromQuery();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setInputFilter($item->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $item->category = $this->em->getRepository('Application\Entity\Category')
                    ->findOneBy(['id' => $data['category'], 'userId' => $this->user->id]);
                $this->em->flush();

                return $this->redirect()->toRoute(
                    'accantonaMovement', ['action' => 'account', 'id' => $item->accountId], ['query' => $searchParams]
                );
            }
        }

        return ['item' => $item, 'form' => $form, 'searchParams' => $searchParams];
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function accountAction()
    {
        $accountId    = $this->params()->fromRoute('id', 0);
        $dateMin      = $this->params()->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months')));
        $dateMax      = $this->params()->fromQuery('dateMax', date('Y-m-d'));
        $searchParams = [
            'accountId'   => $accountId,
            'amountMax'   => $this->params()->fromQuery('amountMax'),
            'amountMin'   => $this->params()->fromQuery('amountMin'),
            'category'    => $this->params()->fromQuery('category'),
            'dateMax'     => $dateMax,
            'dateMin'     => $dateMin,
            'description' => $this->params()->fromQuery('description'),
        ];

        $account = $this->em->getRepository(\Application\Entity\Account::class)->findOneBy([
            'id'        => $accountId,
            'userId'    => $this->user->id,
        ]);

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $categories = $this->em->getRepository(\Application\Entity\Category::class)
            ->findBy(['status' => 1, 'userId' => $this->user->id], ['descrizione' => 'ASC']);

        /* @var \Application\Repository\MovimentRepository $movimentRepository */
        $movimentRepository = $this->em->getRepository(\Application\Entity\Moviment::class);
        $rows               = $movimentRepository->search($searchParams);

        $previewsDate    = date('Y-m-d', strtotime("$dateMin -1 day"));
        $previewsBalance = $movimentRepository->getBalance($accountId, $previewsDate);
        $balances        = [$previewsDate => $previewsBalance];

        foreach (array_reverse($rows) as $moviment) {
            $date = $moviment->date->format('Y-m-d');

            if (isset($balances[$date])) {
                $balances[$date] += $moviment->amount;
            } else {
                $balances[$date] = $moviment->amount + $balances[$previewsDate];
                $previewsDate = $date;
            }
        }

        $dataLineChart = [];
        foreach ($balances as $date => $balance) {
            $dataLineChart[] = ['date' => $date, 'balance' => $balance];
        }

        return new ViewModel(array(
            'account'          => $account,
            'balanceAccount'   => $movimentRepository->getBalance($accountId),
            'balanceAvailable' => $movimentRepository->getBalance($accountId, new \DateTime()),
            'categories'       => $categories,
            'dataLineChart'    => $dataLineChart,
            'rows'             => $movimentRepository->search($searchParams),
            'searchParams'     => $searchParams,
        ));
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function exportAction()
    {
        $accountId    = $this->params()->fromRoute('id', 0);
        $dateMin      = $this->params()->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months')));
        $searchParams = [
            'accountId'   => $accountId,
            'amountMax'   => $this->params()->fromQuery('amountMax'),
            'amountMin'   => $this->params()->fromQuery('amountMin'),
            'category'    => $this->params()->fromQuery('category'),
            'dateMax'     => $this->params()->fromQuery('dateMax'),
            'dateMin'     => $dateMin,
            'description' => $this->params()->fromQuery('description'),
        ];

        /* @var Account $account */
        $account = $this->em->getRepository(Account::class)->findOneBy([
            'id'        => $accountId,
            'userId'    => $this->user->id,
        ]);

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        /* @var \Application\Repository\MovimentRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Moviment::class);

        $this->getResponse()->getHeaders()
            ->addHeaderLine('Content-Disposition: attachment; filename="export-' . strtolower($account->name) . '.csv"')
            ->addHeaderLine('Content-Type: text/csv; charset=utf-8');

        return (new ViewModel(['rows' => $movementRepository->search($searchParams)]))->setTerminal(true);
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
        return $this->redirect()->toRoute('accantonaMovement', ['action' => 'account', 'id' => $item->accountId], ['query' => $this->params()->fromQuery()]);
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

    public function expenseAction()
    {
        return $this->addMovement($this->params('action'));
    }

    public function incomeAction()
    {
        return $this->addMovement($this->params('action'));
    }

    private function addMovement($action)
    {
        $accountId = (int) $this->params()->fromRoute('id');

        /* @var $account Account */
        $account = $this->em->getRepository('Application\Entity\Account')->find($accountId);

        if (!$account || $account->userId != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $request = $this->getRequest();
        $form = new MovimentForm('moviment', $this->em, $this->user->id);
        if ($request->isPost()) {

            $movement = new Moviment();
            $data = $request->getPost();
            $form->setInputFilter($movement->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {

                $repetitionNumber = (int) $data['repetitionNumber'];
                $repetitionNumber = !empty($data['repetition']) && in_array($data['repetitionPeriod'], ['month', 'week']) &&
                                    $repetitionNumber > 0 && $repetitionNumber < 13 ? $repetitionNumber : 1;

                $category = $this->em->getRepository('Application\Entity\Category')
                    ->findOneBy(['id' => $data['category'], 'userId' => $this->user->id]);

                for ($i = 0; $i < $repetitionNumber; $i++) {
                    $movement = new Moviment();
                    $movement->exchangeArray([
                        'date'          => date('Y-m-d', strtotime("{$data['date']} +$i {$data['repetitionPeriod']}")),
                        'amount'        => $data['amount'] * ($action === 'expense' ? -1 : 1),
                        'description'   => $data['description'],
                        'category'      => $category,
                    ]);
                    $movement->account = $account;

                    $this->em->persist($movement);
                    $this->em->flush();
                }

                return $this->redirect()->toRoute('accantonaMovement', ['action' => 'account', 'id' => $accountId]);
            }
        }

        return ['sourceAccount' => $account, 'form' => $form];
    }
}
