<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PostsController extends AbstractController
{
    #[Route('/posts', name: 'app_posts')]
    public function posts(Request $request, EntityManagerInterface $entityManager): Response
    {
        $query = $request->query->get('query');
        $category = $request->query->get('category');

        $qb = $entityManager->getRepository(Post::class)->createQueryBuilder("p");

        if ($query) {
            $qb->andWhere("LOWER(p.title) LIKE LOWER(:query) OR LOWER(p.content) LIKE LOWER(:query)")
                ->setParameter("query", "%$query%");
        }

        if ($category) {
            $qb->andWhere("p.idCategory = :category")
                ->setParameter("category", $category);
        }

        $posts = $qb->getQuery()->getResult();
        return $this->render('posts/index.html.twig', [
            'query' => $query,
            'category' => $entityManager->getRepository(Category::class)->findOneBy(['id' => $category]),
            'posts' => $posts,
        ]);
    }
}
