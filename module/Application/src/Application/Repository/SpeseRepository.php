<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;

class SpeseRepository extends EntityRepository
{

    /**
     * @param array $params
     * @return array
     */
    public function search($params = [])
    {
        $cleanParams = [];
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('spese')
            ->from('Application\Entity\Spese', 'spese')
            ->where('1=1');

        if (!empty($params['categoryId'])) {
            $cleanParams['categoryId'] = $params['categoryId'];
            $qb->andWhere('spese.category = :categoryId');
        }
        if (!empty($params['dateMax'])) {
            $cleanParams['dateMax'] = $params['dateMax'];
            $qb->andWhere('spese.valuta <= :dateMax');
        }
        if (!empty($params['dateMin'])) {
            $cleanParams['dateMin'] = $params['dateMin'];
            $qb->andWhere('spese.valuta >= :dateMin');
        }
        if (!empty($params['description'])) {
            $cleanParams['description'] = '%' . $params['description'] . '%';
            $qb->andWhere('spese.descrizione LIKE :description');
        }
        if (!empty($params['userId'])) {
            $cleanParams['userId'] = $params['userId'];
            $qb->andWhere('spese.userId = :userId');
        }

        return $qb->setParameters($cleanParams)->orderBy('spese.valuta', 'DESC')->getQuery()->getResult();
    }

}