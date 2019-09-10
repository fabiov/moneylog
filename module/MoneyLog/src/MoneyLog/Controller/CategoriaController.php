<?php
namespace MoneyLog\Controller;

use Application\Entity\Aside;
use Application\Entity\Category;
use Doctrine\ORM\EntityManager;
use MoneyLog\Form\CategoriaForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CategoriaController extends AbstractActionController
{
    /**
     * @var \stdClass
     */
    private $user;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(\stdClass $user, EntityManager $em)
    {
        $this->em   = $em;
        $this->user = $user;
    }

    public function addAction()
    {
        $form = new CategoriaForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $category = new Category();
            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $data = $form->getData();
                $data['userId'] = $this->user->id;
                $category->exchangeArray($data);
                $this->em->persist($category);
                $this->em->flush();

                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_categoria');
            }
        }
        return array('form' => $form);
    }

    public function indexAction()
    {
        return new ViewModel([
            'rows' => $this->em->getRepository(Category::class)->findBy(['userId' => $this->user->id])
        ]);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        $category = $this->em->getRepository('Application\Entity\Category')
            ->findOneBy(array('id' => $id, 'userId' => $this->user->id));
        if (!$category) {
            return $this->redirect()->toRoute('accantona_categoria', array('action' => 'index'));
        }

        $form = new CategoriaForm();
        $form->bind($category);

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setInputFilter($category->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->em->flush();
                return $this->redirect()->toRoute('accantona_categoria');
            }
        }
        return array('id' => $id, 'form' => $form);
    }

    public function deleteAction()
    {
        $id   = (int) $this->params()->fromRoute('id', 0);
        $repo = $this->em->getRepository(Category::class);

        /* @var $category Category */
        $category = $repo->find($id);
        if ($category && $category->userId == $this->user->id) {
            $sum = $repo->getSum($id);

            $this->em->beginTransaction();
            if ($sum) {
                $aside = new Aside();
                $aside->userId      = $this->user->id;
                $aside->descrizione = 'Conguaglio rimozione categoria ' . $category->getDescrizione();
                $aside->importo     = $sum;
                $aside->valuta      = new \DateTime();
                $this->em->persist($aside);
            }
            $this->em->remove($category);
            $this->em->flush();
            $this->em->commit();
        }

        return $this->redirect()->toRoute('accantona_categoria'); // Redirect to list of categories

    }
}
