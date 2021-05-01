<?php
namespace Auth\Service\Factory;

use Auth\Service\AuthAdapter;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Storage\Session;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthenticationService
    {
        $authStorage = new Session('Zend_Auth', 'session');

        // Create the service and inject dependencies into its constructor.
        return new AuthenticationService($authStorage, $container->get(AuthAdapter::class));
    }
}
