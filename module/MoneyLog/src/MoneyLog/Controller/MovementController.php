<?php

namespace MoneyLog\Controller;

use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use Application\Repository\AccountRepository;
use Auth\Model\LoggedUser;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use MoneyLog\Form\Filter\MovementFilter;
use MoneyLog\Form\MoveForm;
use MoneyLog\Form\MovementForm;

class MovementController extends AbstractActionController
{
    private LoggedUser $user;

    private EntityManagerInterface $em;

    public function __construct(LoggedUser $user, EntityManagerInterface $em)
    {
        $this->user = $user;
        $this->em = $em;
    }

    public function indexAction(): ViewModel
    {
        $page = (int) $this->getRequest()->getQuery('page', 1);
        $pageSize = (int) $this->getRequest()->getQuery('limit', 25);
        $userId = $this->user->getId();

        /** @var AccountRepository $accountRepository */
        $accountRepository = $this->em->getRepository(Account::class);

        /** @var \Application\Repository\CategoryRepository $categoryRepository */
        $categoryRepository = $this->em->getRepository(Category::class);

        /** @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Movement::class);

        $params = $this->params();
        $searchParams = [
            'account' => $params->fromQuery('account'),
            'amountMax' => $params->fromQuery('amountMax'),
            'amountMin' => $params->fromQuery('amountMin'),
            'category' => $params->fromQuery('category'),
            'dateMax' => $params->fromQuery('dateMax', date('Y-m-d')),
            'dateMin' => $params->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months'))),
            'description' => $params->fromQuery('description'),
            'orderField' => $params->fromQuery('orderField', 'date'),
            'orderType' => $params->fromQuery('orderType', 'DESC'),
        ];

        return new ViewModel([
            'accounts' => $accountRepository->findBy(['user' => $userId], ['name' => 'ASC']),
            'balances' => $accountRepository->getUserAccountBalances($userId),
            'categories' => $categoryRepository->getUserCategories($userId),
            'page' => $page,
            'paginator' => $movementRepository->paginator(array_merge($searchParams, ['user' => $userId]), $page, $pageSize),
            'searchParams' => $searchParams,
        ]);
    }

    /**
     * @return \Laminas\Http\Response|\Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function accountAction()
    {
        $accountId    = $this->params()->fromRoute('id', 0);
        $dateMin      = $this->params()->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months')));
        $dateMax      = $this->params()->fromQuery('dateMax', date('Y-m-d'));
        $searchParams = [
            'account' => $accountId,
            'amountMax' => $this->params()->fromQuery('amountMax'),
            'amountMin' => $this->params()->fromQuery('amountMin'),
            'category' => $this->params()->fromQuery('category'),
            'dateMax' => $dateMax,
            'dateMin' => $dateMin,
            'description' => $this->params()->fromQuery('description'),
            'user' => $this->user->getId(),
        ];

        $criteria = ['id' => $accountId, 'user' => $this->user->getId()];
        $account = $this->em->getRepository(Account::class)->findOneBy($criteria);

        if (!$account) {
            return $this->getRedirectToDashboard();
        }

        $categories = $this->em->getRepository(Category::class)
            ->findBy(['active' => true, 'user' => $this->user->getId()], ['description' => 'ASC']);

        /** @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Movement::class);
        $rows               = $movementRepository->search($searchParams);

        $previewsDate    = date('Y-m-d', (int) strtotime("$dateMin -1 day"));
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

        /** @var ?Account $account */
        $account = $this->em->getRepository(Account::class)
            ->findOneBy(['id' => $accountId, 'user' => $this->user->getId()]);

        if (!$account) {
            return $this->getRedirectToDashboard();
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

        /** @var ?Movement $item */
        $item = $this->em->getRepository(Movement::class)->findOneBy(['id' => $id]);

        if (!$item || $item->getAccount()->getUser()->getId() != $this->user->getId()) {
            return $this->redirect()->toRoute('accantona_recap');
        }

        $this->em->remove($item);
        $this->em->flush();
        return $this->redirect()->toRoute(
            'accantonaMovement',
            ['action' => 'account', 'id' => $item->getAccount()->getId()],
            ['query' => $this->params()->fromQuery()]
        );
    }

    /**
     * @return array<string, mixed>|\Laminas\Http\Response
     * @throws \Exception
     */
    public function moveAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $searchParams = $this->params()->fromQuery();

        /** @var AccountRepository $accountRepository */
        $accountRepository = $this->em->getRepository(Account::class);

        /** @var ?Account $sourceAccount */
        $sourceAccount = $accountRepository->find($id);

        if (!$sourceAccount || $sourceAccount->getUser()->getId() != $this->user->getId()) {
            return $this->getRedirectToDashboard();
        }

        $accountOptions = ['' => ''];
        foreach ($accountRepository->getUserAccounts($this->user->getId()) as $account) {
            if ($account->getId() != $sourceAccount->getId() && $account->getStatus() !== Account::STATUS_CLOSED) {
                $accountOptions[$account->getId()] = $account->getName();
            }
        }
        $form = new MoveForm();
        $form->setAccountOptions($accountOptions);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var array<string, mixed> $data */
                $data = $form->getData();

                /** @var ?Account $targetAccount */
                $targetAccount = $accountRepository->find($data['targetAccountId']);

                if (!$targetAccount || $targetAccount->getUser()->getId() != $this->user->getId()) {
                    return $this->getRedirectToDashboard();
                }

                $date = new \DateTime($data['date']);

                $outComing = new Movement($sourceAccount, $data['amount'] * -1, $date, $data['description']);
                $this->em->persist($outComing);

                $inComing = new Movement($targetAccount, $data['amount'], $date, $data['description']);
                $this->em->persist($inComing);

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
     * @return array<mixed>|\Laminas\Http\Response
     * @throws \Exception
     */
    public function addAction()
    {
        $accountIdFromRoute = (int) $this->params()->fromRoute('id');
        $searchParams = $this->params()->fromQuery();

        $request = $this->getRequest();
        $form = new MovementForm('movement', $this->em, $this->user->getId());
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setInputFilter(new MovementFilter());
            $form->setData($data);

            if ($form->isValid()) {
                /** @var array<string, mixed> $validatedData */
                $validatedData = $form->getData();
                $account = $this->getAccount($validatedData['account']);

                if (!$account) {
                    return $this->getRedirectToDashboard();
                }

                $movement = new Movement(
                    $account,
                    $validatedData['amount'] * $validatedData['type'],
                    new \DateTime($validatedData['date']),
                    $validatedData['description'],
                    $this->getCategory($validatedData['category'])
                );

                $this->em->persist($movement);
                $this->em->flush();

                return $this->redirect()->toRoute(
                    'accantonaMovement',
                    ['action' => 'account', 'id' => $data['account']],
                    ['query' => $searchParams]
                );
            }
        }

        $form->setAttribute('action', $this->url()->fromRoute(
            'accantonaMovement',
            ['action' => 'add', 'id' => $accountIdFromRoute],
            ['query' => $searchParams]
        ));
        return ['accountId' => $accountIdFromRoute, 'form' => $form, 'searchParams' => $searchParams];
    }

