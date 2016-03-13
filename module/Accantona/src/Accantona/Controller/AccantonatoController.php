<?php
namespace Accantona\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Accantonato;
use Accantona\Form\AccantonatoForm;
use Zend\Debug\Debug;

class AccantonatoController extends AbstractActionController
{

    protected $user;
    protected $accantonatoTable;

    public function addAction()
    {
        $form = new AccantonatoForm();

        $request = $this->getRequest();
        if ($request->isPost()) {

            $accantonato = new Accantonato();
            $form->setInputFilter($accantonato->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['userId'] = $this->getUser()->id;
                $accantonato->exchangeArray($data);
                $this->getAccantonatoTable()->save($accantonato);
                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_accantonato');
            }
            Debug::dump($_POST, '$_POST');
            Debug::dump($form->getMessages());
            die();
        }
        return array('form' => $form);
    }

    public function indexAction()
    {
        $where = array(
            'userId=' . $this->getUser()->id,
        );
        if (($months = (int) $this->params()->fromQuery('monthsFilter', 1)) != false) {
            $where[] = 'valuta>"' . date('Y-m-d', strtotime("-$months month")) . '"';
        }

        return new ViewModel(array(
            'months' => $months,
            'rows' => $this->getAccantonatoTable()->fetchAll($where),
        ));
    }

    public function getAccantonatoTable()
    {
        if (!$this->accantonatoTable) {
            $this->accantonatoTable = $this->getServiceLocator()->get('Accantona\Model\AccantonatoTable');
        }
        return $this->accantonatoTable;
    }

    public function getUser()
    {
        if (!$this->user) {
            $this->user = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
        }
        return $this->user;
    }

}