<?php

namespace Accantona\Controller;

use Accantona\Model\Variabile;
use Accantona\Model\VariabileTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Model\Spesa;
use Accantona\Model\CategoriaTable;
use Accantona\Form\SpesaForm;
use Zend\Debug\Debug;

class RecapController extends AbstractActionController
{

    /**
     * @var SpesaTable $spesaTable
     */
    protected $spesaTable;
    protected $variabileTable;
    protected $accantonatoTable;

    /**
     * @var DoctrineORMEntityManager
     */
    protected $em;

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $user = $this->getUser();
        $spesaTable = $this->getSpesaTable();
        $avgPerCategory = $spesaTable->getAvgPerCategories($user->id);
        usort($avgPerCategory, function ($a, $b) {
            return $a['average'] == $b['average'] ? 0 : ($a['average'] < $b['average'] ? 1 : -1);
        });

        $variables = array();
        foreach ($this->getVariabileTable()->fetchAll(array('userId' => $user->id)) as $variable) {
            $variables[$variable->nome] = $variable->valore;
        }

        $payDay = $this->getEntityManager()->find('Application\Entity\Setting', $user->id)->payDay;
        $currentDay = date('j');
        return new ViewModel(array(
            'avgPerCategory' => $avgPerCategory,
            'variables' => $variables,
            'stored' => $this->getAccantonatoTable()->getSum($user->id) - $spesaTable->getSum($user->id),
            'remainingDays' => $currentDay < $payDay ? $payDay - $currentDay : date('t') - $currentDay + $payDay,
        ));
    }

    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            /* @var VariabileTable $variabileTable */
            $variabileTable = $this->getVariabileTable();
            $user = $this->getUser();

            // saldo_banca
            $val = $this->params()->fromPost('saldo_banca');
            if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $val)) {
                $variabileTable->updateByName('saldo_banca', $val, $user->id);
            }
            // contanti
            $val = $this->params()->fromPost('contanti');
            if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $val)) {
                $variabileTable->updateByName('contanti', $val, $user->id);
            }
            // risparmio
            $val = $this->params()->fromPost('risparmio');
            if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $val)) {
                $variabileTable->updateByName('risparmio', $val, $user->id);
            }
        }
        return $this->redirect()->toRoute('accantona_recap', array('action' => 'index'));
    }

    public function getSpesaTable()
    {
        if (!$this->spesaTable) {
            $sm = $this->getServiceLocator();
            $this->spesaTable = $sm->get('Accantona\Model\SpesaTable');
        }
        return $this->spesaTable;
    }

    public function getVariabileTable()
    {
        if (!$this->variabileTable) {
            $sm = $this->getServiceLocator();
            $this->variabileTable = $sm->get('Accantona\Model\VariabileTable');
        }
        return $this->variabileTable;
    }

    public function getAccantonatoTable()
    {
        if (!$this->accantonatoTable) {
            $sm = $this->getServiceLocator();
            $this->accantonatoTable = $sm->get('Accantona\Model\AccantonatoTable');
        }
        return $this->accantonatoTable;
    }

    public function getUser()
    {
        return $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
    }

}