    /**
     * @return array<string, mixed>|\Laminas\Http\Response
     */
    public function editAction()
    {
        $movementId = (int) $this->params()->fromRoute('id', 0);

        /** @var ?Movement $movement */
        $movement = $this->em->getRepository(Movement::class)->findOneBy(['id' => $movementId]);

        if (!$movement || $movement->getAccount()->getUser()->getId() != $this->user->getId()) {
            return $this->getRedirectToDashboard();
        }

        $form = new MovementForm('movement', $this->em, $this->user->getId());
        $form->setData([
            'account' => $movement->getAccount(),
            'amount' => abs($movement->getAmount()),
            'category' => $movement->getCategory(),
            'description' => $movement->getDescription(),
            'type' => $movement->getAmount() < 0 ? Movement::OUT : Movement::IN,
        ]);

        $request = $this->getRequest();
        $searchParams = $this->params()->fromQuery();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setInputFilter(new MovementFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $account = $this->getAccount($data['account']);

                if (!$account) {
                    return $this->getRedirectToDashboard();
                }

                $movement->setAccount($account);
                $movement->setAmount($data['amount'] * $data['type']);
                $movement->setCategory($this->getCategory((int) $data['category']));
                $movement->setDescription($data['description']);
                $this->em->flush();

                return $this->redirect()->toRoute(
                    'accantonaMovement',
                    ['action' => 'account', 'id' => $movement->getAccount()->getId()],
                    ['query' => $searchParams]
                );
            }
        }

        $form->setAttribute('action', $this->url()->fromRoute(
            'accantonaMovement',
            ['action' => 'edit', 'id' => $this->params()->fromRoute('id')],
            ['query' => $searchParams]
        ));
        return ['item' => $movement, 'form' => $form, 'searchParams' => $searchParams];
    }

    private function getAccount(int $id): ?Account
    {
        /** @var ?Account $account */
        $account = $this->em
            ->getRepository(Account::class)
            ->findOneBy(['id' => $id, 'user' => $this->user->getId()]);

        return $account;
    }

    private function getCategory(int $id): ?Category
    {
        /** @var ?Category $category */
        $category = $this->em
            ->getRepository(Category::class)
            ->findOneBy(['id' => $id, 'user' => $this->user->getId()]);

        return $category;
    }

    private function getRedirectToDashboard(): Response
    {
        return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
    }
}
