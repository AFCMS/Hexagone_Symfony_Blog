<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminUsersController extends AbstractController
{
    #[Route('/admin/users', name: 'app_admin_users')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin_users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'app_admin_users_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/admin/users/edit/{id}', name: 'app_admin_users_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $role = $request->request->get('role');

        if (in_array($role, ['ROLE_USER', 'ROLE_PUBLISHER', 'ROLE_ADMIN'])) {
            $user->setRoles([$role]);
        } else {
            throw $this->createNotFoundException('The role does not exist');
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users');
    }
}
