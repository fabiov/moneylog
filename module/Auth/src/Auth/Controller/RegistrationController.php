<?php

declare(strict_types=1);

namespace Auth\Controller;

use Application\Entity\Setting;
use Application\Entity\User;
use Auth\Form\Filter\RegistrationFilter;
use Auth\Form\ForgottenPasswordFilter;
use Auth\Form\ForgottenPasswordForm;
use Auth\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mail\Message;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Model\ViewModel;

/**
 * @method \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger flashMessenger()
 */
class RegistrationController extends AbstractActionController
{
    private EntityManagerInterface $em;

    private ServiceManager $sm;

    public function __construct(EntityManagerInterface $em, ServiceManager $sm)
    {
        $this->em = $em;
        $this->sm = $sm;
    }

    /**
     * @return \Laminas\Http\Response|ViewModel<RegistrationForm>
     * @throws \Exception
     */
    public function indexAction()
    {
        $form = new RegistrationForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new RegistrationFilter($this->sm));
            $form->setData($request->getPost());

            if ($form->isValid()) {

                /** @var array<string, mixed> $data */
                $data = $form->getData();

                $salt = self::getRandomString(4);

                $user = new User(
                    $data['email'],
                    $data['name'],
                    $data['surname'],
                    self::encriptPassword($data['password'], $salt),
                    $salt,
                    User::STATUS_NOT_CONFIRMED,
                    'user',
                    self::getRandomString(8)
                );

                $this->em->persist($user);
                $this->em->flush();
                $this->sendConfirmationEmail($user);
                $this->flashMessenger()->addMessage($user->getEmail());

                return $this->redirect()->toRoute('auth_registration', ['action' => 'registration-success']);
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(['form' => $form]);
    }

    /**
     * @return ViewModel<mixed>
     */
    public function registrationSuccessAction(): ViewModel
    {
        $userEmail = null;
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            foreach ($flashMessenger->getMessages() as $value) {
                $userEmail .=  $value;
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(['userEmail' => $userEmail]);
    }

    /**
     * @return \Laminas\View\Model\ViewModel<mixed>
     */
    public function confirmEmailAction(): ViewModel
    {
        $token = $this->params()->fromRoute('id');
        $viewModel = new ViewModel(['token' => $token]);

        /** @var ?User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['registrationToken' => $token]);

        if ($user) {
            $user->setStatus(User::STATUS_CONFIRMED);
            $this->em->persist($user);

            //create settings
            $setting = new Setting($user);
            $this->em->persist($setting);

            $this->em->flush();
        } else {
            $viewModel->setTemplate('auth/registration/confirm-email-error.phtml');
        }
        $this->layout('layout/unlogged');
        return $viewModel;
    }

    /**
     * @return \Laminas\Http\Response|\Laminas\View\Model\ViewModel<mixed>
     * @throws \Exception
     */
    public function forgottenPasswordAction()
    {
        $form = new ForgottenPasswordForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new ForgottenPasswordFilter($this->sm));
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var array<string, mixed> $data */
                $data = $form->getData();

                $password = $this->getRandomString(10);

                /** @var User $user */
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
                $user->setPassword($this->encriptPassword($password, $user->getSalt()));
                $this->em->persist($user);
                $this->em->flush();

                $this->sendPasswordByEmail($data['email'], $password);
                $this->flashMessenger()->addMessage($data['email']);
                return $this->redirect()->toRoute('auth_registration', ['action' => 'password-change-success']);
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(['form' => $form]);
    }

    /**
     * @return ViewModel<mixed>
     */
    public function passwordChangeSuccessAction(): ViewModel
    {
        $userEmail = null;
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            foreach ($flashMessenger->getMessages() as $value) {
                $userEmail .=  $value;
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(['userEmail' => $userEmail]);
    }

    /**
     * @throws \Exception
     */
    private static function getRandomString(int $length): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $rand  = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $rand;
    }

    private static function encriptPassword(string $password, string $dynamicSalt): string
    {
        return md5($password . $dynamicSalt);
    }

    private function sendConfirmationEmail(User $user): void
    {
        $config = $this->sm->get('Config');
        $body = "Please, click the link to confirm your registration. "
              . $this->getRequest()->getServer('HTTP_ORIGIN')
              . $this->url()->fromRoute('auth_registration', ['action' => 'confirm-email','id' => $user->getRegistrationToken()]);

        $message = new Message();
        $message->addTo($user->getEmail())
                ->addFrom($config['mail']['sender']['address'], $config['mail']['sender']['name'])
                ->setSubject('Conferma registrazione')
                ->setBody($body);
        $this->sm->get('mail.transport')->send($message);
    }

    private function sendPasswordByEmail(string $userEmail, string $password): void
    {
        $config = $this->sm->get('Config');

        $str = 'La tua password su  %s è stata cambiata. La tua nuova password è: %s';
        $message = new Message();
        $message->addTo($userEmail)
                ->addFrom($config['mail']['sender']['address'], $config['mail']['sender']['name'])
                ->setSubject('La tua password è stata cambiata!')
                ->setBody(sprintf($str, $this->getRequest()->getServer('HTTP_ORIGIN'), $password));
        $this->sm->get('mail.transport')->send($message);
    }
}
