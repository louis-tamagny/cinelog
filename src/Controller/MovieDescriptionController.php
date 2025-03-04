<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\TmdbService;

final class MovieDescriptionController extends AbstractController
{

    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    #[Route('/movie/details/{id}', name: 'movie_details')]
    public function details(int $id, TmdbService $tmdbService, CommentRepository $commentRepository, MovieRepository $movieRepository,UserRepository $userRepository): Response
    {
        $movie = $tmdbService->getMovieDetails($id);
        $trailerKey = $tmdbService->getMovieTrailer($id);
        $actorMovies = $tmdbService->getActorMovies($id);
        $createcomment = new Comment();
        $moviecomment = $movieRepository->findOneBy(['tmdbId' => $id]);
        $comments = $commentRepository->findBy(['movie' => $moviecomment]);

        $form = $this->createForm(CommentType::class, $createcomment);

        $user = $this->getUser();
        $userFavorite = $user ? $user->getFavourite()->contains($moviecomment) : False;
        $userWatchLater = $user ? $user->getWatchLater()->contains($moviecomment) : False;

        return $this->render('movie_description/index.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
            'trailerKey' => $trailerKey,
            'actorMovies' => $actorMovies,
            'comments' => $comments,
            'userFavorite' => $userFavorite,
            'userWatchLater' => $userWatchLater,
        ]);
    }

}
