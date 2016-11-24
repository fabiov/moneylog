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

class SpesaTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
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

    public function save(Spesa $spesa)
    {
        $data = array(
            'userId' => $spesa->userId,
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
                throw new \Exception('Spesa id does not exist');
            }
        } else {
            $this->tableGateway->insert($data);
        }
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function joinFetchAll($where)
    {
        $sqlSelect = $this->tableGateway->getSql()->select()
            ->join('Category', 'Category.id=spese.id_categoria', array('categoryDescription' => 'descrizione'))
            ->where($where);
        return $this->tableGateway->selectWith($sqlSelect);
    }

    function getAvgPerCategories($userId)
    {
        //calcolo le spese medie per ogni categoria
        $sqlSum = <<< eoc
SELECT sum(importo) AS somma, min(valuta) AS prima_valuta, id_categoria, Category.descrizione FROM spese
INNER JOIN Category ON Category.id=spese.id_categoria AND spese.userId=Category.userId
WHERE date_sub(now(), interval 30 month) <= valuta AND spese.userId=$userId AND Category.status=1
GROUP BY id_categoria
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
     * @param int $userId
     * @return float
     */
    function getSum($userId)
    {
        $data = $this->tableGateway->adapter
            ->createStatement("SELECT SUM(importo) AS sum FROM spese WHERE userId=$userId")
            ->execute()
            ->next();
        return (float) $data['sum'];
    }

}
