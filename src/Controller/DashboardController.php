<?php

namespace App\Controller;

use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\MovieRepository;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(UserRepository $usersRepository, CommentRepository $commentRepository, TmdbService $tmdbService): Response
    {
        $lesserComments = $commentRepository->findAll();
        $users = $usersRepository->findAll();
        $comments = [];

        foreach ($lesserComments as $comment) {
          $movieDetail = $tmdbService->getMovieDetails($comment->getMovie()->getTmdbId());
          $comments[] = [
            'comment' => $comment,
            'movie' => $movieDetail,
          ];
        }

        return $this->render('dashboard/index.html.twig', [
            'users' => $users,
            'comments' => $comments,
        ]);
    }
}
