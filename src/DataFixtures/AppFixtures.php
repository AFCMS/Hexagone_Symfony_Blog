<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasherInterface;

    public function __construct(UserPasswordHasherInterface $userPasswordHasherInterface)
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }

    public function load(ObjectManager $manager): void
    {
        $generator = Factory::create();

        $admin = new User();
        $admin->setEmail('afcm.contact@gmail.com');
        $admin->setFirstName('Louis');
        $admin->setLastName('Walter');
        $admin->setPassword($this->userPasswordHasherInterface->hashPassword($admin, 'azerty'));
        $admin->setUpdatedAt(new DateTime());
        $admin->setCreatedAt(new DateTime());
        $admin->setRoles(['ROLE_ADMIN']);

        $admin2 = new User();
        $admin2->setEmail('jean.duval@gmail.com');
        $admin2->setFirstName('Jean');
        $admin2->setLastName('Duval');
        $admin2->setPassword($this->userPasswordHasherInterface->hashPassword($admin, 'azerty'));
        $admin2->setUpdatedAt(new DateTime());
        $admin2->setCreatedAt(new DateTime());
        $admin2->setRoles(['ROLE_ADMIN']);

        $normalUser = new User();
        $normalUser->setEmail('tanguy@gmail.com');
        $normalUser->setFirstName('Tanguy');
        $normalUser->setLastName('Dupont');
        $normalUser->setPassword($this->userPasswordHasherInterface->hashPassword($normalUser, 'qwerty'));
        $normalUser->setUpdatedAt(new DateTime());
        $normalUser->setCreatedAt(new DateTime());
        $normalUser->setRoles(['ROLE_USER']);

        $manager->persist($admin);
        $manager->persist($admin2);
        $manager->persist($normalUser);

        $admins = [$admin, $admin2];

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($generator->email());
            $user->setFirstName($generator->firstName());
            $user->setLastName($generator->lastName());
            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, $generator->password()));
            $user->setUpdatedAt(new DateTime());
            $user->setCreatedAt(new DateTime());
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
        }


        $categories = [];
        foreach (['PHP', 'Symfony', 'JavaScript', 'React', 'C#', 'C++', 'Go', 'Gaming'] as $category) {
            $cat = new Category();
            $cat->setName($category);
            $cat->setDescription('Description of ' . $category);
            $categories[] = $cat;
            $manager->persist($cat);
        }

        for ($i = 0; $i < 20; $i++) {
            $post = new Post();
            $post->setTitle($generator->words(5, true));
            $post->setContent($generator->text(3000));
            $post->setIdUser($admins[array_rand($admins)]);
            $post->setPublishedAt(new DateTime());
            $post->setPicture('test_' . ($i + 1) . '.jpeg');
            $post->setIdCategory($categories[array_rand($categories)]);
            $manager->persist($post);
        }

        $manager->flush();
    }
}
