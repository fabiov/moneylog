<?php
namespace Auth\Controller;

use Zend\Debug\Debug;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Zend\Authentication\Result;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\Session as SessionStorage;

use Zend\Db\Adapter\Adapter as DbAdapter;

use Zend\Authentication\Adapter\DbTable as AuthAdapter;

use Auth\Model\Auth;
use Auth\Form\AuthForm;

class IndexController extends AbstractActionController
{
    
    public function indexAction()
    {
        return new ViewModel();
    }

    public function loginAction()
    {
        $form = new AuthForm();
        $form->get('submit')->setValue('Login');
        $messages = null;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $authFormFilters = new Auth();
            $form->setInputFilter($authFormFilters->getInputFilter());	
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $sm = $this->getServiceLocator();
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');

                $authAdapter = new AuthAdapter(
                    $dbAdapter,
                    'user', // there is a method setTableName to do the same
                    'username', // there is a method setIdentityColumn to do the same
                    'password', // there is a method setCredentialColumn to do the same
                    "MD5(CONCAT(?, salt)) AND active=1" // setCredentialTreatment(parametrized string) 'MD5(?)'
                );
                $authAdapter->setIdentity($data['username'])->setCredential($data['password']);

                $auth = new AuthenticationService();
                // or prepare in the globa.config.php and get it from there. Better to be in a module, so we can replace
                // in another module.
                // $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');

                // You can set the service here but will be loaded only if this action called.
                // $sm->setService('Zend\Authentication\AuthenticationService', $auth);
                $result = $auth->authenticate($authAdapter);

                switch ($result->getCode()) {
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                        // do stuff for nonexistent identity
                        break;
                    case Result::FAILURE_CREDENTIAL_INVALID:
                        // do stuff for invalid credential
                        break;
                    case Result::SUCCESS:
                        $storage = $auth->getStorage();
                        $storage->write($authAdapter->getResultRowObject(null, 'password'));
                        $time = 1209600; // 14 days 1209600/3600 = 336 hours => 336/24 = 14 days
                        if ($data['rememberme']) {
                            $sessionManager = new \Zend\Session\SessionManager();
                            $sessionManager->rememberMe($time);
                        }
                        break;
                    default:
                        // do stuff for other failure
                        break;
                }
                foreach ($result->getMessages() as $message) {
                    $messages .= "$message\n"; 
                }
            }
        }
        return new ViewModel(array('form' => $form, 'messages' => $messages));
    }

    public function logoutAction()
    {
        $auth = new AuthenticationService();
        // or prepare in the globa.config.php and get it from there
        // $auth = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');

        $auth->clearIdentity();
        $sessionManager = new \Zend\Session\SessionManager();
        $sessionManager->forgetMe();

        return $this->redirect()->toRoute('auth/default', array('controller' => 'index', 'action' => 'login'));
    }

}