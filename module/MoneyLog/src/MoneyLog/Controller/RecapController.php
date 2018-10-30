<?php
namespace MoneyLog\Controller;

use Application\Entity\Accantonati;
use Application\Entity\Account;
use Application\Entity\Movement;
use Application\Entity\Setting;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RecapController extends AbstractActionController
{
    /**
     * @var \stdClass
     */
    private $user;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct($em, \stdClass $user) {
        $this->em   = $em;
        $this->user = $user;
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

        $totalExpense   = $this->em->getRepository(Movement::class)->getTotalExpense($this->user->id);
        $stored         = $this->em->getRepository(Accantonati::class)->getSum($this->user->id) + $totalExpense;
        $accounts       = $this->em->getRepository(Account::class)->getTotals($this->user->id, true, new \DateTime());
        $donutSpends    = [];
        $donutAccounts  = [];
        $currentDay     = date('j');
        $monthBudget    = $stored > 0 ? 0 - $stored : 0;

        foreach ($avgPerCategory as $category) {
            if ($category['average'] < 0) {
                $donutSpends[] = ['label' => $category['description'], 'value' => abs($category['average'])];
            }
        }

        foreach ($accounts as $account) {
            $donutAccounts[] = ['label' => $account['name'], 'value' => $account['total']];
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
