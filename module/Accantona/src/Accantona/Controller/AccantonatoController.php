<?php
namespace Accantona\Controller;

use Accantona\Form\AccantonatoForm;
use Accantona\Model\AccantonatoTable;
use Application\Entity\Accantonati;
use Doctrine\ORM\EntityManager;
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
        $dateMax        = $this->params()->fromQuery('dateMax', date('Y-m-d'));
        $dateMin        = $this->params()->fromQuery('dateMin', date('Y-m-d', strtotime('-3 months')));
        $description    = $this->params()->fromQuery('description');

        $accantonati = $this->em->getRepository('Application\Entity\Accantonati');

        return new ViewModel(array(
            'balance'       => $accantonati->getBalance($this->user->id),
            'dateMax'       => $dateMax,
            'dateMin'       => $dateMin,
            'description'   => $description,
            'rows'          => $accantonati->search([
                'dateMax'       => $dateMax,
                'dateMin'       => $dateMin,
                'description'   => $description,
                'userId'        => $this->user->id,
            ]),
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
        if ($request->isPost()) {
            $form->setInputFilter($accantonati->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();
                return $this->redirect()->toRoute('accantona_accantonato');
            }
        }
        return array('id' => $id, 'form' => $form);
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
        return $this->redirect()->toRoute('accantona_accantonato');
    }
}