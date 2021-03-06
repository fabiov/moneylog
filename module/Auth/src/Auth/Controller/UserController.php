<?php

namespace Auth\Controller;

use Application\Entity\User;
use Auth\Form\AuthForm;
use Auth\Form\ChangePasswordForm;
use Auth\Form\Filter\ChangePasswordFilter;
use Auth\Form\Filter\LoginFilter;
use Auth\Form\Filter\UserFilter;
use Auth\Form\UserForm;
use Auth\Service\AuthManager;
use Doctrine\ORM;
use Laminas\Authentication\Result;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * @method \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger flashMessenger()
 */
class UserController extends AbstractActionController
{
    /**
     * @var ORM\EntityManager
     */
    private $em;

    /**
     * @var ?\stdClass
     */
    private $user;

    /**
     * Auth manager.
     * @var AuthManager
     */
    private $authManager;

    /**
     * UserController constructor.
     *
     * @param ?\stdClass $user
     * @param ORM\EntityManager $em
     * @param AuthManager $authManager
     */
    public function __construct(?\stdClass $user, ORM\EntityManager $em, AuthManager $authManager)
    {
        $this->authManager = $authManager;
        $this->em          = $em;
        $this->user        = $user;
    }

    /**
     * @throws ORM\OptimisticLockException
     * @throws ORM\TransactionRequiredException
     * @throws ORM\ORMException
     */
    public function updateAction()
    {
        if (!$this->user) {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }

        /** @var ?User $user */
        $user = $this->em->find(User::class, $this->user->id);
        if (!$user) {
            return $this->forward()->dispatch(UserController::class, ['action' => 'logout']);
        }
        $user->setInputFilter(new UserFilter());

        $form = new UserForm();
        $form->bind($user);
        $message = '';
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var User $data */
                $data = $form->getData();
                $user->setName($data->getName());
                $user->setSurname($data->getSurname());
                $this->em->persist($user);
                $this->em->flush();
                $message = 'I tuoi dati sono stati salvati correttamente';
            }
        }

        return ['form' => $form, 'message' => $message];
    }

    /**
     * @return Response|ViewModel
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
            $form->setInputFilter(new LoginFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {

                /** @var array $data */
                $data = $form->getData();

                // Perform login attempt.
                $result = $this->authManager->login($data['email'], $data['password'], $data['rememberme']);

                switch ($result->getCode()) {
                    case Result::SUCCESS:
                        return $this->redirect()->toRoute('accantona_recap');
                    case Result::FAILURE_IDENTITY_NOT_FOUND:
                    case Result::FAILURE_CREDENTIAL_INVALID:
                    default:
                        $messages = 'Invalid credentials';
                        break;
                }
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(['form' => $form, 'messages' => $messages]);
    }

    /**
     * @return Response
     */
    public function logoutAction(): Response
    {
        $this->authManager->logout();
        return $this->redirect()->toRoute('auth', ['action' => 'login']);
    }

    /**
     * @throws ORM\OptimisticLockException
     * @throws ORM\TransactionRequiredException
     * @throws ORM\ORMException
     */
    public function changePasswordAction()
    {
        if (!$this->user) {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }

        /** @var ?User $user */
        $user = $this->em->find(User::class, $this->user->id);
        if (!$user) {
            return $this->forward()->dispatch(UserController::class, ['action' => 'logout']);
        }

        $user->setInputFilter(new UserFilter());

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
                if (md5($data['current'] . $user->getSalt()) == $user->getPassword()) {
                    $user->setPassword(md5($data['password'] . $user->getSalt()));
                    $this->em->persist($user);
                    $this->em->flush();

                    $this->flashMessenger()->addMessage('La password è stata aggiornata con successo');
                    return $this->redirect()->toRoute('auth', ['action' => 'change-password']);
                } else {
                    $error   = true;
                    $message = 'Password non valida';
                }
            }
        }

        return ['error' => $error, 'form' => $form, 'message' => $message];
    }
}
