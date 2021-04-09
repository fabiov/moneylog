<?php

namespace Application\Repository;

use Application\Entity\Account;
use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{

    /**
     * @param $userId
     * @param false $onlyRecap
     * @return array<Account>
     */
    public function getUserAccounts($userId, $onlyRecap = false)
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.userId=:userId')
            ->orderBy('a.name', 'ASC')
            ->setParameter(':userId', $userId);

        if ($onlyRecap) {
            $qb->andWhere('a.recap=1');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @param bool $onlyRecap
     * @param \DateTime|null|string $date
     * @return array
     */
    public function getTotals(int $userId, $onlyRecap = false, $date = null)
    {
        /* @var Doctrine\ORM\QueryBuilder */
        $qb = $this->getEntityManager()
                   ->createQueryBuilder()
                   ->select('a.id', 'a.name', 'a.recap', 'a.closed', 'COALESCE(SUM(m.amount), 0) AS total')
                   ->from(Account::class, 'a')
                   ->leftJoin('a.movements', 'm')
                   ->where("a.userId=$userId");

        if ($date) {
            $qb->andWhere('m.date<=:date')
                ->setParameter(':date', $date instanceof \DateTime ? $date->format('Y-m-d') : $date);
        }

        if ($onlyRecap) {
            $qb->andWhere('a.recap=1');
        }

        return $qb->orderBy('total', 'DESC')->groupBy('a.id')->getQuery()->getResult();
    }
}
