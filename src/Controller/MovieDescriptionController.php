<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
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
    public function details(int $id, TmdbService $tmdbService, CommentRepository $commentRepository): Response
    {
        $movie = $tmdbService->getMovieDetails($id);
        $trailerKey = $tmdbService->getMovieTrailer($id);
        $createcomment = new Comment();
        $comments = $commentRepository->findBy(['movie' => $movie]);
        $movies = [];
        foreach ($comments as $comment) {
            $tmdbId = $comment->getMovie();
            $movies[$tmdbId] = $tmdbService->getMovieDetails($tmdbId);
        }
        $form = $this->createForm(CommentType::class, $createcomment);

        return $this->render('movie_description/index.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
            'trailerKey' => $trailerKey,
            'comments' => $comments
        ]);
    }

}
