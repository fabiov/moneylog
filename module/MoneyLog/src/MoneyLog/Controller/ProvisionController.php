<?php

namespace MoneyLog\Controller;

use Application\Entity\Provision;
use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use MoneyLog\Form\AccantonatoForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class ProvisionController extends AbstractActionController
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

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function addAction()
    {
        $form = new AccantonatoForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $provision = new Provision();
            $form->setInputFilter($provision->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                /** @var User $user */
                $user = $this->em->find(User::class, $this->user->id);

                /** @var array $data */
                $data = $form->getData();

                $provision->exchangeArray($data);
                $provision->setUser($user);
                $this->em->persist($provision);
                $this->em->flush();

                return $this->redirect()->toRoute('accantona_accantonato');
            }
        }
        return new ViewModel(['form' => $form]);
    }

    public function indexAction(): ViewModel
    {
        $searchParams = [
            'dateMax'     => $this->params()->fromQuery('dateMax'),
            'dateMin'     => $this->params()->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months'))),
            'description' => $this->params()->fromQuery('description'),
        ];

        /** @var \Application\Repository\ProvisionRepository $provisionRepository */
        $provisionRepository = $this->em->getRepository(Provision::class);

        return new ViewModel([
            'balance'       => $provisionRepository->getBalance($this->user->id),
            'searchParams'  => $searchParams,
            'rows'          => $provisionRepository->search(array_merge($searchParams, ['userId' => $this->user->id])),
        ]);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var ?Provision $provision */
        $provision = $this->em->getRepository(Provision::class)->findOneBy(['id' => $id, 'user' => $this->user->id]);

        if (!$provision) {
            return $this->redirect()->toRoute('accantona_accantonato', ['action' => 'index']);
        }

        $form = new AccantonatoForm('accantonati');
        $form->bind($provision);

        $request = $this->getRequest();
        $searchParams = $this->params()->fromQuery();
        if ($request->isPost()) {
            $form->setInputFilter($provision->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();
                return $this->redirect()
                    ->toRoute('accantona_accantonato', [], ['query' => $searchParams]);
            }
        }
        return ['id' => $id, 'form' => $form, 'searchParams' => $searchParams];
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $spend = $this->em->getRepository(Provision::class)->findOneBy(['id' => $id, 'user' => $this->user->id]);

        if ($spend) {
            $this->em->remove($spend);
            $this->em->flush();
        }
        return $this->redirect()->toRoute('accantona_accantonato', [], ['query' => $this->params()->fromQuery()]);
    }
}
