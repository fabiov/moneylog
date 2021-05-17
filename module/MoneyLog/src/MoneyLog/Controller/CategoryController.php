<?php
declare(strict_types=1);

namespace MoneyLog\Controller;

use Application\Entity\Provision;
use Application\Entity\Category;
use Application\Entity\User;
use Doctrine\ORM\EntityManager;
use Laminas\Http\Response;
use MoneyLog\Form\CategoriaForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class CategoryController extends AbstractActionController
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

                /** @var User $user */
                $user = $this->em->getRepository(User::class)->find($this->user->id);

                /** @var array $data */
                $data = $form->getData();

                $category->exchangeArray($data);
                $category->setUser($user);
                $this->em->persist($category);
                $this->em->flush();

                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_categoria');
            }
        }
        return ['form' => $form];
    }

    public function indexAction()
    {
        return new ViewModel([
            'rows' => $this->em->getRepository(Category::class)->findBy(['user' => $this->user->id])
        ]);
    }

    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        /** @var ?Category $category */
        $category = $this->em->getRepository(Category::class)
            ->findOneBy(['id' => $id, 'user' => $this->user->id]);

        if (!$category) {
            return $this->redirect()->toRoute('accantona_categoria', ['action' => 'index']);
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
        return ['id' => $id, 'form' => $form];
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\NoResultException
     */
    public function deleteAction(): Response
    {
        $id   = (int) $this->params()->fromRoute('id');

        /** @var \Application\Repository\CategoryRepository $categoryRepository */
        $categoryRepository = $this->em->getRepository(Category::class);

        /** @var ?Category $category */
        $category = $categoryRepository->find($id);

        if ($category && $category->getUser()->getId() === $this->user->id) {
            $sum = $categoryRepository->getSum($id);

            $this->em->beginTransaction();
            if ($sum) {
                $provision = new Provision();
                $provision->setUser($category->getUser());
                $provision->setDescrizione('Conguaglio rimozione categoria ' . $category->getDescrizione());
                $provision->setImporto($sum);
                $provision->setValuta(new \DateTime());
                $this->em->persist($provision);
            }
            $this->em->remove($category);
            $this->em->flush();
            $this->em->commit();
        }

        return $this->redirect()->toRoute('accantona_categoria'); // Redirect to list of categories
    }
}
