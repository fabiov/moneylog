<?php
namespace Accantona\Controller;

use Application\Entity\Setting;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Accantona\Form\SettingsForm;

class SettingsController extends AbstractActionController
{

    /**
     * @var DoctrineORMEntityManager
     */
    private $em;
    private $user;

    public function indexAction()
    {
        $userId = $this->getUser()->id;
        $message = '';
        $setting = $this->getEntityManager()->find('Application\Entity\Setting', $userId);

        $form = new SettingsForm();
        $form->bind($setting);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($setting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $setting->userId = $userId;
                $this->getEntityManager()->flush();

                $message = 'You successfully saved settings.';
            }
        }

        return array('form' => $form, 'message' => $message);
    }

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function getUser()
    {
        if (!$this->user) {
            $this->user = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
        }
        return $this->user;
    }

}