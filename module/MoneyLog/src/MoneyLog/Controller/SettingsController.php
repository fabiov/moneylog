<?php

namespace MoneyLog\Controller;

use Application\Entity\Setting;
use Auth\Service\UserData;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Laminas\View\Model\ViewModel;
use MoneyLog\Form\SettingsForm;
use Laminas\Mvc\Controller\AbstractActionController;

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

    /**
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ORMException
     */
    public function indexAction(): ViewModel
    {
        /** @var Setting $setting */
        $setting = $this->em->find(Setting::class, $this->user->id);
        $message = '';

        $form = new SettingsForm();
        $form->bind($setting);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($setting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();

                $this->userData->setSettings($setting);
                $message = 'Impostazioni salvate correttamente.';
            }
        }
        return new ViewModel(['form' => $form, 'message' => $message]);
    }
}
