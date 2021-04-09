<?php

namespace MoneyLog\Controller;

use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use Application\Repository\AccountRepository;
use Doctrine\ORM\EntityManager;
use MoneyLog\Form\MoveForm;
use MoneyLog\Form\MovementForm;
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

        /* @var Movement $item */
        $item = $this->em->getRepository(Movement::class)->findOneBy(['id' => $id]);

        $type = $item->amount < 0 ? -1 : 1;
        $item->amount = abs($item->amount);

        if (!$item || $item->account->getUserId() != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $form = new MovementForm('movement', $this->em, $this->user->id);
        $form->bind($item);
        $form->get('type')->setValue($type);

        $request = $this->getRequest();
        $searchParams = $this->params()->fromQuery();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setInputFilter($item->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $item->amount = $item->amount * $data['type'];
                $item->category = $this->em->getRepository(Category::class)->findOneBy([
                    'id' => $data['category'], 'userId' => $this->user->id
                ]);
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

        /* @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Movement::class);
        $rows               = $movementRepository->search($searchParams);

        $previewsDate    = date('Y-m-d', strtotime("$dateMin -1 day"));
        $previewsBalance = $movementRepository->getBalance($accountId, $previewsDate);
        $balances        = [$previewsDate => $previewsBalance];

        foreach (array_reverse($rows) as $movement) {
            $date = $movement->date->format('Y-m-d');

            if (isset($balances[$date])) {
                $balances[$date] += $movement->amount;
            } else {
                $balances[$date] = $movement->amount + $balances[$previewsDate];
                $previewsDate = $date;
            }
        }

        $dataLineChart = [];
        foreach ($balances as $date => $balance) {
            $dataLineChart[] = ['date' => $date, 'balance' => $balance];
        }

        return new ViewModel(array(
            'account'          => $account,
            'balanceAccount'   => $movementRepository->getBalance($accountId),
            'balanceAvailable' => $movementRepository->getBalance($accountId, new \DateTime()),
            'categories'       => $categories,
            'dataLineChart'    => $dataLineChart,
            'rows'             => $movementRepository->search($searchParams),
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

        /* @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Movement::class);

        $fileName = 'export-' . strtolower($account->getName()) . '.csv';
        $this->getResponse()->getHeaders()
            ->addHeaderLine('Content-Disposition: attachment; filename="' . $fileName . '"')
            ->addHeaderLine('Content-Type: text/csv; charset=utf-8');

        return (new ViewModel(['rows' => $movementRepository->search($searchParams)]))->setTerminal(true);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /* @var $item \Application\Entity\Movement */
        $item = $this->em->getRepository('Application\Entity\Movement')->findOneBy(array('id' => $id));

        if ($item && $item->account->getUserId() == $this->user->id) {
            $this->em->remove($item);
            $this->em->flush();
        }

        return $this->redirect()->toRoute(
            'accantonaMovement',
            ['action' => 'account', 'id' => $item->accountId],
            ['query' => $this->params()->fromQuery()]
        );
    }

    public function moveAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $searchParams = $this->params()->fromQuery();

        /* @var AccountRepository $accountRepo */
        $accountRepo = $this->em->getRepository('Application\Entity\Account');

        /* @var $sourceAccount Account */
        $sourceAccount = $accountRepo->find($id);

        if (!$sourceAccount || $sourceAccount->getUserId() != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $accountOptions = array('' => '');
        foreach ($accountRepo->getUserAccounts($this->user->id) as $account) {
            if ($account->getId() != $sourceAccount->getId() && !$account->isClosed()) {
                $accountOptions[$account->getId()] = $account->getName();
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

                if (!$targetAccount || $targetAccount->getUserId() != $this->user->id) {
                    return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
                }

                $outcoming = new Movement();
                $outcoming->exchangeArray(array(
                    'date'        => $data['date'],
                    'amount'      => $data['amount'] * -1,
                    'description' => $data['description'],
                ));
                $outcoming->account = $sourceAccount;
                $this->em->persist($outcoming);

                $incoming = new Movement();
                $incoming->exchangeArray(array(
                    'accountId'   => $targetAccount->getId(),
                    'date'        => $data['date'],
                    'amount'      => $data['amount'],
                    'description' => $data['description'],
                ));
                $incoming->account = $targetAccount;
                $this->em->persist($incoming);

                $this->em->flush();

                $routeParams = ['action' => 'account', 'id' => $sourceAccount->getId()];
                return $this->redirect()->toRoute('accantonaMovement', $routeParams, ['query' => $searchParams]);
            }
        }

        $routeParams = ['action' => 'move', 'id' => $id];
        $routeOptions = ['query' => $searchParams];
        $form->setAttribute('action', $this->url()->fromRoute('accantonaMovement', $routeParams, $routeOptions));
        return ['sourceAccount' => $sourceAccount, 'form' => $form, 'searchParams' => $searchParams];
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function addAction()
    {
        $accountId = (int) $this->params()->fromRoute('id');
        $searchParams = $this->params()->fromQuery();

        /* @var $account Account */
        $account = $this->em->getRepository('Application\Entity\Account')->find($accountId);

        if (!$account || $account->getUserId() != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
        }

        $request = $this->getRequest();
        $form = new MovementForm('movement', $this->em, $this->user->id);
        if ($request->isPost()) {
            $movement = new Movement();
            $data = $request->getPost();
            $form->setInputFilter($movement->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {

                $repetitionNumber = (int) $data['repetitionNumber'];
                $repetitionNumber = !empty($data['repetition']) && 
                                    in_array($data['repetitionPeriod'], ['month', 'week']) &&
                                    $repetitionNumber > 0 && $repetitionNumber < 13 ? $repetitionNumber : 1;

                $category = $this->em->getRepository('Application\Entity\Category')
                    ->findOneBy(['id' => $data['category'], 'userId' => $this->user->id]);

                for ($i = 0; $i < $repetitionNumber; $i++) {
                    $movement = new Movement();
                    $movement->exchangeArray([
                        'date'          => date('Y-m-d', strtotime("{$data['date']} +$i {$data['repetitionPeriod']}")),
                        'amount'        => $data['amount'] * $data['type'],
                        'description'   => $data['description'],
                        'category'      => $category,
                    ]);
                    $movement->account = $account;

                    $this->em->persist($movement);
                    $this->em->flush();
                }

                return $this->redirect()->toRoute('accantonaMovement', [
                    'action' => 'account', 
                    'id'    => $accountId
                ], ['query' => $searchParams]);
            }
        }

        $form->setAttribute('action', $this->url()->fromRoute(
            'accantonaMovement',
            ['action' => 'add', 'id' => $account->getId()],
            ['query' => $searchParams]
        ));
        return ['sourceAccount' => $account, 'form' => $form, 'searchParams' => $searchParams];
    }
}
