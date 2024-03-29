<?php

declare(strict_types=1);

namespace MoneyLog\Controller;

use Application\Entity\Account;
use Application\Entity\Movement;
use Application\Entity\User;
use Auth\Model\LoggedUser;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use MoneyLog\Form\AccountForm;
use MoneyLog\Form\Filter\AccountFilter;

class AccountController extends AbstractActionController
{
    private LoggedUser $user;

    private EntityManagerInterface $em;

    public function __construct(LoggedUser $user, EntityManagerInterface $em)
    {
        $this->user = $user;
        $this->em = $em;
    }

    /**
     * @return Response|array<AccountForm>
     */
    public function addAction()
    {
        $form = new AccountForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new AccountFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                /** @var User $user */
                $user = $this->em->find(User::class, $this->user->getId());

                /** @var array<string, mixed> $data */
                $data = $form->getData();

                $account = new Account($user, $data['name']);

                $this->em->persist($account);
                $this->em->flush();

                return $this->redirect()->toRoute('accantonaAccount');
            }
        }
        return ['form' => $form];
    }

    public function indexAction(): ViewModel
    {
        $data = [];

        /** @var \Application\Repository\AccountRepository $accountRepository */
        $accountRepository = $this->em->getRepository(Account::class);

        // i dati in un record set potrebbero non essere nell'altro e vice versa

        $accountAvailable = $accountRepository->getTotals($this->user->getId(), false, new \DateTime());
        foreach ($accountAvailable as $i) {
            $data[$i['id']]['id'] = $i['id'];
            $data[$i['id']]['name'] = $i['name'];
            $data[$i['id']]['status'] = $i['status'];
            $data[$i['id']]['available'] = $i['total'];
        }

        $accountBalances = $accountRepository->getTotals($this->user->getId(), false);
        foreach ($accountBalances as $i) {
            $data[$i['id']]['id'] = $i['id'];
            $data[$i['id']]['name'] = $i['name'];
            $data[$i['id']]['status'] = $i['status'];
            $data[$i['id']]['balance'] = $i['total'];
        }

        return new ViewModel(['rows' => $data]);
    }

    /**
     * @return array<string, mixed>|Response
     */
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var ?Account $account */
        $account = $this->em->getRepository(Account::class)->findOneBy(['id' => $id, 'user' => $this->user->getId()]);

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
        }

        $form = new AccountForm();
        $form->setData(['name' => $account->getName(), 'status' => $account->getStatus()]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setInputFilter(new AccountFilter());
            $form->setData($data);

            if ($form->isValid()) {

                /** @var array<string, mixed> $validatedData */
                $validatedData = $form->getData();

                $account->setName($validatedData['name']);
                $account->setStatus($validatedData['status']);

                $this->em->flush();
                return $this->redirect()->toRoute('accantonaAccount'); // Redirect to list
            }
        }
        return ['id' => $id, 'form' => $form];
    }

    /**
     * @return Response
     */
    public function deleteAction(): Response
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('accantona_categoria');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            /* @var Account $account */
            $account = $this->em->getRepository(Account::class)->findOneBy(['id' => $id, 'user' => $this->user->getId()]);
            if ($account) {
                $this->em->createQueryBuilder()
                    ->delete(Movement::class, 'm')
                    ->where('m.account=:account')
                    ->setParameter('account', $account)
                    ->getQuery()->execute();
                $this->em->remove($account);
                $this->em->flush();
            }
        }
        // Redirect to list of accounts
        return $this->redirect()->toRoute('accantonaAccount');
    }

    /**
     * @return \Laminas\Http\Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function balanceAction(): Response
    {
        $amount      = (float) $this->getRequest()->getPost('amount');
        $description = $this->getRequest()->getPost('description', 'Conguaglio');
        $id          = (int) $this->params()->fromRoute('id');

        /** @var ?Account $account */
        $account = $this->em
            ->getRepository(Account::class)
            ->findOneBy(['id' => $id, 'user' => $this->user->getId()]);

        if ($account) {

            /** @var \Application\Repository\MovementRepository $movementRepository */
            $movementRepository = $this->em->getRepository(Movement::class);

            $currentBalance = $movementRepository->getBalance($id, new \DateTime());

            $movement = new Movement($account, $amount - $currentBalance, new \DateTime(), $description);
            $this->em->persist($movement);
            $this->em->flush();
        }

        return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
    }
}
