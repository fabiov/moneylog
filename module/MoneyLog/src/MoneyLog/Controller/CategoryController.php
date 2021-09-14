<?php

declare(strict_types=1);

namespace MoneyLog\Controller;

use Application\Entity\Category;
use Application\Entity\Provision;
use Application\Entity\User;
use Auth\Model\LoggedUser;
use Doctrine\ORM\EntityManagerInterface;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use MoneyLog\Form\CategoryForm;
use MoneyLog\Form\Filter\CategoryFilter;

class CategoryController extends AbstractActionController
{
    private LoggedUser $user;

    private EntityManagerInterface $em;

    public function __construct(LoggedUser $user, EntityManagerInterface $em)
    {
        $this->em   = $em;
        $this->user = $user;
    }

    /**
     * @return Response|array<string, CategoryForm>
     */
    public function addAction()
    {
        $form = new CategoryForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new CategoryFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {

                /** @var User $user */
                $user = $this->em->getRepository(User::class)->find($this->user->getId());

                /** @var array<string> $data */
                $data = $form->getData();

                $category = new Category($user, $data['description'], (int) $data['status']);

                $this->em->persist($category);
                $this->em->flush();

                // Redirect to list of categories
                return $this->redirect()->toRoute('accantona_categoria');
            }
        }
        return ['form' => $form];
    }

    public function indexAction(): ViewModel
    {
        return new ViewModel([
            'rows' => $this->em->getRepository(Category::class)->findBy(['user' => $this->user->getId()])
        ]);
    }

    /**
     * @return array<string, mixed>|\Laminas\Http\Response
     */
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id');

        /** @var ?Category $category */
        $category = $this->em->getRepository(Category::class)
            ->findOneBy(['id' => $id, 'user' => $this->user->getId()]);

        if (!$category) {
            return $this->redirect()->toRoute('accantona_categoria', ['action' => 'index']);
        }

        $form = new CategoryForm();
        $form->setData([
            'description' => $category->getDescription(),
            'status' => $category->getStatus(),
        ]);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter(new CategoryFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                /** @var array<string, mixed> $data */
                $data = $form->getData();
                $category->setDescription($data['description']);
                $category->setStatus($data['status']);
                $this->em->flush();
                return $this->redirect()->toRoute('accantona_categoria');
            }
        }
        return ['id' => $id, 'form' => $form];
    }

    /**
     * @return \Laminas\Http\Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteAction(): Response
    {
        $id   = (int) $this->params()->fromRoute('id');

        /** @var \Application\Repository\CategoryRepository $categoryRepository */
        $categoryRepository = $this->em->getRepository(Category::class);

        /** @var ?Category $category */
        $category = $categoryRepository->find($id);

        if ($category && $category->getUser()->getId() === $this->user->getId()) {
            $sum = $categoryRepository->getSum($id);

            $this->em->beginTransaction();
            if ($sum) {
                $provision = new Provision();
                $provision->setUser($category->getUser());
                $provision->setDescription('Conguaglio rimozione categoria ' . $category->getDescription());
                $provision->setAmount($sum);
                $provision->setDate(new \DateTime());
                $this->em->persist($provision);
            }
            $this->em->remove($category);
            $this->em->flush();
            $this->em->commit();
        }

        return $this->redirect()->toRoute('accantona_categoria'); // Redirect to list of categories
    }
}
