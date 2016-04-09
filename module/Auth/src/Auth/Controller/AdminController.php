<?php
namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Db\TableGateway\TableGateway;

use Auth\Form\UserForm;
use Auth\Form\UserFilter;

class AdminController extends AbstractActionController
{
    protected $userTable = null;

    // R - retrieve = Index
    public function indexAction()
    {
        return new ViewModel(array('rowset' => $this->getUserTable()->select()));
    }

    // C - Create
    public function createAction()
    {
        $form = new UserForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new UserFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                unset($data['submit']);
                if (empty($data['usr_registration_date'])) $data['usr_registration_date'] = '2013-07-19 12:00:00';
                $this->getUserTable()->insert($data);
                return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));
            }
        }
        return new ViewModel(array('form' => $form));
    }

    // U - Update
    public function updateAction()
    {
        $id = $this->params()->fromRoute('id');
        if (!$id) return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));
        $form = new UserForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new UserFilter());
            $form->setData($request->getPost());
             if ($form->isValid()) {
                $data = $form->getData();
                unset($data['submit']);
                if (empty($data['usr_registration_date'])) $data['usr_registration_date'] = '2013-07-19 12:00:00';
                $this->getUserTable()->update($data, array('usr_id' => $id));
                return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));
            }
        }
        else {
            $form->setData($this->getUserTable()->select(array('usr_id' => $id))->current());
        }

        return new ViewModel(array('form' => $form, 'id' => $id));
    }

    // D - delete
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id');
        if ($id) {
            $this->getUserTable()->delete(array('usr_id' => $id));
        }

        return $this->redirect()->toRoute('auth/default', array('controller' => 'admin', 'action' => 'index'));
    }

    public function getUserTable()
    {
        // I have a Table data Gateway ready to go right out of the box
        if (!$this->userTable) {
            $this->userTable = new TableGateway('user', $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
        }
        return $this->userTable;
    }
}