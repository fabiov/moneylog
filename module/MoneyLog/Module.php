<?php

declare(strict_types=1);

namespace MoneyLog;

use Laminas\Loader\ClassMapAutoloader;
use Laminas\Loader\StandardAutoloader;
use Laminas\ModuleManager\Feature\AutoloaderProviderInterface;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    /**
     * @return array<string, array>
     */
    public function getAutoloaderConfig(): array
    {
        return [
            ClassMapAutoloader::class => [
                __DIR__ . '/autoload_classmap.php',
            ],
            StandardAutoloader::class => [
                'namespaces' => [__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__],
            ],
        ];
    }

    /**
     * @return array<string, array>
     */
    public function getConfig(): array
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array<string, array>
     */
    public function getServiceConfig(): array
    {
        return ['factories' => []];
    }
}
