<?php
namespace MoneyLog\Controller;

use Application\Entity\Account;
use Application\Entity\Movement;
use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use MoneyLog\Form\AccountForm;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccountController extends AbstractActionController
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
                $data['user'] = $this->em->find(User::class, $this->user->id);
                $account->exchangeArray($data);
                $this->em->persist($account);
                $this->em->flush();

                return $this->redirect()->toRoute('accantonaAccount');
            }
        }
        return ['form' => $form];
    }

    /**
     * @return ViewModel
     */
    public function indexAction(): ViewModel
    {
        $data = [];
        $accountRepository = $this->em->getRepository(Account::class);

        // i dati in un record set potrebbero non essere nell'altro e vice versa

        $accountAvailable = $accountRepository->getTotals($this->user->id, false, new \DateTime());
        foreach ($accountAvailable as $i) {
            $data[$i['id']]['id']        = $i['id'];
            $data[$i['id']]['name']      = $i['name'];
            $data[$i['id']]['closed']    = $i['closed'];
            $data[$i['id']]['recap']     = $i['recap'];
            $data[$i['id']]['available'] = $i['total'];
        }

        $accountBalances  = $accountRepository->getTotals($this->user->id, false);
        foreach ($accountBalances as $i) {
            $data[$i['id']]['id']        = $i['id'];
            $data[$i['id']]['name']      = $i['name'];
            $data[$i['id']]['closed']    = $i['closed'];
            $data[$i['id']]['recap']     = $i['recap'];
            $data[$i['id']]['balance']   = $i['total'];
        }
        return new ViewModel(['rows' => $data]);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var Account $account */
        $account = $this->em->getRepository(Account::class)->findOneBy(['id' => $id, 'user' => $this->user->id]);

        if (!$account) {
            return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
        }

        $form = new AccountForm();
        $form->bind($account);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($account->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();
                return $this->redirect()->toRoute('accantonaAccount'); // Redirect to list
            }
        }
        return ['id' => $id, 'form' => $form];
    }

    /**
     * @return Response
     * @throws OptimisticLockException
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
            $account = $this->em->getRepository(Account::class)->findOneBy(['id' => $id, 'user' => $this->user->id]);
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
     * @return Response
     * @throws OptimisticLockException
     */
    public function balanceAction(): Response
    {
        // check if the user is account owner
        $amount      = (float) $this->getRequest()->getPost('amount');
        $description = $this->getRequest()->getPost('description', 'Conguaglio');
        $routeName   = $this->getRequest()->getPost('forward');
        $id          = (int) $this->params()->fromRoute('id', 0);

        $account = $this->em->getRepository('Application\Entity\Account')
            ->findOneBy(['id' => $id, 'userId' => $this->user->id]);

        if ($account && $amount) {
            $currentBalance = $this->em->getRepository(Movement::class)
                ->getBalance($id, new \DateTime());

            $movement              = new Movement();
            $movement->account     = $account;
            $movement->date        = new \DateTime();
            $movement->amount      = $amount - $currentBalance;
            $movement->description = $description;
            $this->em->persist($movement);
            $this->em->flush();
        }

        switch ($routeName) {
            case 'accantonaMovement':
                return $this->redirect()->toRoute('accantonaMovement', ['action' => 'account', 'id' => $id]);
            case 'accantonaAccount':
            default:
                return $this->redirect()->toRoute('accantonaAccount', ['action' => 'index']);
        }
    }
}
