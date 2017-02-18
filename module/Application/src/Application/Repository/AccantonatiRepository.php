<?php

namespace Application\Repository;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class AccantonatiRepository extends EntityRepository
{

    public function getBalance($userId)
    {
        // SELECT COALESCE(SUM(col_a), 0) - (SELECT COALESCE(SUM(col_b), 0) FROM tab_b WHERE user_id=1) FROM tab_a WHERE user_id=1;
        $qbA =  $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(a.importo), 0) AS total')
            ->from('Application\Entity\Accantonati', 'a')
            ->where('a.userId=:userId')
            ->setParameter(':userId', $userId);

        $qbS =  $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COALESCE(SUM(s.importo), 0) AS total')
            ->from('Application\Entity\Spese', 's')
            ->where('s.userId=:userId')
            ->setParameter(':userId', $userId);

        return $qbA->getQuery()->getSingleScalarResult() - $qbS->getQuery()->getSingleScalarResult();
    }

}