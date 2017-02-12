<?php

namespace Accantona\Controller;

use Accantona\Model\SpesaTable;
use Application\Entity\Category;
use Application\Entity\Moviment;
use Application\Entity\Spese;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Accantona\Form\SpesaForm;

class SpesaController extends AbstractActionController
{

    /**
     * @var SpesaTable
     */
    private $spesaTable;

    /**
     * @var \stdClass
     */
    private $user;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(SpesaTable $spesaTable, \stdClass $user, EntityManager $em)
    {
        $this->spesaTable   = $spesaTable;
        $this->user         = $user;
        $this->em           = $em;
    }

    public function addAction()
    {
        $form = new SpesaForm('spesa', array(), $this->em, $this->user->id);

        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            $spese = new Spese();
            $form->setInputFilter($spese->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $spese->exchangeArray($form->getData());
                $spese->userId = $this->user->id;
                $spese->category = $this->em->getRepository('Application\Entity\Category')->findOneBy(array(
                    'id' => $data['id_categoria'],
                    'userId' => $this->user->id,
                ));

                $this->em->persist($spese);

                // if is set account id add new moviment to account
                if ($data['accountId']) {

                    $account = $this->em->getRepository('Application\Entity\Account')
                        ->findOneBy(array('id' => $data['accountId'], 'userId' => $this->user->id));
                    if ($account) {
                        $moviment = new Moviment();
                        $moviment->exchangeArray(array(
                            'amount'      => -1 * $data['importo'],
                            'date'        => $data['valuta'],
                            'description' => $data['descrizione'],
                        ));
                        $moviment->account = $account;
                        $this->em->persist($moviment);
                    }
                }
                $this->em->flush();

                // Redirect to list of albums
                return $this->redirect()->toRoute('accantona_spesa');
            }
        }

        return array('form' => $form);
    }

    public function indexAction()
    {
        $where = 'spese.userId=:userId';
        $params = array('userId' => $this->user->id);

        if (($categoryId = (int) $this->params()->fromQuery('categoryIdFilter', 0)) != false) {
            $where .= ' AND spese.category=:categoryId';
            $params['categoryId'] = $categoryId;
        }
        if (($months = (int) $this->params()->fromQuery('monthsFilter', 1)) != false) {
            $where .= ' AND spese.valuta >= :date';
            $dateTime = new \DateTime();
            $params['date'] = $dateTime->modify("-$months month");
        }

        $categories = $this->em->getRepository('Application\Entity\Category')
            ->findBy(array('status' => Category::STATUS_ACTIVE, 'userId' => $this->user->id));

        $rows = $this->em->getRepository('Application\Entity\Spese')->getSpese($where, $params);

        $sum = 0;
        foreach ($rows as $row) {
            $sum += $row->importo;
        }

        return new ViewModel(array(
            'avgPerCategory'    => $this->spesaTable->getAvgPerCategories($this->user->id),
            'categories'        => $categories,
            'categoryId'        => $categoryId,
            'months'            => $months,
            'rows'              => $rows,
            'sum'               => $sum,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /* @var Spese $spend */
        $spend = $this->em->getRepository('Application\Entity\Spese')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));

        if (!$spend) {
            return $this->redirect()->toRoute('accantona_spesa', array('action' => 'index'));
        }

        $form = new SpesaForm('spesa', array(), $this->em, $this->user->id);
        $form->bind($spend);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($spend->getInputFilter());
            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {

                $spend->category = $this->em->getRepository('Application\Entity\Category')->findOneBy(array(
                    'id' => $data['id_categoria'],
                    'userId' => $this->user->id,
                ));

                $this->em->flush();

                return $this->redirect()->toRoute('accantona_spesa');
            }
        }

        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $spend = $this->em->getRepository('Application\Entity\Spese')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));

        if ($spend) {
            $this->em->remove($spend);
            $this->em->flush();
        }
        return $this->redirect()->toRoute('accantona_spesa');
    }
}