<?php

/**
 *
 * @author fabio.ventura
 */
namespace Accantona\Model;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class VariabileTable
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

    public function get($id)
    {
        $id  = (int) $id;
        $rs = $this->tableGateway->select(array('id' => $id));
        $row = $rs->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function save(Valiabile $variabile)
    {
        $data = array('id' => $variabile->id, 'descrizione' => $variabile->descrizione);

        $id = (int) $variabile->id;
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

}
