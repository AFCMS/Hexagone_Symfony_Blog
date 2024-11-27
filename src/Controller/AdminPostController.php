<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPostController extends AbstractController
{
    #[Route('/admin/post/create', name: 'app_admin_post_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cat = new Post();

        $form = $this->createForm(PostType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cat = $form->getData();

            $cat->setPublishedAt(new DateTime());
            $cat->setIdUser($this->getUser());

            $entityManager->persist($cat);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_category_list');
        }

        return $this->render('admin_post/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/post/edit/{id}', name: 'app_admin_post_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->attributes->get('id');
        $cat = $entityManager->getRepository(Post::class)->find($id);

        if (!$cat) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $form = $this->createForm(PostType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $cat = $form->getData();

            $entityManager->persist($cat);
            $entityManager->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('admin_post/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/post/delete/{id}', name: 'app_admin_post_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Post::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_main');
    }
}
