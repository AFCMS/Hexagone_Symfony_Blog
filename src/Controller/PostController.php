<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'app_post')]
    public function index(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $comment = new Comment();

        /** @var CommentRepository $rep */
        $is_update = false;
        $rep = $entityManager->getRepository(Comment::class);
        $old_post = $rep->findByUserAndPost($this->getUser()->getId(), $post->getId());

        if ($old_post) {
            $comment = $old_post;
            $is_update = true;
        } else {
            $comment->setUser($this->getUser());
            $comment->setPost($post);
            $comment->setCreatedAt(new DateTime());
        }

        $form_comment = $this->createForm(CommentType::class, $comment);

        $form_comment->handleRequest($request);

        if ($form_comment->isSubmitted() && $form_comment->isValid()) {
            /** @var Comment $cat */
            $c = $form_comment->getData();

            $entityManager->persist($c);
            $entityManager->flush();
            return $this->redirectToRoute('app_post', ['id' => $id]);
        }

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'form_comment' => $form_comment,
            'is_update' => $is_update,
        ]);
    }

    #[Route('/post/{id}/delcomment', name: 'app_post_delete_comment')]
    public function delcomment(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('The post does not exist');
        }

        $user = $this->getUser();

        /** @var CommentRepository $rep */
        $rep = $entityManager->getRepository(Comment::class);

        $comment = $rep->findByUserAndPost($user->getId(), $post->getId());

        if (!$comment) {
            throw $this->createNotFoundException('The comment does not exist');
        }

        $entityManager->remove($comment);
        $entityManager->flush();

        return $this->redirectToRoute('app_post', ['id' => $id]);
    }
}
