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
