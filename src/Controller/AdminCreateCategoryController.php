<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminCreateCategoryController extends AbstractController
{
    #[Route('/admin/create/category', name: 'app_admin_create_category')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cat = new Category();

        $form = $this->createForm(CategoryType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cat = $form->getData();

            $entityManager->persist($cat);
            $entityManager->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('admin_create_category/index.html.twig', [
            'form' => $form,
        ]);
    }
}
