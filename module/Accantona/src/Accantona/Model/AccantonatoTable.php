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

    /**
     * @param int $userId
     * @return float
     */
    function getSum($userId)
    {
        $data = $this->tableGateway->adapter
            ->createStatement("SELECT SUM(importo) AS sum FROM accantonati WHERE userId=$userId")
            ->execute()
            ->next();
        return (float) $data['sum'];
    }

}
