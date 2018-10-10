<?php
namespace Accantona\Controller;

use Accantona\Form\CategoriaForm;
use Application\Entity\Category;
use Doctrine\ORM\EntityManager;
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
        $this->user             = $user;
        $this->em               = $em;
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
        return new ViewModel(array(
            'rows' => $this->em->getRepository('Application\Entity\Category')->findBy(array('userId' => $this->user->id))
        ));
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
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('accantona_categoria');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
            }

            // Redirect to list of categories
            return $this->redirect()->toRoute('accantona_categoria');
        }

        return array('id' => $id, 'category' => null);
    }
}
