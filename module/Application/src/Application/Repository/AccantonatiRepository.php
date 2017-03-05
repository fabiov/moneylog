<?php

namespace Application\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class AccantonatiRepository extends EntityRepository
{

    public function getBalance($userId)
    {
        $em = $this->getEntityManager();
        // SELECT COALESCE(SUM(importo), 0) - (SELECT COALESCE(SUM(importo), 0)
        // FROM spese WHERE userId=1) FROM accantonati WHERE userId=1;
        $qb = $em
            ->createQueryBuilder()
            ->select('COALESCE(SUM(a.importo), 0) AS total')
            ->from('Application\Entity\Accantonati', 'a')
            ->where('a.userId=:userId')
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->getSingleScalarResult() + $em->getRepository('Application\Entity\Moviment')->getTotalExpense($userId);
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
            ->select('a')
            ->from('Application\Entity\Accantonati', 'a')
            ->where('1=1');

        if (!empty($params['userId'])) {
            $qb->andWhere('a.userId = :userId');
            $cleanParams['userId'] = $params['userId'];
        }
        if (!empty($params['dateMin'])) {
            $qb->andWhere('a.valuta >= :dateMin');
            $cleanParams['dateMin'] = $params['dateMin'];
        }
        if (!empty($params['dateMax'])) {
            $qb->andWhere('a.valuta <= :dateMax');
            $cleanParams['dateMax'] = $params['dateMax'];
        }
        if (!empty($params['description'])) {
            $qb->andWhere('a.descrizione LIKE :description');
            $cleanParams['description'] = '%' . $params['description'] . '%';
        }

        return $qb->setParameters($cleanParams)->orderBy('a.valuta', 'DESC')->getQuery()->getResult();
    }
}