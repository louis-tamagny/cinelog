<?php

namespace App\Controller;

use App\Form\UserEditType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

      #[Route('/user/profile', name: 'user_profile')]
      public function user_profile(Request $request, EntityManagerInterface $entityManager, TmdbService $tmdbService): Response
      {
        $user = $this->getUser();
        $favourites = $user->getFavourite();
        $favouriteMovies = [];
        $watchLaters = $user->getWatchLater();
        $watchLaterMovies = [];

        foreach ($favourites as $favourite) {
          $favouriteMovies[] = $tmdbService->getMovieDetails($favourite->getTmdbId());
        }

        foreach ($watchLaters as $watchLater) {
          $watchLaterMovies[] = $tmdbService->getMovieDetails($watchLater->getTmdbId());
        }



        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updatedUser = $form->getData();
            $user->setEmail($updatedUser->getEmail());
            $user->setBirthDate($updatedUser->getBirthDate());
            $user->setUsername($updatedUser->getUsername());

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('user/profile.html.twig', [
          'user' => $user,
          'favouriteMovies' => $favouriteMovies,
          'watchLaterMovies' => $watchLaterMovies,
          'form' => $this->createForm(UserEditType::class, $user),
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

    #[Route('/user/{id}/toggle-admin', name: 'toggle_admin')]
    public function toggleAdmin(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('user_list');
        }

        if ($user->isAdmin()) {
            $user->setRoles(['ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/user/{id}/toggle-disabled', name: 'toggle_disabled', methods: ["POST"])]
    public function toggleDisabled(User $user, EntityManagerInterface $entityManager): RedirectResponse
    {
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('error', 'Vous ne pouvez pas vous bannir.');
            return $this->redirectToRoute('app_dashboard');
        }

        $user->setIsDisabled(!$user->isDisabled());

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
    }
}
