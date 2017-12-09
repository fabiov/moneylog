<?php
namespace Accantona\Controller;

use Accantona\Model\AccantonatoTable;
use Application\Entity\Setting;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RecapController extends AbstractActionController
{
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
        $em, AccantonatoTable $accantonatoTable, \stdClass $user
    ) {
        $this->em               = $em;
        $this->accantonatoTable = $accantonatoTable;
        $this->user             = $user;
    }

    /**
     * @return array|ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function indexAction()
    {
        /* @var Setting $settings */
        $settings = $this->em->find('Application\Entity\Setting', $this->user->id);
        $avgPerCategory = $this->em->getRepository('Application\Entity\Category')
            ->getAverages($this->user->id, new \DateTime('-' . $settings->monthsRetrospective . ' MONTH'));

        usort($avgPerCategory, function ($a, $b) {
            return $a['average'] == $b['average'] ? 0 : ($a['average'] < $b['average'] ? -1 : 1);
        });

        $totalExpense   = $this->em->getRepository('Application\Entity\Moviment')->getTotalExpense($this->user->id);
        $stored         = $this->accantonatoTable->getSum($this->user->id) + $totalExpense;
        $accounts       = $this->em->getRepository('Application\Entity\Account')->getTotals($this->user->id, true, new \DateTime());
        $donutSpends    = array();
        $donutAccounts  = array();
        $currentDay     = date('j');
        $monthBudget    = $stored > 0 ? 0 - $stored : 0;

        foreach ($avgPerCategory as $category) {
            if ($category['average'] < 0) {
                $donutSpends[] = array('label' => $category['description'], 'value' => abs($category['average']));
            }
        }

        foreach ($accounts as $account) {
            $donutAccounts[] = array('label' => $account['name'], 'value' => $account['total']);
            $monthBudget += $account['total'];
        }

        if ($settings->payDay) {
            $remainingDays = $currentDay < $settings->payDay
                           ? $settings->payDay - $currentDay : date('t') - $currentDay + $settings->payDay;
        } else {
            $remainingDays = 0;
        }
        return new ViewModel([
            'accounts'       => $accounts,
            'avgPerCategory' => $avgPerCategory,
            'donutAccounts'  => $donutAccounts,
            'donutSpends'    => $donutSpends,
            'monthBudget'    => $monthBudget,
            'remainingDays'  => $remainingDays,
            'stored'         => $stored,
        ]);
    }
}