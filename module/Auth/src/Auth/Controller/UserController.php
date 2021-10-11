<?php

namespace Auth\Controller;

use Application\Entity\User;
use Auth\Form\AuthForm;
use Auth\Form\ChangePasswordForm;
use Auth\Form\Filter\ChangePasswordFilter;
use Auth\Form\Filter\LoginFilter;
use Auth\Form\Filter\UserFilter;
use Auth\Form\UserForm;
use Auth\Model\LoggedUser;
use Auth\Service\AuthManager;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Authentication\Result;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;

/**
 * @method \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger flashMessenger()
 */
class UserController extends AbstractActionController
{
    private EntityManagerInterface $em;

    private ?LoggedUser $user;

    private AuthManager $authManager;

    public function __construct(?LoggedUser $user, EntityManagerInterface $em, AuthManager $authManager)
    {
        $this->authManager = $authManager;
        $this->em          = $em;
        $this->user        = $user;
    }

    /**
     * @return array|\Laminas\Http\Response|mixed
     */
    public function updateAction()
    {
        if (!$this->user) {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }

        /** @var ?User $user */
        $user = $this->em->find(User::class, $this->user->getId());
        if (!$user) {
            return $this->forward()->dispatch(UserController::class, ['action' => 'logout']);
        }

        $form = new UserForm();
        $form->setData(['name' => $user->getName(), 'surname' => $user->getSurname()]);

        $message = '';
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new UserFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var array<string, string> $data */
                $data = $form->getData();
                $user->setName($data['name']);
                $user->setSurname($data['surname']);
                $this->em->persist($user);
                $this->em->flush();
                $message = 'I tuoi dati sono stati salvati correttamente';
            }
        }

        return ['form' => $form, 'message' => $message];
    }

    /**
     * @return array<string, mixed>|\Laminas\Http\Response
     */
    public function loginAction()
    {
        if ($this->user) {
            return $this->redirect()->toRoute('accantona_recap');
        }
        $form = new AuthForm();
        $messages = '';

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new LoginFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {

                /** @var array<string, mixed> $data */
                $data = $form->getData();

                // Perform login attempt.
                $result = $this->authManager->login($data['email'], $data['password'], (bool) $data['rememberMe']);

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
        $this->layout('layout/sign-in-b5');
        return ['form' => $form, 'messages' => $messages];
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
     * @return array|Response|mixed
     */
    public function changePasswordAction()
    {
        if (!$this->user) {
            return $this->redirect()->toRoute('auth', ['action' => 'login']);
        }

        /** @var ?User $user */
        $user = $this->em->find(User::class, $this->user->getId());
        if (!$user) {
            return $this->forward()->dispatch(UserController::class, ['action' => 'logout']);
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
