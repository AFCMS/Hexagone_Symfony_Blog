<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPostController extends AbstractController
{
    #[Route('/admin/post/create', name: 'app_admin_post_create')]
    public function create(
        Request                                                        $request,
        EntityManagerInterface                                         $entityManager,
        #[Autowire('%kernel.project_dir%/public/images/posts')] string $postsDirectory
    ): Response
    {
        $cat = new Post();

        $form = $this->createForm(PostType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $cat */
            $cat = $form->getData();

            $cat->setIdUser($this->getUser());

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('pictureFile')->getData();

            $newFilename = 'post-' . uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($postsDirectory, $newFilename);
            $cat->setPicture($newFilename);

            $cat->setPublishedAt(new DateTime());

            $entityManager->persist($cat);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_category_list');
        }

        return $this->render('admin_post/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/admin/post/edit/{id}', name: 'app_admin_post_edit')]
    public function edit(
        Request                                                        $request,
        EntityManagerInterface                                         $entityManager,
        #[Autowire('%kernel.project_dir%/public/images/posts')] string $postsDirectory
    ): Response
    {
        $id = $request->attributes->get('id');
        $cat = $entityManager->getRepository(Post::class)->find($id);

        if (!$cat) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $form = $this->createForm(PostType::class, $cat);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Post $cat */
            $cat = $form->getData();

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('pictureFile')->getData();

            $newFilename = 'post-' . uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($postsDirectory, $newFilename);
            $cat->setPicture($newFilename);

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

        $file = $this->getParameter('kernel.project_dir') . '/public/images/posts/' . $category->getPicture();
        if (file_exists($file)) {
            unlink($file);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_main');
    }
}
