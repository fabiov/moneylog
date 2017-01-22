<?php
namespace Accantona\Controller;

use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Accantona\Form\SettingsForm;

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

    public function __construct(EntityManager $em, \stdClass $user)
    {
        $this->em   = $em;
        $this->user = $user;
    }

    public function indexAction()
    {
        $message = '';
        $setting = $this->em->find('Application\Entity\Setting', $this->user->id);

        $form = new SettingsForm();
        $form->bind($setting);
        $form->get('submit')->setAttribute('value', 'Save');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($setting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $setting->userId = $this->user->id;
                $this->em->flush();
                $message = 'You successfully saved settings.';
            }
        }
        return array('form' => $form, 'message' => $message);
    }
}