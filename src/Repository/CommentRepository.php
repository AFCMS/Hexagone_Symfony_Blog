<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }
    
    /**
     * @return Comment|null
     */
    public function findByUserAndPost(int $userId, int $postId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.user = :userId')
            ->andWhere('c.post = :postId')
            ->setParameter('userId', $userId)
            ->setParameter('postId', $postId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
