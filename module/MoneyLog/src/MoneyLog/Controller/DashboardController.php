<?php

declare(strict_types=1);

namespace MoneyLog\Controller;

use Application\Entity\Account;
use Application\Entity\Category;
use Application\Entity\Movement;
use Application\Entity\Provision;
use Application\Entity\Setting;
use Auth\Model\LoggedUser;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{
    private LoggedUser $user;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, LoggedUser $user)
    {
        $this->em   = $em;
        $this->user = $user;
    }

    public function indexAction(): ViewModel
    {
        /** @var \Application\Repository\AccountRepository $accountRepository */
        $accountRepository = $this->em->getRepository(Account::class);

        /** @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $this->em->getRepository(Movement::class);

        /** @var \Application\Repository\ProvisionRepository $provisionRepository */
        $provisionRepository = $this->em->getRepository(Provision::class);

        /** @var \Application\Repository\CategoryRepository $categoryRepository */
        $categoryRepository = $this->em->getRepository(Category::class);

        /** @var Setting $setting */
        $setting = $this->em->find(Setting::class, $this->user->getId());

        $since = new \DateTime('-' . $setting->getMonths() . ' MONTH');
        $avgPerCategory = $categoryRepository->getAverages($this->user->getId(), $since);

        usort($avgPerCategory, static function ($a, $b) {
            return ($a['average'] ?? 0) <=> ($b['average'] ?? 0);
        });

        $totalExpense   = $movementRepository->getTotalExpense($this->user->getId());
        $stored         = $provisionRepository->getSum($this->user->getId()) + $totalExpense;
        $accounts       = $accountRepository->getTotals($this->user->getId(), true, new \DateTime());
        $donutSpends    = [];
        $donutAccounts  = [];
        $currentDay     = date('j');
        $monthBudget    = $stored > 0 && $setting->hasProvisioning() ? 0 - $stored : 0;
        $payDay         = $setting->getPayday();
        $totalBalance   = 0;

        foreach ($avgPerCategory as $category) {
            $totalBalance += $category['average'];
            if ($category['average'] < 0) {
                $donutSpends[] = ['label' => $category['description'], 'value' => abs($category['average'])];
            }
        }

        foreach ($accounts as $account) {
            $donutAccounts[] = ['label' => $account['name'], 'value' => $account['total']];
            $monthBudget += $account['total'];
        }

        if ($payDay) {
            if ($currentDay < $payDay) {
                $remainingDays = $setting->getPayday() - $currentDay;
                $begin = date("Y-m-$payDay", (int) strtotime('last month'));
            } else {
                $remainingDays = date('t') - $currentDay + $payDay;
                $begin = date("Y-m-$payDay");
            }
            $end = date('Y-m-d', (int) strtotime(($remainingDays - 1) . ' day'));
        } else {
            $remainingDays  = 0;
            $begin          = date('Y-m-01');
            $end            = date('Y-m-t');
        }

        $beginFiller = \DateTime::createFromFormat('Y-m-d', $begin);
        $endFiller   = \DateTime::createFromFormat('Y-m-d', $end);
        $monthlyOverviewData = [];
        for ($i = $beginFiller; $i <= $endFiller; $i->modify('+1 day')) {
            /** @var \DateTime $i */
            $monthlyOverviewData[$i->format('Y-m-d')] = ['date' => $i->format('d/m/Y'), 'amount' => 0];
        }
        foreach ($movementRepository->getMovementByDay($this->user->getId(), $begin, $end) as $item) {
            /** @var \DateTime $date */
            $date = $item['date'];
            $monthlyOverviewData[$date->format('Y-m-d')] = [
                'date' => $date->format('d/m/Y'), 'amount' => $item['amount']
            ];
        }

        return new ViewModel([
            'accounts'              => $accounts,
            'avgPerCategory'        => $avgPerCategory,
            'donutAccounts'         => $donutAccounts,
            'donutSpends'           => $donutSpends,
            'monthBudget'           => $monthBudget,
            'monthlyOverviewData'   => $monthlyOverviewData,
            'remainingDays'         => $remainingDays,
            'stored'                => $stored,
            'totalBalance'          => $totalBalance,
        ]);
    }
}
