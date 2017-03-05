<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Zend\Debug\Debug;

class CategoryRepository extends EntityRepository
{

    /**
     * Get the averages for categories
     *
     * @param int $userId
     * @param \DateTime $since
     * @return array
     */
    public function getAverages($userId, \DateTime $since)
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('SUM(m.amount) AS amount, MIN(m.date) AS first_date, c.id, c.descrizione')
            ->from('Application\Entity\Moviment', 'm')
            ->innerJoin('m.category', 'c')
            ->where('c.userId=:userId')
            ->andWhere('c.status=:status')
            ->andWhere('m.date >= :since')
            ->setParameters([
                ':since'  => $since->format('Y-m-d'),
                ':status' => 1,
                ':userId' => $userId,
            ])
            ->groupBy('c.id');

        $rs = $qb->getQuery()->getResult();

        $data = array();
        foreach ($rs as $row) {
            list($y, $m, $d) = explode('-', $row['first_date']);

            //mesi di differenza
            $monthDiff = (mktime(0, 0, 0) - mktime(0, 0, 0, $m, $d, $y)) / 2628000;
            if ($monthDiff) {
                $data[] = array('average' => $row['amount'] / $monthDiff, 'description' => $row['descrizione']);
            }

        }
        return $data;
    }
}