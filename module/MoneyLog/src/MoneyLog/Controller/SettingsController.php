<?php
namespace MoneyLog\Controller;

use Auth\Service\UserData;
use Doctrine\ORM\EntityManager;
use MoneyLog\Form\SettingsForm;
use Zend\Mvc\Controller\AbstractActionController;

class SettingsController extends AbstractActionController
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
     * @var UserData
     */
    private $userData;

    public function __construct(EntityManager $em, \stdClass $user, UserData $userData)
    {
        $this->em       = $em;
        $this->user     = $user;
        $this->userData = $userData;
    }

    public function indexAction()
    {
        $message = '';
        $setting = $this->em->find('Application\Entity\Setting', $this->user->id);

        $form = new SettingsForm();
        $form->bind($setting);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($setting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $setting->userId = $this->user->id;
                $this->em->flush();

                $this->userData->setSettings($setting);
                $message = 'Impostazioni salvate correttamente.';
            }
        }
        return array('form' => $form, 'message' => $message);
    }
}