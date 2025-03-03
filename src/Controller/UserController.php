<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\MovieRepository;
use App\Entity\User;
use App\Service\TmdbService;

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
    public function user_detail(UserRepository $userRepository, string $username, CommentRepository $commentRepository, TmdbService $tmdb): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);
        $comments = $commentRepository->findBy(['commentUser' => $user]);
        $movies = [];
        foreach ($comments as $comment) {
            $tmdbId = $comment->getMovie()->getTmdbId();
            $movies[$tmdbId] = $tmdb->getMovieDetails($tmdbId);
        }
        return $this->render('user/user_detail.html.twig', [
            'user_detail' => $user,
            'comments' => $comments,
            'movies' => $movies,
        ]);
    }
}
