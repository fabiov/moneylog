<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Entity\Account;
use Application\Entity\Movement;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;

class MovementRepository extends EntityRepository
{
    /**
     * @param int $accountId
     * @param ?\DateTime $date
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBalance(int $accountId, ?\DateTime $date = null): float
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(m.amount), 0) AS balance')
            ->from(Movement::class, 'm')
            ->where('m.account=:accountId')
            ->setParameter(':accountId', $accountId);

        if ($date) {
            $qb->andWhere('m.date<=:date')->setParameter(':date', $date->format('Y-m-d'));
        }

        $result = $qb->getQuery()->getOneOrNullResult();
        return (float) $result['balance'];
    }

    /**
     * @param array<string, mixed> $searchParams
     * @param int $page
     * @param int $limit
     * @return Paginator<Movement>
     */
    public function paginator(array $searchParams, int $page, int $limit): Paginator
    {
        $query = $this
            ->getQuery($searchParams)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($query);
    }

    /**
     * @param array<string, mixed> $params
     * @return array<Movement>
     */
    public function search(array $params = []): array
    {
        return $this->getQuery($params)->getResult();
    }

    /**
     * @param int $userId
     * @return float
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getTotalExpense(int $userId): float
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(m.amount), 0) AS amount')
            ->from(Movement::class, 'm')
            ->innerJoin('m.category', 'c')
            ->where('c.user=:userId')
            ->setParameter(':userId', $userId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param int $userId
     * @param string $minDate
     * @param string $maxDate
     * @return array<int, array>
     */
    public function getMovementByDay(int $userId, string $minDate, string $maxDate): array
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m.date, SUM(m.amount) AS amount')
            ->from(Movement::class, 'm')
            ->innerJoin('m.account', 'a')
            ->where("a.user=:userId")
            ->andWhere('m.amount < 0 AND a.status=:status AND m.date BETWEEN :minDate AND :maxDate')
            ->groupBy('m.date')
            ->orderBy('m.date')
            ->setParameters([
                ':maxDate' => $maxDate,
                ':minDate' => $minDate,
                ':status' => Account::STATUS_HIGHLIGHT,
                ':userId' => $userId,
            ]);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param array<string, mixed> $params
     * @return Query
     */
    private function getQuery(array $params): Query
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from(Movement::class, 'm')
            ->join('m.account', 'a', Join::WITH, 'a.user=:user')
            ->setParameter('user', (int) $params['user']);

        if (!empty($params['account'])) {
            $qb->andWhere('m.account = :account')->setParameter('account', (int) $params['account']);
        }
        if (!empty($params['dateMin'])) {
            $qb->andWhere('m.date >= :dateMin')->setParameter('dateMin', $params['dateMin']);
        }
        if (!empty($params['dateMax'])) {
            $qb->andWhere('m.date <= :dateMax')->setParameter('dateMax', $params['dateMax']);
        }
        if (!empty($params['category'])) {
            $qb->andWhere('m.category = :category')->setParameter('category', (int) $params['category']);
        }
        if (!empty($params['description'])) {
            $description = '%' . $params['description'] . '%';
            $qb->andWhere('m.description LIKE :description')->setParameter('description', $description);
        }
        if (is_numeric($params['amountMin'])) {
            $qb->andWhere('m.amount >= :amountMin')->setParameter('amountMin', (float) $params['amountMin']);
        }
        if (is_numeric($params['amountMax'])) {
            $qb->andWhere('m.amount <= :amountMax')->setParameter('amountMax', (float) $params['amountMax']);
        }

        switch ($params['orderField']) {
            case 'amount':
                $qb->orderBy('m.amount', $params['orderType'] === 'DESC' ? 'DESC' : 'ASC');
                break;
            case 'description':
                $qb->orderBy('m.description', $params['orderType'] === 'DESC' ? 'DESC' : 'ASC');
                break;
            case 'date':
            default:
                $qb->orderBy('m.date', $params['orderType'] === 'ASC' ? 'ASC' : 'DESC');
                break;
        }

        return $qb->getQuery();
    }
}
