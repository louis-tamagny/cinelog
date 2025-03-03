<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\ResponseRepository;
use App\Entity\User;
use App\Service\TmdbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{username}', name: 'user_detail')]
    public function user_detail(UserRepository $userRepository, ResponseRepository $responseRepository, string $username, CommentRepository $commentRepository, TmdbService $tmdb): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);
        $comments = $commentRepository->findBy(['commentUser' => $user]);
        $movies = [];
        foreach ($comments as $comment) {
            $tmdbId = $comment->getMovie()->getTmdbId();
            $movies[$tmdbId] = $tmdb->getMovieDetails($tmdbId);
            $movies[$tmdbId]['nbResponses'] = count($responseRepository->findBy(['comment' => $comment]));
        }
        return $this->render('user/user_detail.html.twig', [
            'user_detail' => $user,
            'comments' => $comments,
            'movies' => $movies,
        ]);
    }

    #[Route('/user/toggle-admin/{id}', name: 'toggle_admin')]
    public function toggleAdmin(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('user_list');
        }

        if ($user->isAdmin()) {
            $user->setRoles(array_diff($user->getRoles(), ['ROLE_ADMIN']));
        } else {
            $roles = $user->getRoles();
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
