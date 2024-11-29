<?php

namespace App\Twig;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('categories', [$this, 'getCategories']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('firstWords', [$this, 'getFirstWords']),
        ];
    }

    /**
     * @return Category[]
     */
    public function getCategories(): array
    {
        return $this->entityManager->getRepository(Category::class)->findAll();
    }

    public function getFirstWords(string $content, int $words = 20): string
    {
        $content = explode(' ', strip_tags($content));
        $wordsArray = array_slice($content, 0, $words);

        return implode(' ', $wordsArray) . (count($content) > $words ? '...' : '');
    }
}