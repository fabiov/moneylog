<?php

namespace Accantona\Controller;

use Accantona\Form\AccountForm;
use Application\Entity\Account;
use Application\Entity\Moviment;
use Doctrine\ORM\EntityManager;
use Zend\Debug\Debug;
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
                $data['userId'] = $this->user->id;
                $account->exchangeArray($data);
                $this->em->persist($account);
                $this->em->flush();

                return $this->redirect()->toRoute('accantonaAccount');
            }
        }
        return array('form' => $form);
    }

    public function indexAction()
    {
        $data              = [];
        $accountRepository = $this->em->getRepository('Application\Entity\Account');

        // i dati in un record set potrebbero non essere nell'altro e vice versa

        $accountAvailable = $accountRepository->getTotals($this->user->id, false, new \DateTime());
        foreach ($accountAvailable as $i) {
            $data[$i['id']]['id']        = $i['id'];
            $data[$i['id']]['name']      = $i['name'];
            $data[$i['id']]['recap']     = $i['recap'];
            $data[$i['id']]['available'] = $i['total'];
        }

        $accountBalances  = $accountRepository->getTotals($this->user->id, false);
        foreach ($accountBalances as $i) {
            $data[$i['id']]['id']        = $i['id'];
            $data[$i['id']]['name']      = $i['name'];
            $data[$i['id']]['recap']     = $i['recap'];
            $data[$i['id']]['balance']   = $i['total'];
        }
        return new ViewModel(['rows' => $data]);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $account = $this->em->getRepository('Application\Entity\Account')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));

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
                $this->em->flush();
                return $this->redirect()->toRoute('accantonaAccount'); // Redirect to list
            }
        }
        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('accantona_categoria');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {

            /* @var Account $account */
            $account = $this->em->getRepository('Application\Entity\Account')->findOneBy([
                'id'     => $id,
                'userId' => $this->user->id
            ]);
            if ($account) {
                $this->em->createQueryBuilder()
                    ->delete('Application\Entity\Moviment', 'm')
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
     * @return \Zend\Http\Response
     */
    public function balanceAction()
    {
        // check if the user is account owner
        $amount      = (float) $this->getRequest()->getPost('amount');
        $description = $this->getRequest()->getPost('description', 'Conguaglio');
        $id          = (int) $this->params()->fromRoute('id', 0);

        $account = $this->em->getRepository('Application\Entity\Account')
            ->findOneBy(['id' => $id, 'userId' => $this->user->id]);

        if ($account && $amount) {
            /* @var \Doctrine\ORM\QueryBuilder $qb */
            $qb = $this->em
                ->createQueryBuilder()
                ->select('COALESCE(SUM(m.amount), 0) AS total')
                ->from('Application\Entity\Moviment', 'm')
                ->where('m.accountId=:accountId')
                ->setParameter(':accountId', $id);
            $r = $qb->getQuery()->getOneOrNullResult();

            $moviment              = new Moviment();
            $moviment->account     = $account;
            $moviment->date        = new \DateTime();
            $moviment->amount      = $amount - $r['total'];
            $moviment->description = $description;
            $this->em->persist($moviment);
            $this->em->flush();
        }

        return $this->redirect()->toRoute('accantonaAccount', array('action' => 'index'));
    }
}
