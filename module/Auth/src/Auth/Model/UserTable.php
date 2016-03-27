<?php
namespace Auth\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable
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

    public function getUser($id)
    {
        $id  = (int) $id;
        $row = $this->tableGateway->select(array('id' => $id))->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getUserByToken($token)
    {
        $row = $this->tableGateway->select(array('registrationToken' => $token))->current();
        if (!$row) {
            throw new \Exception("Could not find row $token");
        }
        return $row;
    }

    public function activateUser($id)
    {
        $this->tableGateway->update(array('status' => 1), array('id' => $id));
    }

    public function getUserByEmail($usr_email)
    {
        $rowset = $this->tableGateway->select(array('usr_email' => $usr_email));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $usr_email");
        }
        return $row;
    }

    public function changePassword($id, $password)
    {
        $data = array('password' => $password);
        $this->tableGateway->update($data, array('usr_id' => (int) $id));
    }

    public function saveUser(Auth $auth)
    {
        // for Zend\Db\TableGateway\TableGateway we need the data in array not object
        $data = array(
            'email'             => $auth->email,
            'name'              => $auth->name,
            'password'          => $auth->password,
            'salt'              => $auth->salt,
            'status'            => $auth->status,
            'role'              => $auth->role,
            'registrationToken' => $auth->registrationToken,
        );

        $id = (int) $auth->id;
        if ($id) {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        } else {
            $this->tableGateway->insert($data);
        }
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(array('usr_id' => $usr_id));
    }	
}