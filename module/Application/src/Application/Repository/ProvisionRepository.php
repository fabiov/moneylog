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
            ->select('COALESCE(SUM(Provision.importo), 0) AS total')
            ->from(Provision::class, 'Provision')
            ->where('Provision.user=:userId')
            ->setParameter(':userId', $userId);

        /** @var \Application\Repository\MovementRepository $movementRepository */
        $movementRepository = $em->getRepository(Movement::class);

        return $qb->getQuery()->getSingleScalarResult() + $movementRepository->getTotalExpense($userId);
    }

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
            $qb->andWhere('Provision.valuta >= :dateMin');
            $cleanParams['dateMin'] = $params['dateMin'];
        }
        if (!empty($params['dateMax'])) {
            $qb->andWhere('Provision.valuta <= :dateMax');
            $cleanParams['dateMax'] = $params['dateMax'];
        }
        if (!empty($params['description'])) {
            $qb->andWhere('Provision.descrizione LIKE :description');
            $cleanParams['description'] = '%' . $params['description'] . '%';
        }

        $query = $qb->setParameters($cleanParams)->orderBy('Provision.valuta', 'DESC')->getQuery();
        return $query->getResult();
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
                 ->select('COALESCE(SUM(Provision.importo), 0) AS total')
                 ->from(Provision::class, 'Provision')
                 ->where('Provision.user=:userId')
                 ->setParameter(':userId', $userId);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }
}
