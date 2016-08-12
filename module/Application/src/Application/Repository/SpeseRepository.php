<?php

namespace Application\Repository;

use Doctrine\ORM\EntityRepository;
use Zend\Debug\Debug;

class SpeseRepository extends EntityRepository
{

    /**
     * @param string $where
     * @param array $params
     * @return array
     */
    public function getSpese($where = '', array $params = array())
    {
        $qb = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('spese')
            ->from('Application\Entity\Spese', 'spese');
        if ($where) {
            $qb->where($where);
        }
        if ($params) {
            $qb->setParameters($params);
        }

        return $qb->orderBy('spese.valuta', 'DESC')->getQuery()->getResult();
    }

}