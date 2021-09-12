<?php

namespace MoneyLog\Controller;

use Application\Entity\Provision;
use Application\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use MoneyLog\Form\AccantonatoForm;
use MoneyLog\Form\Filter\ProvisionFilter;

class ProvisionController extends AbstractActionController
{
    private \stdClass $user;

    private EntityManagerInterface $em;

    public function __construct(\stdClass $user, EntityManagerInterface $em)
    {
        $this->user = $user;
        $this->em = $em;
    }

    /**
     * @return Response|ViewModel
     * @throws \Exception
     */
    public function addAction()
    {
        $form = new AccantonatoForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $provision = new Provision();
            $form->setInputFilter(new ProvisionFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                /** @var User $user */
                $user = $this->em->find(User::class, $this->user->id);

                /** @var array<string, mixed> $data */
                $data = $form->getData();

                $provision->setDescription($data['description']);
                $provision->setAmount($data['amount']);
                $provision->setDate(new \DateTime($data['date']));
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

    /**
     * @return array<string, mixed>|Response
     * @throws \Exception
     */
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var ?Provision $provision */
        $provision = $this->em->getRepository(Provision::class)->findOneBy(['id' => $id, 'user' => $this->user->id]);

        if (!$provision) {
            return $this->redirect()->toRoute('accantona_accantonato', ['action' => 'index']);
        }

        $form = new AccantonatoForm('accantonati');
        $form->setData([
            'date' => $provision->getDate(),
            'amount' => $provision->getAmount(),
            'description' => $provision->getDescription(),
        ]);

        $request = $this->getRequest();
        $searchParams = $this->params()->fromQuery();
        if ($request->isPost()) {
            $form->setInputFilter(new ProvisionFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var array<string, mixed> $validatedData */
                $validatedData = $form->getData();

                $provision->setDate(new \DateTime($validatedData['date']));
                $provision->setAmount($validatedData['amount']);
                $provision->setDescription($validatedData['description']);

                $this->em->flush();
                return $this->redirect()->toRoute('accantona_accantonato', [], ['query' => $searchParams]);
            }
        }
        return ['id' => $id, 'form' => $form, 'searchParams' => $searchParams];
    }

    public function deleteAction(): Response
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
