<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MovieController extends AbstractController
{
    #[Route('/movies/{tmdbId}/toggle-favourite', name: 'favorite_movie', methods: ['POST'])]
    public function toggleFavourite(int $tmdbId, MovieRepository $movieRepository, EntityManagerInterface $em): Response
    {
        $movie = $movieRepository->findOneBy(['tmdbId' => $tmdbId]);
        if (!$movie)
        {
          $movie = new Movie();
          $movie->setTmdbId($tmdbId);
          $em->persist($movie);
          $em->flush();
        }

        $user = $this->getUser();
        if ($user->getFavourite()->contains($movie)){
          $user->removeFavourite($movie);
        } else {
          $user->addFavourite($movie);
        }
        $em->flush();

        return $this->redirectToRoute('movie_details', ['id'=> $movie->getTmdbId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/movies/{tmdbId}/toggle-watch-later', name: 'watch_later_movie', methods: ['POST'])]
    public function toggleWatchLater(int $tmdbId, MovieRepository $movieRepository, EntityManagerInterface $em): Response
    {
      $movie = $movieRepository->findOneBy(['tmdbId' => $tmdbId]);
      if (!$movie)
        {
          $movie = new Movie();
          $movie->setTmdbId($tmdbId);
          $em->persist($movie);
          $em->flush();
        }

        $user = $this->getUser();
        if ($user->getWatchLater()->contains($movie)){
          $user->removeWatchLater($movie);
        } else {
          $user->addWatchLater($movie);
        }
        $em->flush();

        return $this->redirectToRoute('movie_details', ['id'=> $movie->getTmdbId()], Response::HTTP_SEE_OTHER);
    }
}
