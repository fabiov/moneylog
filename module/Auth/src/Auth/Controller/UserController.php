<?php
namespace Auth\Controller;

use Application\Entity\User;
use Auth\Form\AuthForm;
use Auth\Form\ChangePasswordForm;
use Auth\Form\Filter\ChangePasswordFilter;
use Auth\Form\Filter\UserFilter;
use Auth\Form\UserForm;
use Auth\Model\Auth;
use Auth\Service\AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Authentication\Storage\Session as SessionStorage;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * @var AuthAdapter
     */
    private $adapter;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \stdClass
     */
    private $user;

    /**
     * @var \stdClass
     */
    private $userData;

    /**
     * UserController constructor.
     * @param AuthAdapter $adapter
     * @param $user
     * @param $em
     * @param $userData
     */
    public function __construct(AuthAdapter $adapter, $user, $em, $userData)
    {
        $this->adapter  = $adapter;
        $this->em       = $em;
        $this->user     = $user;
        $this->userData = $userData;
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
                $this->userData->setName($user->name)->setSurname($user->surname);
                $message = 'I tuoi dati sono stati salvati correttamente';
            }
        }

        return ['form' => $form, 'message' => $message];
    }

    /**
     * @return \Zend\Http\Response|ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Zend\Authentication\Exception\ExceptionInterface
     */
    public function loginAction()
    {
        $auth = new AuthenticationService();
        $auth->setStorage(new SessionStorage());

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

                // You can set the service here but will be loaded only if this action called.
                $result = $this->adapter->setEmail($data['email'])->setPassword($data['password'])->authenticate();

                switch ($result->getCode()) {
                    case Result::SUCCESS:
                        $storage = $auth->getStorage();
                        $identity = (object) $result->getIdentity();
                        $storage->write($identity);
                        if ($data['rememberme']) {
                            $sessionManager = new \Zend\Session\SessionManager();
                            $sessionManager->rememberMe(604800); // 7 days
                        }

                        /* @var User $user */
                        $user = $this->em->find('Application\Entity\User', $identity->id);
                        $user->setLastLogin(new \DateTime());
                        $this->em->flush();

                        // save user info in session
                        $this->userData
                            ->setName($user->name)
                            ->setSurname($user->surname)
                            ->setSettings($user->setting);

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

        $form     = new ChangePasswordForm();
        $error    = false;
        $messages = $this->flashMessenger()->getMessages();
        $message  = $messages ? $messages[0] : '';
        $request  = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost();

            $form->setInputFilter(new ChangePasswordFilter());
            $form->setData($data);

            if ($form->isValid()) {

                if (md5($data['current'] . $user->salt) == $user->password) {
                    $user->password = md5($data['password'] . $user->salt);
                    $this->em->persist($user);
                    $this->em->flush();
                    $this->flashMessenger()->addMessage('La password è stata aggiornata con successo');
                    return $this->redirect()->toRoute('auth/default', ['controller' => 'user', 'action' => 'change-password']);
                } else {
                    $error   = true;
                    $message = 'Password non valida';
                }
            }
        }

        return ['error' => $error, 'form' => $form, 'message' => $message];
    }
}