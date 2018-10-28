<?php
namespace MoneyLog\Controller;

use Application\Entity\Accantonati;
use Doctrine\ORM\EntityManager;
use MoneyLog\Form\AccantonatoForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AccantonatoController extends AbstractActionController
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
        $form = new AccantonatoForm();
        $request = $this->getRequest();
        if ($request->isPost()) {

            $accantonato = new Accantonati();
            $form->setInputFilter($accantonato->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['userId'] = $this->user->id;
                $accantonato->exchangeArray($data);
                $this->em->persist($accantonato);
                $this->em->flush();

                return $this->redirect()->toRoute('accantona_accantonato');
            }
        }
        return new ViewModel(['form' => $form]);
    }

    public function indexAction()
    {
        $searchParams = [
            'dateMax'     => $this->params()->fromQuery('dateMax'),
            'dateMin'     => $this->params()->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months'))),
            'description' => $this->params()->fromQuery('description'),
        ];

        $stored = $this->em->getRepository('Application\Entity\Accantonati');

        return new ViewModel(array(
            'balance'       => $stored->getBalance($this->user->id),
            'searchParams'  => $searchParams,
            'rows'          => $stored->search(array_merge($searchParams, ['userId' => $this->user->id])),
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $accantonati = $this->em->getRepository('Application\Entity\Accantonati')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));

        if (!$accantonati) {
            return $this->redirect()->toRoute('accantona_accantonato', array('action' => 'index'));
        }

        $form = new AccantonatoForm('accantonati');
        $form->bind($accantonati);

        $request = $this->getRequest();
        $searchParams = $this->params()->fromQuery();
        if ($request->isPost()) {
            $form->setInputFilter($accantonati->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();
                return $this->redirect()
                    ->toRoute('accantona_accantonato', [], ['query' => $searchParams]);
            }
        }
        return array('id' => $id, 'form' => $form, 'searchParams' => $searchParams);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $spend = $this->em->getRepository('Application\Entity\Accantonati')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));

        if ($spend) {
            $this->em->remove($spend);
            $this->em->flush();
        }
        return $this->redirect()->toRoute('accantona_accantonato', [], ['query' => $this->params()->fromQuery()]);
    }
}