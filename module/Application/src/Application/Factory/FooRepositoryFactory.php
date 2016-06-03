<?php

namespace Application\Factory\AccountRepository;

use Application\Repository\FooRepository;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AccountRepositoryFactory implements FactoryInterface
{

    public function createService(ServiceLocatorInterface $sl)
    {
        $em     = $sl->get('Doctrine\ORM\EntityManager');
        $meta   = $em->getClassMetadata('Account');
        $logger = $sl->get('MyLoggerService');

        $repository = new FooRepository($em, $meta, $logger);
        return $repository;
    }

}