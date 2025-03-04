<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Service\TmdbService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $tmdbService;
    private $movieRepository;

    public function __construct(TmdbService $tmdbService, MovieRepository $movieRepository)
    {
        $this->tmdbService = $tmdbService;
        $this->movieRepository = $movieRepository;
    }

    #[Route('/', name: 'home')]
    public function search(Request $request): Response
    {
        $query = $request->query->get('query');
        $movies = [];

        if ($query) {
            $movies = $this->tmdbService->searchMovies($query, 6);
        }

        $topCommentedMovies = $this->movieRepository->findMoviesWithMostComments();
        $topCommentedMoviesDetails = [];

        foreach ($topCommentedMovies as $movie) {
            $movieDetails = $this->tmdbService->getMovieDetails($movie['tmdbId']);
            $movieDetails['comment_count'] = $movie['comment_count'];
            $topCommentedMoviesDetails[] = $movieDetails;
        }

        $topRatedMovies = $this->movieRepository->findTopRatedMovies();
        $topRatedMoviesDetails = [];

        foreach ($topRatedMovies as $movie) {
            $movieDetails = $this->tmdbService->getMovieDetails($movie['tmdbId']);
            $movieDetails['avg_rating'] = $movie['avg_rating'];
            $topRatedMoviesDetails[] = $movieDetails;
        }

        return $this->render('home/index.html.twig', [
            'query' => $query,
            'movies' => $movies,
            'topCommentedMoviesDetails' => $topCommentedMoviesDetails,
            'topRatedMoviesDetails' => $topRatedMoviesDetails,
        ]);
    }
}