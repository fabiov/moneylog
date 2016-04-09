<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AnagraficaTable
 *
 * @author fabio.ventura
 */
namespace Accantona\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Debug\Debug;

class AccantonatoTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * @param  Where|\Closure|string|array|Predicate\PredicateInterface $predicate
     * @return mixed
     */
    public function fetchAll($where)
    {
        $resultSet = $this->tableGateway->select($where); //->where($where);
        return $resultSet;
    }

    public function get($idAnagrafica)
    {
        $idAnagrafica  = (int) $idAnagrafica;
        $rowset = $this->tableGateway->select(array('id_anagrafica' => $idAnagrafica));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $idAnagrafica");
        }
        return $row;
    }

    /**
     * @param Accantonato $accantonato
     * @throws \Exception
     */
    public function save(Accantonato $accantonato)
    {
        $data = array(
            'userId' => $accantonato->userId,
            'valuta' => $accantonato->valuta,
            'importo' => $accantonato->importo,
            'descrizione' => $accantonato->descrizione,
        );

        $id = (int) $accantonato->id;
        if ($id) {
            if ($this->get($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Accantonato id does not exist');
            }
        } else {
            $this->tableGateway->insert($data);
        }
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    function getAvgPerCategories()
    {
        //calcolo le spese medie per ogni categoria
        $sqlSum = <<< eoc
SELECT sum(importo) AS somma, min(valuta) AS prima_valuta, id_categoria, categorie.descrizione FROM spese
INNER JOIN categorie ON categorie.id=spese.id_categoria WHERE date('now', '-30 months') <= valuta GROUP BY id_categoria
eoc;
        $statement = $this->tableGateway->adapter->createStatement($sqlSum);
        $rs = $statement->execute();

        $data = array();
        foreach ($rs as $row) {
            list($y, $m, $d) = explode('-', $row['prima_valuta']);
            $monthDiff = (mktime(0, 0, 0) - mktime(0, 0, 0, $m, $d, $y)) / 2628000;
            if ($monthDiff) {
                $data[] = array('average' => $row['somma'] / $monthDiff, 'description' => $row['descrizione']);
            }

        }
        return $data;
    }

    /**
     * @return float
     */
    function getSum()
    {
        $data = $this->tableGateway->adapter->createStatement('SELECT SUM(importo) AS sum FROM accantonati')->execute()
            ->next();
        return (float) $data['sum'];
    }

}
