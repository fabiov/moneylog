<?php
namespace Auth\Model;

use Laminas\Db\TableGateway\TableGateway;

class UserTable
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    /**
     * @throws \Exception
     */
    public function getUser($id)
    {
        $id  = (int) $id;
        $row = $this->tableGateway->select(['id' => $id])->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function changePassword($id, $password)
    {
        $data = ['password' => $password];
        $this->tableGateway->update($data, ['usr_id' => (int) $id]);
    }

    public function saveUser(Auth $auth)
    {
        // for Laminas\Db\TableGateway\TableGateway we need the data in array not object
        $data = [
            'email'             => $auth->email,
            'name'              => $auth->name,
            'password'          => $auth->password,
            'salt'              => $auth->salt,
            'status'            => $auth->status,
            'role'              => $auth->role,
            'registrationToken' => $auth->registrationToken,
        ];

        $id = (int) $auth->id;
        if ($id) {
            if ($this->getUser($id)) {
                $this->tableGateway->update($data, ['id' => $id]);
            } else {
                throw new \Exception('Form id does not exist');
            }
        } else {
            $this->tableGateway->insert($data);
        }
    }

    public function deleteUser($id)
    {
        $this->tableGateway->delete(['usr_id' => $id]);
    }

}
