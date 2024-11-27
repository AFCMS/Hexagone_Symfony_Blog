<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminCategoryController extends AbstractController
{
    #[Route('/admin/category/create', name: 'app_admin_category_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cat = new Category();

        $form = $this->createForm(CategoryType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cat = $form->getData();

            $entityManager->persist($cat);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_category_list');
        }

        return $this->render('admin_category/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/category/edit/{id}', name: 'app_admin_category_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->attributes->get('id');
        $cat = $entityManager->getRepository(Category::class)->find($id);

        if (!$cat) {
            throw $this->createNotFoundException('The category does not exist');
        }

        $form = $this->createForm(CategoryType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cat = $form->getData();

            $entityManager->persist($cat);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_category_list');
        }

        return $this->render('admin_category/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/category/delete/{id}', name: 'app_admin_category_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_category_list');
    }

    #[Route('/admin/category/list', name: 'app_admin_category_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('admin_category/list.html.twig', [
            'categories' => $categories,
        ]);
    }
}
