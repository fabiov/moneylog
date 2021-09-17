<?php

namespace Application\Repository;

use Application\Entity\Category;
use Application\Entity\Movement;
use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    /**
     * Get the averages for categories
     *
     * @param int $userId
     * @param \DateTime $since
     * @return array<int, array<string, mixed>>
     */
    public function getAverages(int $userId, \DateTime $since): array
    {
        $oldest = $this->oldestMovements($userId);

        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(m.amount) AS amount, MIN(m.date) AS first_date, c.id')
            ->from(Category::class, 'c', 'c.id')
            ->innerJoin(Movement::class, 'm', 'WITH', 'c.id=m.category')
            ->where("c.user=$userId")
            ->andWhere('c.active=:active')
            ->andWhere('m.date >= :since')
            ->setParameters([':since'  => $since->format('Y-m-d'), ':active' => true])
            ->groupBy('c.id');

        $rs = $qb->getQuery()->getResult();

        $data = [];
        foreach ($oldest as $categoryId => $row) {
            $average = null;
            if (isset($rs[$categoryId])) {
                $date = $row['date'] < $rs[$categoryId]['first_date']
                      ? $since->format('Y-m-d') : $rs[$categoryId]['first_date'];
                [$y, $m, $d] = explode('-', $date);

                // mesi di differenza
                $firstDateUnixTime = mktime(0, 0, 0, (int) $m, (int) $d, (int) $y);
                $monthDiff = (mktime(0, 0, 0) - $firstDateUnixTime) / 2628000;
                if ($monthDiff) {
                    $average = $rs[$categoryId]['amount'] / $monthDiff;
                }
            }
            $data[] = [
                'average' => $average,
                'description' => $row['description'],
                'id' => $row['id'],
                'active' => $row['active'],
            ];
        }
        return $data;
    }

    /**
     * Get the averages for categories
     *
     * @param int $userId
     * @param bool|null $active null for all
     * @return array<int, array>
     */
    public function oldestMovements(int $userId, bool $active = null): array
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c.id, c.description, MIN(m.date) AS date, c.active')
            ->from(Category::class, 'c', 'c.id')
            ->leftJoin(Movement::class, 'm', 'WITH', 'c.id=m.category')
            ->where("c.user=$userId")
            ->groupBy('c.id');
        if ($active !== null) {
            $qb->andWhere("c.active=:active")->setParameter(':active', $active);
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $categoryId
     * @return float
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSum(int $categoryId)
    {
        return (float) $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(m.amount)')
            ->from(Category::class, 'c', 'c.id')
            ->innerJoin(Movement::class, 'm', 'WITH', 'c.id=m.category')
            ->where("c.id=$categoryId")
            ->getQuery()
            ->getSingleScalarResult();
    }
}
