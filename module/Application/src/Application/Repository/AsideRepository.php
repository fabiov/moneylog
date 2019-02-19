<?php

namespace Application\Repository;

use Application\Entity\Aside;
use Application\Entity\Movement;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class AsideRepository extends EntityRepository
{

    public function getBalance($userId)
    {
        $em = $this->getEntityManager();
        $qb = $em
            ->createQueryBuilder()
            ->select('COALESCE(SUM(a.importo), 0) AS total')
            ->from(Aside::class, 'a')
            ->where('a.userId=:userId')
            ->setParameter(':userId', $userId);

        return $qb->getQuery()->getSingleScalarResult() + $em->getRepository(Movement::class)->getTotalExpense($userId);
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
            ->from(Aside::class, 'a')
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

    /**
     * @param $userId
     * @return float
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSum($userId) 
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
                 ->select('COALESCE(SUM(a.importo), 0) AS total')
                 ->from(Aside::class, 'a')
                 ->where('a.userId=:userId')
                 ->setParameter(':userId', $userId);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
