<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
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
    public function details(int $id, TmdbService $tmdbService): Response
    {
        $movie = $tmdbService->getMovieDetails($id);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        return $this->render('movie_description/index.html.twig', [
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

}
