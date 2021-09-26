<?php

namespace Application\Repository;

use Application\Entity\Account;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

class AccountRepository extends EntityRepository
{
    /**
     * @param int $userId
     * @return array<array>
     */
    public function getUserAccountBalances(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a.id', 'a.name', 'a.status', 'COALESCE(SUM(m.amount), 0) AS balance')
            ->from(Account::class, 'a')
            ->join('a.movements', 'm', Join::WITH, 'a.user=:userId')
            ->where("a.closed=:closed")
            ->setParameters([':closed' => false, ':userId' => $userId])
            ->groupBy('a.id');

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @param bool $onlyHighlight
     * @return array<Account>
     */
    public function getUserAccounts(int $userId, bool $onlyHighlight = false): array
    {
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.user=:userId')
            ->orderBy('a.name', 'ASC')
            ->setParameter(':userId', $userId);

        if ($onlyHighlight) {
            $qb->andWhere('a.status=:status')->setParameter(':status', Account::STATUS_HIGHLIGHT);
        } else {
            $qb->andWhere('a.status<>:status')->setParameter(':status', Account::STATUS_CLOSED);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @param bool $onlyHighlight
     * @param \DateTime|null|string $date
     * @return array<array>
     */
    public function getTotals(int $userId, bool $onlyHighlight = false, $date = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
           ->select('a.id', 'a.name', 'a.status', 'COALESCE(SUM(m.amount), 0) AS total')
           ->from(Account::class, 'a')
           ->leftJoin('a.movements', 'm')
           ->where("a.user=$userId");

        if ($date) {
            $qb->andWhere('m.date<=:date')
                ->setParameter(':date', $date instanceof \DateTime ? $date->format('Y-m-d') : $date);
        }

        if ($onlyHighlight) {
            $qb->andWhere('a.status=:status')->setParameter(':status', Account::STATUS_HIGHLIGHT);
        }

        return $qb->orderBy('total', 'DESC')->groupBy('a.id')->getQuery()->getResult();
    }

    /**
     * @param int $userId
     * @return array<Account>
     */
    public function getByUsage(int $userId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from(Account::class, 'a')
            ->leftJoin('a.movements', 'm')
            ->where('a.user=:userId')
            ->andWhere('a.status<>:status')
            ->groupBy('m.account')
            ->orderBy('COUNT(m.account)', 'DESC')
            ->setParameters([':status' => Account::STATUS_CLOSED, ':userId' => $userId]);

        return $qb->getQuery()->getResult();
    }
}
