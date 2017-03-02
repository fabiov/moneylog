<?php
namespace Auth\Controller;

use Application\Entity\User;
use Auth\Form\AuthForm;
use Auth\Form\ChangePasswordForm;
use Auth\Form\Filter\ChangePasswordFilter;
use Auth\Form\Filter\UserFilter;
use Auth\Form\UserForm;
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
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \stdClass
     */
    private $user;

    /**
     * IndexController constructor.
     * @param Adapter $dbAdapter
     */
    public function __construct(Adapter $dbAdapter, $user, $em)
    {
        $this->dbAdapter = $dbAdapter;
        $this->em        = $em;
        $this->user      = $user;

    }

    public function updateAction()
    {
        /* @var User $user*/
        $user = $this->em->find('Application\Entity\User', $this->user->id)->setInputFilter(new UserFilter());
        if (!$user) {
            return $this->forward()->dispatch('Auth\Controller\User', ['action' => 'logout']);
        }

        $form = new UserForm();
        $form->bind($user);
        $message = '';
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();
                $message = 'I tuoi dati sono stati salvati correttamente';
            }
        }

        return ['form' => $form, 'message' => $message];
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
                        $identity = $authAdapter->getResultRowObject(null, ['password', 'registrationToken', 'salt']);
                        $storage->write($identity);
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

    public function changePasswordAction()
    {
        /* @var User $user*/
        $user = $this->em->find('Application\Entity\User', $this->user->id)->setInputFilter(new UserFilter());
        if (!$user) {
            return $this->forward()->dispatch('Auth\Controller\User', ['action' => 'logout']);
        }

        $form    = new ChangePasswordForm();
        $error   = false;
        $message = '';
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            $form->setInputFilter(new ChangePasswordFilter());
            $form->setData($data);

            if ($form->isValid()) {

                if (md5($data['current'] . $user->salt) == $user->password) {
                    $user->password = md5($data['password'] . $user->salt);
                    $this->em->persist($user);
                    $this->em->flush();
                    $message = 'La password è stata aggiornata con successo';
                } else {
                    $error   = true;
                    $message = 'Password non valida';
                }
            }
        }

        return ['error' => $error, 'form' => $form, 'message' => $message];
    }

}