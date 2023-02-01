<?php

declare(strict_types=1);

namespace Application\Repository;

use Application\Entity\Movement;
use Application\Entity\Provision;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class ProvisionRepository extends EntityRepository
{
    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getBalance(int $userId): float
    {
        $em = $this->getEntityManager();
        $qb = $em
            ->createQueryBuilder()
            ->select('COALESCE(SUM(Provision.amount), 0) AS total')
            ->from(Provision::class, 'Provision')
            ->where('Provision.user=:userId')
            ->setParameter(':userId', $userId);

        /** @var MovementRepository $movementRepository */
        $movementRepository = $em->getRepository(Movement::class);

        return $qb->getQuery()->getSingleScalarResult() + $movementRepository->getTotalExpense($userId);
    }

    /**
     * @param array<string, mixed> $params
     * @return array<int, mixed>
     */
    public function search(array $params = []): array
    {
        $cleanParams  = [];
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('Provision')
            ->from(Provision::class, 'Provision')
            ->where('1=1');

        if (!empty($params['userId'])) {
            $qb->andWhere('Provision.user = :userId');
            $cleanParams['userId'] = $params['userId'];
        }
        if (!empty($params['dateMin'])) {
            $qb->andWhere('Provision.date >= :dateMin');
            $cleanParams['dateMin'] = $params['dateMin'];
        }
        if (!empty($params['dateMax'])) {
            $qb->andWhere('Provision.date <= :dateMax');
            $cleanParams['dateMax'] = $params['dateMax'];
        }
        if (!empty($params['description'])) {
            $qb->andWhere('Provision.description LIKE :description');
            $cleanParams['description'] = '%' . $params['description'] . '%';
        }

        $query = $qb->setParameters($cleanParams)->orderBy('Provision.date', 'DESC')->getQuery();
        return $query->getResult();
    }

    /**
     * @param int $userId
     * @return float
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getSum(int $userId): float
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder()
                 ->select('COALESCE(SUM(Provision.amount), 0) AS total')
                 ->from(Provision::class, 'Provision')
                 ->where('Provision.user=:userId')
                 ->setParameter(':userId', $userId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }
}
