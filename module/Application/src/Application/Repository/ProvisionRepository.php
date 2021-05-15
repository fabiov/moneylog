<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Entity\Provision;
use Application\Entity\Movement;
use Doctrine\ORM\EntityRepository;

class ProvisionRepository extends EntityRepository
{
    public function getBalance(int $userId): float
    {
        $em = $this->getEntityManager();
        $qb = $em
            ->createQueryBuilder()
            ->select('COALESCE(SUM(a.importo), 0) AS total')
            ->from(Provision::class, 'a')
            ->where('a.userId=:userId')
            ->setParameter(':userId', $userId);

        /** @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $em->getRepository(Movement::class);

        return $qb->getQuery()->getSingleScalarResult() + $movementRepository->getTotalExpense($userId);
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
            ->from(Provision::class, 'a')
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
     * @param int $userId
     * @return float
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSum(int $userId): float
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
                 ->select('COALESCE(SUM(a.importo), 0) AS total')
                 ->from(Provision::class, 'a')
                 ->where('a.userId=:userId')
                 ->setParameter(':userId', $userId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }
}
