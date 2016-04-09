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

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Debug\Debug;

class CategoriaTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll(array $where = array(), $order = '')
    {

        $resultSet = $this->tableGateway->select(function (Select $select) use($where, $order) {
            $select->where($where);
            if ($order) {
                $select->order($order);
            }
        });
        return $resultSet;
    }

    public function getCategoria($id)
    {
        $id  = (int) $id;
        $rs = $this->tableGateway->select(array('id' => $id));
        $row = $rs->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function save(Categoria $categoria)
    {
        $data = array('id' => $categoria->id, 'userId' => $categoria->userId, 'descrizione' => $categoria->descrizione);

        $id = (int) $categoria->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCategoria($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Anagrafica id does not exist');
            }
        }
    }

    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }

    public function deleteByAttributes(array $attributes)
    {
        //Debug::dump($attributes);die;
        $this->tableGateway->delete($attributes);
    }

}
