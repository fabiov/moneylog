<?php
namespace Auth\Controller;

use Application\Entity\Setting;
use Application\Entity\User;
use Auth\Form\ForgottenPasswordFilter;
use Auth\Form\ForgottenPasswordForm;
use Auth\Form\Filter\RegistrationFilter;
use Auth\Form\RegistrationForm;
use Auth\Model\UserTable;
use Doctrine\ORM\EntityManager;
use Zend\Mail\Message;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Model\ViewModel;

class RegistrationController extends AbstractActionController
{
    /**
     * @var DoctrineORMEntityManager
     */
    private $em;

    /**
     * @var ServiceManager
     */
    private $sm;

    /**
     * @var UserTable
     */
    private $userTable;

    public function __construct(EntityManager $em, ServiceManager $sm, UserTable $userTable)
    {
        $this->em           = $em;
        $this->sm           = $sm;
        $this->userTable    = $userTable;
    }

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
                $this->flashMessenger()->addMessage($user->email);

                return $this->redirect()->toRoute('auth/default', array(
                    'controller' => 'registration',
                    'action' => 'registration-success'
                ));
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(array('form' => $form));
    }

    public function registrationSuccessAction()
    {
        $usr_email = null;
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            foreach($flashMessenger->getMessages() as $key => $value) {
                $usr_email .=  $value;
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(array('usr_email' => $usr_email));
    }

    public function confirmEmailAction()
    {
        $token = $this->params()->fromRoute('id');
        $viewModel = new ViewModel(array('token' => $token));
        try {

            $user = $this->userTable->getUserByToken($token);
            $this->userTable->activateUser($user->id);

            //create settings
            $setting = new Setting();
            $setting->userId = $user->id;
            $this->em->persist($setting);
            $this->em->flush();
        } catch(\Exception $e) {
            $viewModel->setTemplate('auth/registration/confirm-email-error.phtml');
        }
        $this->layout('layout/unlogged');
        return $viewModel;
    }

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

                $user = $this->em->getRepository('Application\Entity\User')
                    ->findOneBy(array('email' => $data['email']));
                $user->password = $this->encriptPassword($password, $user->salt);
                $this->em->persist($user);
                $this->em->flush();

                $this->sendPasswordByEmail($data['email'], $password);
                $this->flashMessenger()->addMessage($data['email']);
                return $this->redirect()->toRoute('auth/default', array(
                    'action'        => 'password-change-success',
                    'controller'    => 'registration',
                ));
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(array('form' => $form));
    }

    public function passwordChangeSuccessAction()
    {
        $usr_email = null;
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            foreach($flashMessenger->getMessages() as $key => $value) {
                $usr_email .=  $value;
            }
        }
        $this->layout('layout/unlogged');
        return new ViewModel(array('usr_email' => $usr_email));
    }

    public function prepareData($data)
    {
        $data['salt'] = $this->getRandomString(4);
        $data['password'] = $this->encriptPassword($data['password'], $data['salt']);
        $data['status'] = 0;
        $data['role'] = 'user';
        $data['registrationToken'] = $this->getRandomString(8);
        return $data;
    }

    /**
     * @param int
     * @return string
     */
    public function getRandomString($length)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $rand  = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $rand;
    }

    public function getStaticSalt()
    {
        $config = $this->sm->get('Config');
        $staticSalt = $config['static_salt'];
        return $staticSalt;
    }

    public function encriptPassword($password, $dynamicSalt)
    {
        return md5($password . $dynamicSalt);
    }

    /**
     * @param $user
     */
    public function sendConfirmationEmail($user)
    {
        $config = $this->sm->get('Config');
        $body = "Please, click the link to confirm your registration. "
              . $this->getRequest()->getServer('HTTP_ORIGIN')
              . $this->url()->fromRoute('auth/default', array(
                    'controller' => 'registration',
                    'action' => 'confirm-email',
                    'id' => $user->registrationToken
                ));

        $message = new Message();
        $message->addTo($user->email)
                ->addFrom($config['mail']['sender']['address'], $config['mail']['sender']['name'])
                ->setSubject('Conferma registrazione')
                ->setBody($body);
        $this->sm->get('mail.transport')->send($message);
    }

    /**
     * @param string $userEmail
     * @param string $password
     */
    public function sendPasswordByEmail($userEmail, $password)
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
