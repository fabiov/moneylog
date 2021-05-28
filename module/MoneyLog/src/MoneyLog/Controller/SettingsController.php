<?php

namespace MoneyLog\Controller;

use Application\Entity\Setting;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use MoneyLog\Form\SettingForm;

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

        $form = new SettingForm();
        $form->bind($setting);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($setting->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();

                $message = 'Impostazioni salvate correttamente.';
            }
        }
        return new ViewModel(['form' => $form, 'message' => $message]);
    }
}
