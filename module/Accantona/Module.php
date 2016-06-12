<?php

/**
 * @author fabio.ventura
 */
namespace Accantona;

use Accantona\Model\Accantonato;
use Accantona\Model\AccantonatoTable;

use Accantona\Model\Spesa;
use Accantona\Model\SpesaTable;

use Accantona\Model\Categoria;
use Accantona\Model\CategoriaTable;

use Accantona\Model\Variabile;
use Accantona\Model\VariabileTable;

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
                'Accantona\Model\SpesaTable' => function($sm) {
                    $tableGateway = $sm->get('SpesaTableGateway');
                    $table = new SpesaTable($tableGateway);
                    return $table;
                },
                'SpesaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Spesa());
                    return new TableGateway('spese', $dbAdapter, null, $resultSetPrototype);
                },
                'Accantona\Model\VariabileTable' => function($sm) {
                    $tableGateway = $sm->get('VariabileTableGateway');
                    $table = new VariabileTable($tableGateway);
                    return $table;
                },
                'VariabileTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Variabile());
                    return new TableGateway('variabili', $dbAdapter, null, $resultSetPrototype);
                },
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
                'Accantona\Model\CategoriaTable' => function($sm) {
                    $tableGateway = $sm->get('CategoriaTableGateway');
                    $table = new CategoriaTable($tableGateway);
                    return $table;
                },
                'CategoriaTableGateway' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Categoria());
                    return new TableGateway('Category', $dbAdapter, null, $resultSetPrototype);
                },
            ),
        );
    }

}
