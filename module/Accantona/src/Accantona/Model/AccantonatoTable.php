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

    public function save(Accantonato $spesa)
    {
        $data = array(
            'id_categoria' => $spesa->id_categoria,
            'valuta' => $spesa->valuta,
            'importo' => $spesa->importo,
            'descrizione' => $spesa->descrizione,
        );

        $id = (int) $spesa->id;
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

    public function deleteAnagrafica($idAnagrafica)
    {
        $this->tableGateway->delete(array('id' => (int) $idAnagrafica));
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

}
