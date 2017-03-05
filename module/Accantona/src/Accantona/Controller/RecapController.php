<?php
namespace Accantona\Controller;

use Accantona\Model\AccantonatoTable;
use Accantona\Model\VariabileTable;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RecapController extends AbstractActionController
{
    /**
     * @var VariabileTable
     */
    private $variabileTable;

    /**
     * @var AccantonatoTable
     */
    private $accantonatoTable;

    /**
     * @var \stdClass
     */
    private $user;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(
        $em, AccantonatoTable $accantonatoTable, VariabileTable $variabileTable, \stdClass $user
    ) {
        $this->em               = $em;
        $this->accantonatoTable = $accantonatoTable;
        $this->variabileTable   = $variabileTable;
        $this->user             = $user;
    }

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $avgPerCategory = $this->em->getRepository('Application\Entity\Category')
            ->getAverages($this->user->id, new \DateTime('-30 MONTH'));

        usort($avgPerCategory, function ($a, $b) {
            return $a['average'] == $b['average'] ? 0 : ($a['average'] < $b['average'] ? -1 : 1);
        });

        $totalExpense   = $this->em->getRepository('Application\Entity\Moviment')->getTotalExpense($this->user->id);
        $payDay         = $this->em->find('Application\Entity\Setting', $this->user->id)->payDay;
        $stored         = $this->accantonatoTable->getSum($this->user->id) + $totalExpense;
        $accounts       = $this->em->getRepository('Application\Entity\Account')->getTotals($this->user->id, true, new \DateTime());
        $variables      = array();
        $donutSpends    = array();
        $donutAccounts  = array();
        $currentDay     = date('j');
        $monthBudget    = "-$stored";

        foreach ($avgPerCategory as $category) {
            if ($category['average'] < 0) {
                $donutSpends[] = array('label' => $category['description'], 'value' => abs($category['average']));
            }
        }

        foreach ($accounts as $account) {
            $donutAccounts[] = array('label' => $account['name'], 'value' => $account['total']);
            $monthBudget += $account['total'];
        }

        $rs = $this->variabileTable->fetchAll(array('userId' => $this->user->id));
        foreach ($rs as $variable) {
            $monthBudget += $variable->valore * $variable->segno;
            $variables[$variable->nome] = $variable->valore;
        }

        return new ViewModel(array(
            'stored'            => $stored,
            'accounts'          => $accounts,
            'variables'         => $variables,
            'monthBudget'       => $monthBudget,
            'remainingDays'     => $currentDay < $payDay ? $payDay - $currentDay : date('t') - $currentDay + $payDay,
            'avgPerCategory'    => $avgPerCategory,
            'donutSpends'       => $donutSpends,
            'donutAccounts'     => $donutAccounts,
        ));
    }

    public function editAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            // risparmio
            $val = $this->params()->fromPost('risparmio');
            if (preg_match('/^[0-9]+(\.[0-9]+)?$/', $val)) {
                $this->variabileTable->updateByName('risparmio', $val, $this->id);
            }
        }
        return $this->redirect()->toRoute('accantona_recap', array('action' => 'index'));
    }
}