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

    public function getAnagrafica($idAnagrafica)
    {
        $idAnagrafica  = (int) $idAnagrafica;
        $rowset = $this->tableGateway->select(array('id_anagrafica' => $idAnagrafica));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $idAnagrafica");
        }
        return $row;
    }

    public function saveAnagrafica(Anagrafica $anagrafica)
    {
        $data = array(
            'id_azienda' => $anagrafica->id_azienda,
            'cd_societa' => $anagrafica->cd_societa,
        );

        $idAnagrafica = (int) $anagrafica->id_anagrafica;
        if ($idAnagrafica == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getAnagrafica($idAnagrafica)) {
                $this->tableGateway->update($data, array('id_anagrafica' => $idAnagrafica));
            } else {
                throw new \Exception('Anagrafica id does not exist');
            }
        }
    }

    public function deleteAnagrafica($idAnagrafica)
    {
        $this->tableGateway->delete(array('id' => (int) $idAnagrafica));
    }

    public function joinFetchAll($where)
    {
        $sqlSelect = $this->tableGateway->getSql()->select()
            ->join('categorie', 'categorie.id=spese.id_categoria', array('categoryDescription' => 'descrizione'))
            ->where($where);
        return $this->tableGateway->selectWith($sqlSelect);
    }

}
