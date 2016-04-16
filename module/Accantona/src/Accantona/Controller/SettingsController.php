<?php
namespace Accantona\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Debug\Debug;
use Accantona\Form\SettingsForm;

class SettingsController extends AbstractActionController
{

    /**
     * @var DoctrineORMEntityManager
     */
    protected $em;
    protected $user;

//    public function addAction()
//    {
//        $form = new AccantonatoForm();
//
//        $request = $this->getRequest();
//        if ($request->isPost()) {
//
//            $accantonato = new Accantonato();
//            $form->setInputFilter($accantonato->getInputFilter());
//            $form->setData($request->getPost());
//
//            if ($form->isValid()) {
//                $data = $form->getData();
//                $data['userId'] = $this->getUser()->id;
//                $accantonato->exchangeArray($data);
//                $this->getAccantonatoTable()->save($accantonato);
//                // Redirect to list of categories
//                return $this->redirect()->toRoute('accantona_accantonato');
//            }
//            Debug::dump($_POST, '$_POST');
//            Debug::dump($form->getMessages());
//            die();
//        }
//        return array('form' => $form);
//    }

    public function indexAction()
    {
        $message = '';
        $form = new SettingsForm();


        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
//                $this->getAlbumTable()->saveAlbum($album);
                $this->getEntityManager()->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }


        $userSettings = $this->getEntityManager()->getRepository('Application\Entity\UserSetting')
            ->findBy(array('userId' => $this->getUser()->id));

        // set user value for input setting
        foreach ($userSettings as $userSetting) {
            $form->get($userSetting->setting->name)->setAttribute('value', $userSetting->value);
        }

        return new ViewModel(array(
            'message'      => $message,
            'settingForm'  => $form,
            'userSettings' => $userSettings,
        ));
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