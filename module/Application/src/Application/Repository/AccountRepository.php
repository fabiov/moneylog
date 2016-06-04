<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository
{

    public function getTotals($userId, $onlyRecap = false)
    {
        $qb =  $this->getEntityManager()
        ->createQueryBuilder()
        ->select('a.id', 'a.name', 'a.recap', 'COALESCE(SUM(m.amount), 0) AS total')
        ->from('Application\Entity\Account', 'a')
        ->leftJoin('a.moviments', 'm')
        ->where('a.userId=:userId')
        ->setParameter(':userId', $userId);

        if ($onlyRecap) {
            $qb->andWhere('a.recap=1');
        }

        return $qb->orderBy('a.name', 'ASC')->groupBy('a.id')->getQuery()->getResult();
    }

}