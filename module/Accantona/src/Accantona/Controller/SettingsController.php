<?php
namespace Accantona\Controller;

use Accantona\Form\SettingsForm;
use Doctrine\ORM\EntityManager;
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

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($setting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $setting->userId = $this->user->id;
                $this->em->flush();
                $message = 'Impostazioni salvate correttamente.';
            }
        }
        return array('form' => $form, 'message' => $message);
    }
}