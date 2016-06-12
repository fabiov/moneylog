<?php

namespace Accantona\Controller;

use Application\Entity\Category;
use Application\Entity\Moviment;
use Application\Entity\Spese;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Form\SpesaForm;

class SpesaController extends AbstractActionController
{

    protected $spesaTable;

    public function addAction()
    {
        $em = $this->getEntityManager();
        $user = $this->getUser();
        $form = new SpesaForm('spesa', array(), $em, $user->id);

        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            $spese = new Spese();
            $form->setInputFilter($spese->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $spese->exchangeArray($form->getData());
                $spese->userId = $user->id;

                $em->persist($spese);

                // if is set account id add new moviment to account
                if ($data['accountId']) {


                    $account = $em->getRepository('Application\Entity\Account')
                        ->findOneBy(array('id' => $data['accountId'], 'userId' => $user->id));
                    if ($account) {
                        $moviment = new Moviment();
                        $moviment->exchangeArray(array(
                            'amount'      => -1 * $data['importo'],
                            'date'        => $data['valuta'],
                            'description' => $data['descrizione'],
                        ));
                        $moviment->account = $account;
                        $em->persist($moviment);
                    }
                }
                $em->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('accantona_spesa');
            }
        }

        return array('form' => $form);
    }

    public function indexAction()
    {
        $em = $this->getEntityManager();
        $user = $this->getUser();
        $where = array('spese.userId' => $user->id);

        if (($categoryId = (int) $this->params()->fromQuery('categoryIdFilter', 0)) != false) {
            $where[] = "Category.id=$categoryId";
        }
        if (($months = (int) $this->params()->fromQuery('monthsFilter', 1)) != false) {
            $where[] = 'spese.valuta>"' . date('Y-m-d', strtotime("-$months month")) .'"';
        }

        $categories = $em->getRepository('Application\Entity\Category')
            ->findBy(array('status' => Category::STATUS_ACTIVE, 'userId' => $user->id));

        return new ViewModel(array(
            'categoryId' => $categoryId,
            'months'     => $months,
            'rows'       => $this->getSpesaTable()->joinFetchAll($where),
            'categories' => $categories,
            'avgPerCategory' => $this->getSpesaTable()->getAvgPerCategories($user->id),
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $user = $this->getUser();

        $spend = $em->getRepository('Application\Entity\Spese')
            ->findOneBy(array('id' => $id, 'userId' => $user->id));

        if (!$spend) {
            return $this->redirect()->toRoute('accantona_spesa', array('action' => 'index'));
        }

        $form = new SpesaForm('spesa', array(), $em, $user->id);
        $form->bind($spend);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($spend->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->flush();

                return $this->redirect()->toRoute('accantona_spesa');
            }
        }

        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $em = $this->getEntityManager();
        $spend = $em->getRepository('Application\Entity\Spese')
            ->findOneBy(array('id' => $id, 'userId' => $this->getUser()->id));

        if ($spend) {
            $em->remove($spend);
            $em->flush();
        }
        return $this->redirect()->toRoute('accantona_spesa');
    }

    public function getSpesaTable()
    {
        if (!$this->spesaTable) {
            $sm = $this->getServiceLocator();
            $this->spesaTable = $sm->get('Accantona\Model\SpesaTable');
        }
        return $this->spesaTable;
    }

    public function getUser()
    {
        return $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity();
    }

    public function getEntityManager()
    {
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

}
