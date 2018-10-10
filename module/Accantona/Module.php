<?php
/**
 * @author fabio.ventura
 */
namespace Accantona;

use Accantona\Model\Accantonato;
use Accantona\Model\AccantonatoTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Accantona\Model\AccantonatoTable' => function($sm) {
                    $tableGateway = $sm->get('AccantonatoTableGateway');
                    $table = new AccantonatoTable($tableGateway);
                    return $table;
                },
                'AccantonatoTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Accantonato());
                    return new TableGateway('accantonati', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
