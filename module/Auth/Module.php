<?php
namespace Auth; 

use Laminas\Mail\Transport\Smtp;
use Laminas\Mail\Transport\SmtpOptions;
use Laminas\ServiceManager\ServiceManager;

class Module
{

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig(): array
    {
        return [
            'Laminas\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getServiceConfig(): array
    {
        return [
            'factories' => [
                // Add this for SMTP transport
                'mail.transport' => function (ServiceManager $serviceManager) {
                    $config = $serviceManager->get('Config');
                    $transport = new Smtp();
                    $transport->setOptions(new SmtpOptions($config['mail']['transport']['options']));
                    return $transport;
                },
            ],
        ];
    }
}
