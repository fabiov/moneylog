<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class MovimentRepository extends EntityRepository
{

    public function getBalance($accountId, $date = null)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(m.amount), 0) AS balance')
            ->from('Application\Entity\Moviment', 'm')
            ->where('m.accountId=:accountId')
            ->setParameter(':accountId', $accountId);

        if ($date) {
            $qb->andWhere('m.date<=:date')
                ->setParameter(':date', $date instanceof \DateTime ? $date->format('Y-m-d') : $date);
        }

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result['balance'];
    }

    /**
     * @param int $accountId
     * @param DateTime|string $date
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLatest($accountId, $date = null)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from('Application\Entity\Moviment', 'm')
            ->where('m.accountId=:accountId')
            ->setParameter(':accountId', $accountId)
            ->orderBy('m.date', 'DESC');

        if ($date) {
            $qb->andWhere('m.date>=:date')
                ->setParameter(':date', $date instanceof \DateTime ? $date->format('Y-m-d') : $date);
        }

        return $qb->getQuery()->getResult();
    }

}