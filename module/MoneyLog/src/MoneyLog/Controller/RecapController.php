<?php
namespace MoneyLog\Controller;

use Application\Entity\Provision;
use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use Application\Entity\Setting;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

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
        $em = $this->em;
        /* @var Setting $settings */
        $settings = $em->find(Setting::class, $this->user->id);
        $avgPerCategory = $em->getRepository(Category::class)
                ->getAverages($this->user->id, new \DateTime('-' . $settings->monthsRetrospective . ' MONTH'));

        usort($avgPerCategory, function ($a, $b) { return ($a['average'] ?? 0) <=> ($b['average'] ?? 0); });

        $totalExpense   = $em->getRepository(Movement::class)->getTotalExpense($this->user->id);
        $stored         = $em->getRepository(Provision::class)->getSum($this->user->id) + $totalExpense;
        $accounts       = $em->getRepository(Account::class)->getTotals($this->user->id, true, new \DateTime());
        $donutSpends    = [];
        $donutAccounts  = [];
        $currentDay     = date('j');
        $monthBudget    = $stored > 0 && $settings->stored ? 0 - $stored : 0;

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
            if ($currentDay < $settings->payDay) {
                $remainingDays = $settings->payDay - $currentDay;
                $begin = date("Y-m-$settings->payDay", strtotime('last month'));
            } else {
                $remainingDays = date('t') - $currentDay + $settings->payDay;
                $begin = date("Y-m-$settings->payDay");
            }
            $end = date('Y-m-d', strtotime(($remainingDays - 1) . ' day'));
        } else {
            $remainingDays  = 0;
            $begin          = date('Y-m-01');
            $end            = date('Y-m-t');
        }

        $beginFiller = \DateTime::createFromFormat('Y-m-d', $begin);
        $endFiller   = \DateTime::createFromFormat('Y-m-d', $end);
        $monthlyOverviewData = [];
        for ($i = $beginFiller; $i <= $endFiller; $i->modify('+1 day')) {
            $monthlyOverviewData[$i->format('Y-m-d')] = ['date' => $i->format('d/m/Y'), 'amount' => 0];
        }
        foreach ($em->getRepository(Movement::class)->getMovementByDay($this->user->id, $begin, $end) as $item) {
            $monthlyOverviewData[$item['date']->format('Y-m-d')] = [
                'date' => $item['date']->format('d/m/Y'), 'amount' => $item['amount']
            ];
        }

        return [
            'accounts'              => $accounts,
            'avgPerCategory'        => $avgPerCategory,
            'donutAccounts'         => $donutAccounts,
            'donutSpends'           => $donutSpends,
            'monthBudget'           => $monthBudget,
            'monthlyOverviewData'   => $monthlyOverviewData,
            'remainingDays'         => $remainingDays,
            'stored'                => $stored,
        ];
    }
}
