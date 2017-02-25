<?php
namespace Auth\Controller;

use Auth\Form\AuthForm;
use Auth\Model\Auth;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Db\Adapter\Adapter;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * @var Adapter
     */
    private $dbAdapter;

    /**
     * IndexController constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }

    public function updateAction()
    {
        return new ViewModel();
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     */
    public function loginAction()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()) {
            return $this->redirect()->toRoute('accantona_recap');
        }
        $form = new AuthForm();
        $messages = null;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $authFormFilters = new Auth();
            $form->setInputFilter($authFormFilters->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();

                $authAdapter = new AuthAdapter(
                    $this->dbAdapter,
                    'User', // there is a method setTableName to do the same
                    'email',
                    'password', // there is a method setCredentialColumn to do the same
                    "MD5(CONCAT(?, salt)) AND status=1" // setCredentialTreatment(parametrized string) 'MD5(?)'
                );
                $authAdapter->setIdentity($data['email'])->setCredential($data['password']);

                // You can set the service here but will be loaded only if this action called.
                $result = $auth->authenticate($authAdapter);

                switch ($result->getCode()) {
                    case Result::SUCCESS:
                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(null, 'password'));
                        if ($data['rememberme']) {
                            $sessionManager = new \Zend\Session\SessionManager();
                            $sessionManager->rememberMe(604800); // 7 days
                        }
                        return $this->redirect()->toRoute('accantona_recap');
                        break;
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                    case Result::FAILURE_CREDENTIAL_INVALID:
                    default:
                        $messages = 'Invalid credentials';
                        break;
                }
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(array('form' => $form, 'messages' => $messages));
    }

    public function logoutAction()
    {
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionManager->forgetMe();

        return $this->redirect()->toRoute('auth/default', array('action' => 'login'));
    }
}