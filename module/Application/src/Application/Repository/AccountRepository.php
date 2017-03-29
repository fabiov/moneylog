<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{

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
     * @param $userId
     * @param bool $onlyRecap
     * @param \DateTime|null|string $date
     * @return array
     */
    public function getTotals($userId, $onlyRecap = false, $date = null)
    {
        $qb =  $this->getEntityManager()
        ->createQueryBuilder()
        ->select('a.id', 'a.name', 'a.recap', 'COALESCE(SUM(m.amount), 0) AS total')
        ->from('Application\Entity\Account', 'a')
        ->leftJoin('a.moviments', 'm')
        ->where('a.userId=:userId')
        ->setParameter(':userId', $userId);

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