<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users', name: 'app_admin_user_')]
#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(UserRepository $repo): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $repo->findBy([], ['id' => 'ASC']),
        ]);
    }

    #[Route('/{id}/toggle-admin', name: 'toggle_admin', methods: ['POST'])]
    public function toggleAdmin(int $id, Request $request, UserRepository $repo, EntityManagerInterface $em): Response
    {
        $user = $repo->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        // Un admin ne peut pas modifier son propre rôle
        if ($user->getUserIdentifier() === $this->getUser()->getUserIdentifier()) {
            $this->addFlash('danger', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        if (!$this->isCsrfTokenValid('user_toggle_' . $id, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_user_index');
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            $user->setRoles([]);
            $this->addFlash('success', sprintf('%s est maintenant utilisateur.', $user->getEmail()));
        } else {
            $user->setRoles(['ROLE_ADMIN']);
            $this->addFlash('success', sprintf('%s est maintenant administrateur.', $user->getEmail()));
        }

        $em->flush();

        return $this->redirectToRoute('app_admin_user_index');
    }
}
