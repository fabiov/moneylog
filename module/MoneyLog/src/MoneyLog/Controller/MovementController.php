<?php

namespace MoneyLog\Controller;

use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use Application\Repository\AccountRepository;
use Doctrine\ORM\EntityManager;
use MoneyLog\Form\MoveForm;
use MoneyLog\Form\MovementForm;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

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

        /* @var ?Movement $item */
        $item = $this->em->getRepository(Movement::class)->findOneBy(['id' => $id]);

        if (!$item || $item->getAccount()->getUser()->getId() != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
        }

        $type = $item->getAmount() < 0 ? -1 : 1;
        $item->setAmount(abs($item->getAmount()));

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

                /** @var Category $category */
                $category = $this->em
                    ->getRepository(Category::class)
                    ->findOneBy(['id' => $data['category'], 'user' => $this->user->id]);

                $item->setAmount($item->getAmount() * $data['type']);
                $item->setCategory($category) ;
                $this->em->flush();

                return $this->redirect()->toRoute(
                    'accantonaMovement',
                    ['action' => 'account', 'id' => $item->getAccount()->getId()],
                    ['query' => $searchParams]
                );
            }
        }

        return ['item' => $item, 'form' => $form, 'searchParams' => $searchParams];
    }

    /**
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

        $criteria = ['id' => $accountId, 'user' => $this->user->id];
        $account = $this->em->getRepository(Account::class)->findOneBy($criteria);

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
        }

        $categories = $this->em->getRepository(Category::class)
            ->findBy(['status' => 1, 'user' => $this->user->id], ['descrizione' => 'ASC']);

        /** @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Movement::class);
        $rows               = $movementRepository->search($searchParams);

        $previewsDate    = date('Y-m-d', strtotime("$dateMin -1 day"));
        $previewsBalance = $movementRepository->getBalance($accountId, new \DateTime($previewsDate));
        $balances        = [$previewsDate => $previewsBalance];

        /** @var Movement $movement */
        foreach (array_reverse($rows) as $movement) {
            $date = $movement->getDate()->format('Y-m-d');

            if (isset($balances[$date])) {
                $balances[$date] += $movement->getAmount();
            } else {
                $balances[$date] = $movement->getAmount() + $balances[$previewsDate];
                $previewsDate = $date;
            }
        }

        $dataLineChart = [];
        foreach ($balances as $date => $balance) {
            $dataLineChart[] = ['date' => $date, 'balance' => $balance];
        }

        return new ViewModel([
            'account'          => $account,
            'balanceAccount'   => $movementRepository->getBalance($accountId),
            'balanceAvailable' => $movementRepository->getBalance($accountId, new \DateTime()),
            'categories'       => $categories,
            'dataLineChart'    => $dataLineChart,
            'rows'             => $movementRepository->search($searchParams),
            'searchParams'     => $searchParams,
        ]);
    }

    /**
     * @return \Laminas\Http\Response|ViewModel
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
        $account = $this->em->getRepository(Account::class)
            ->findOneBy(['id' => $accountId, 'user' => $this->user->id]);

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
        }

        /** @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Movement::class);

        $fileName = 'export-' . strtolower($account->getName()) . '.csv';
        $this->getResponse()->getHeaders()
            ->addHeaderLine('Content-Disposition: attachment; filename="' . $fileName . '"')
            ->addHeaderLine('Content-Type: text/csv; charset=utf-8');

        return (new ViewModel(['rows' => $movementRepository->search($searchParams)]))->setTerminal(true);
    }

    public function deleteAction(): Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /* @var $item \Application\Entity\Movement */
        $item = $this->em->getRepository('Application\Entity\Movement')->findOneBy(['id' => $id]);

        if ($item && $item->getAccount()->getUser()->getId() == $this->user->id) {
            $this->em->remove($item);
            $this->em->flush();
        }

        return $this->redirect()->toRoute(
            'accantonaMovement',
            ['action' => 'account', 'id' => $item->getAccount()->getId()],
            ['query' => $this->params()->fromQuery()]
        );
    }

    public function moveAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $searchParams = $this->params()->fromQuery();

        /** @var AccountRepository $accountRepository */
        $accountRepository = $this->em->getRepository(Account::class);

        /* @var $sourceAccount Account */
        $sourceAccount = $accountRepository->find($id);

        if (!$sourceAccount || $sourceAccount->getUser()->getId() != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
        }

        $accountOptions = ['' => ''];
        foreach ($accountRepository->getUserAccounts($this->user->id) as $account) {
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
                $targetAccount = $accountRepository->find($data['targetAccountId']);

                if (!$targetAccount || $targetAccount->getUser()->getId() != $this->user->id) {
                    return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
                }

                $outcoming = new Movement();
                $outcoming->setDate(new \DateTime($data['date']));
                $outcoming->exchangeArray([
                    'date'        => $data['date'],
                    'amount'      => $data['amount'] * -1,
                    'description' => $data['description'],
                ]);
                $outcoming->setAccount($sourceAccount);
                $this->em->persist($outcoming);

                $incoming = new Movement();
                $incoming->exchangeArray([
                    'accountId'   => $targetAccount->getId(),
                    'date'        => $data['date'],
                    'amount'      => $data['amount'],
                    'description' => $data['description'],
                ]);
                $incoming->setAccount($targetAccount);
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
        $account = $this->em->getRepository(Account::class)->find($accountId);

        if (!$account || $account->getUser()->getId() != $this->user->id) {
            return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
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

                $category = $this->em->getRepository(Category::class)
                    ->findOneBy(['id' => $data['category'], 'user' => $this->user->id]);

                for ($i = 0; $i < $repetitionNumber; $i++) {
                    $movement = new Movement();
                    $movement->exchangeArray([
                        'date'          => date('Y-m-d', strtotime("{$data['date']} +$i {$data['repetitionPeriod']}")),
                        'amount'        => $data['amount'] * $data['type'],
                        'description'   => $data['description'],
                        'category'      => $category,
                    ]);
                    $movement->setAccount($account);

                    $this->em->persist($movement);
                    $this->em->flush();
                }

                return $this->redirect()->toRoute(
                    'accantonaMovement',
                    ['action' => 'account', 'id' => $accountId],
                    ['query' => $searchParams]
                );
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
