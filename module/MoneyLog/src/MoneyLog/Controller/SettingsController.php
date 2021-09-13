<?php

namespace MoneyLog\Controller;

use Application\Entity\Setting;
use Auth\Model\LoggedUser;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use MoneyLog\Form\Filter\SettingFilter;
use MoneyLog\Form\SettingForm;

class SettingsController extends AbstractActionController
{
    private EntityManagerInterface $em;

    private LoggedUser $user;

    public function __construct(EntityManagerInterface $em, LoggedUser $user)
    {
        $this->em   = $em;
        $this->user = $user;
    }

    /**
     * @return \Laminas\View\Model\ViewModel
     */
    public function indexAction(): ViewModel
    {
        /** @var Setting $setting */
        $setting = $this->em->find(Setting::class, $this->user->getId());
        $message = '';

        $form = new SettingForm();
        $form->setData([
            'payday' => $setting->getPayday(),
            'months' => $setting->getMonths(),
            'provisioning' => $setting->hasProvisioning(),
        ]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new SettingFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var array<string, mixed> $data */
                $data = $form->getData();

                $setting->setPayday($data['payday']);
                $setting->setMonths($data['months']);
                $setting->setProvisioning($data['provisioning']);

                $this->em->flush();

                $message = 'Impostazioni salvate correttamente.';
            }
        }
        return new ViewModel(['form' => $form, 'message' => $message]);
    }
}
