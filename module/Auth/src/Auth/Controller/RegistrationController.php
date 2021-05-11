<?php

declare(strict_types=1);

namespace Auth\Controller;

use Application\Entity\Setting;
use Application\Entity\User;
use Auth\Form\ForgottenPasswordFilter;
use Auth\Form\ForgottenPasswordForm;
use Auth\Form\Filter\RegistrationFilter;
use Auth\Form\RegistrationForm;
use Doctrine\ORM\EntityManager;
use Laminas\Mail\Message;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\Model\ViewModel;

/**
 * @method \Laminas\Mvc\Plugin\FlashMessenger\FlashMessenger flashMessenger()
 */
class RegistrationController extends AbstractActionController
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ServiceManager
     */
    private $sm;

    public function __construct(EntityManager $em, ServiceManager $sm)
    {
        $this->em = $em;
        $this->sm = $sm;
    }

    /**
     * @return \Laminas\Http\Response|\Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function indexAction()
    {
        $form = new RegistrationForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new RegistrationFilter($this->sm));
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data = $this->prepareData($data);

                $user = new User();
                $user->exchangeArray($data);
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

    public function registrationSuccessAction(): ViewModel
    {
        $usr_email = null;
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            foreach ($flashMessenger->getMessages() as $key => $value) {
                $usr_email .=  $value;
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(['usr_email' => $usr_email]);
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function confirmEmailAction(): ViewModel
    {
        $token = $this->params()->fromRoute('id');
        $viewModel = new ViewModel(['token' => $token]);

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['registrationToken' => $token]);

        if ($user) {
            $user->setStatus(User::STATUS_CONFIRMED);
            $this->em->persist($user);

            //create settings
            $setting = new Setting();
            $setting->userId = $user->getId();
            $this->em->persist($setting);

            $this->em->flush();
        } else {
            $viewModel->setTemplate('auth/registration/confirm-email-error.phtml');
        }
        $this->layout('layout/unlogged');
        return $viewModel;
    }

    /**
     * @return \Laminas\Http\Response|\Laminas\View\Model\ViewModel
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function forgottenPasswordAction()
    {
        $form = new ForgottenPasswordForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new ForgottenPasswordFilter($this->sm));
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();

                $password = $this->getRandomString(10);

                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
                $user->password = $this->encriptPassword($password, $user->salt);
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

    public function passwordChangeSuccessAction(): ViewModel
    {
        $usr_email = null;
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            foreach ($flashMessenger->getMessages() as $value) {
                $usr_email .=  $value;
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(['usr_email' => $usr_email]);
    }

    private static function prepareData(array $data): array
    {
        $data['salt'] = self::getRandomString(4);
        $data['password'] = self::encriptPassword($data['password'], $data['salt']);
        $data['status'] = 0;
        $data['role'] = 'user';
        $data['registrationToken'] = self::getRandomString(8);
        return $data;
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

    private function sendPasswordByEmail($userEmail, $password): void
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
