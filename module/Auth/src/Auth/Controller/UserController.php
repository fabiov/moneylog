<?php
namespace Auth\Controller;

use Application\Entity\User;
use Auth\Form\AuthForm;
use Auth\Form\ChangePasswordForm;
use Auth\Form\Filter\ChangePasswordFilter;
use Auth\Form\Filter\UserFilter;
use Auth\Form\UserForm;
use Auth\Model\Auth;
use Auth\Model\UserTable;
use Auth\Service\AuthManager;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * @var EntityManager
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
     * Auth manager.
     * @var AuthManager
     */
    private $authManager;

    /**
     * UserController constructor.
     *
     * @param $user
     * @param $em
     * @param $userData
     * @param $authManager
     */
    public function __construct($user, EntityManager $em, UserTable $userData, AuthManager $authManager)
    {
        $this->authManager = $authManager;
        $this->em          = $em;
        $this->user        = $user;
        $this->userData    = $userData;
    }

    public function updateAction()
    {
        /* @var User $user*/
        $user = $this->em->find(\Application\Entity\User::class, $this->user->id)->setInputFilter(new UserFilter());
        if (!$user) {
            return $this->forward()->dispatch(\Auth\Controller\User::class, ['action' => 'logout']);
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
     * @throws \Exception
     */
    public function loginAction()
    {
        if ($this->user) {
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

                // Perform login attempt.
                $result = $this->authManager->login($data['email'], $data['password'], $data['rememberme']);

                switch ($result->getCode()) {
                    case Result::SUCCESS:
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

    /**
     * @return \Zend\Http\Response
     * @throws \Exception
     */
    public function logoutAction()
    {
        $this->authManager->logout();
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