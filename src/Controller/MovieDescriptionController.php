<?php

namespace App\Controller;

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

        return $this->render('movie_description/index.html.twig', [
            'movie' => $movie,
        ]);
    }

}
