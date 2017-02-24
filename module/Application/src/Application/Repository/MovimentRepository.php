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
     * @param array $params
     * @return array
     */
    public function search(array $params = [])
    {
        $cleanParams  = [];
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('m')
            ->from('Application\Entity\Moviment', 'm')
            ->where('1=1')
            ->orderBy('m.date', 'DESC');

        if (!empty($params['accountId'])) {
            $qb->andWhere('m.accountId = :accountId');
            $cleanParams['accountId'] = $params['accountId'];
        }
        if (!empty($params['dateMin'])) {
            $qb->andWhere('m.date >= :dateMin');
            $cleanParams['dateMin'] = $params['dateMin'];
        }
        if (!empty($params['dateMax'])) {
            $qb->andWhere('m.date <= :dateMax');
            $cleanParams['dateMax'] = $params['dateMax'];
        }
        if (!empty($params['description'])) {
            $qb->andWhere('m.description LIKE :description');
            $cleanParams['description'] = '%' . $params['description'] . '%';
        }

        return $qb->setParameters($cleanParams)->orderBy('m.date', 'DESC')->getQuery()->getResult();
    }

}