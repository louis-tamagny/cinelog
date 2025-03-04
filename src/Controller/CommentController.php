<?php

namespace App\Controller;

use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Comment;
use App\Entity\Response as CommentResponse;
use App\Form\CommentType;
use App\Repository\MovieRepository;
use App\Repository\ResponseRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\TmdbService;

final class CommentController extends AbstractController
{
    #[Route('/comment/{id}', name:'delete_comment', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Comment $comment): Response
    {
      if ($comment) {
        foreach ($comment->getResponses() as $response) {
          $entityManager->remove($response);
        }
        $entityManager->remove($comment);
        $entityManager->flush();
        $this->addFlash( 'success','Le commentaire a été effacé');
        return $this->redirectToRoute('app_dashboard', status: Response::HTTP_SEE_OTHER);
      }
      return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/movie/details/{tmdbId}', name:'movie_new_comment', methods: ['POST'])]
    public function new(Request $request, int $tmdbId, EntityManagerInterface $entityManager, CommentRepository $commentRepository): Response
    {
        // Créer une nouvelle instance de l'entité User
        $comment = new Comment();

        $user = $this->getUser();
        if (!$user) {
          return $this->redirectToRoute('login');
        }

        $movie = $entityManager->getRepository(Movie::class)->findOneBy(['tmdbId' => $tmdbId]);
        if (!$movie) {
          $movie = new Movie();
          $movie->setTmdbId($tmdbId);
          $entityManager->persist($movie);
        }

        // Vérifier si l'utilisateur a déjà écrit un commentaire pour ce film
        $existingComment = $commentRepository->findOneBy(['commentUser' => $user, 'movie' => $movie]);
        if ($existingComment) {
            $this->addFlash('error', 'Vous avez déjà écrit un commentaire pour ce film.');
            return $this->redirectToRoute('movie_details', ['id' => $movie->getTmdbId()]);
        }

        $date = new \DateTime();

        // Créer le formulaire et le lier à l'entité
        $form = $this->createForm(CommentType::class, $comment);

        // Attacher les attributs automatiques
        $comment->setCommentUser($user);
        $comment->setMovie($movie);
        $comment->setDate($date);
        // Traiter la requête HTTP
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($comment);
            $entityManager->flush();

            // Rediriger vers une autre page ou afficher un message de succès
            return $this->redirectToRoute('movie_details', ['id' => $movie->getTmdbId()]);
        }

        // Rendre le formulaire pour qu'il soit affiché à l'utilisateur
        return $this->redirectToRoute('movie_details', ['id' => $movie->getTmdbId()], status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    #[Route('/comment/{id}/response', name: 'commentResponse')]
    public function commentResponse(int $id, TmdbService $tmdb, CommentRepository $commentRepository, ResponseRepository $responseRepository, MovieRepository $movieRepository): Response
    {
        $comment = $commentRepository->find(['id' => $id]);
        $tmdbId = $comment->getMovie()->getTmdbId();
        $movie = $tmdb->getMovieDetails($tmdbId);
        $responses = $comment->getResponses();
        return $this->render('comment/response.html.twig', [
            'comment' => $comment,
            'movie' => $movie,
            'responses' => $responses
        ]);
    }

    #[Route('/comment/{id}/add_response', name: 'add_response', methods: ['POST'])]
    public function addResponse(int $id, Request $request, CommentRepository $commentRepository, EntityManagerInterface $entityManager, ResponseRepository $responseRepository): Response
    {
        $comment = $commentRepository->find($id);
        $user = $this->getUser();

        // Empêcher la personne qui a mis l'avis de répondre à son propre commentaire
        if ($comment->getCommentUser() === $user) {
          return $this->redirectToRoute('commentResponse', ['id' => $id]);
        }

        // Empêcher une personne qui a déjà répondu de répondre à nouveau
        $existingResponse = $responseRepository->findOneBy(['comment' => $comment, 'userResponse' => $user]);
        if ($existingResponse) {
            return $this->redirectToRoute('commentResponse', ['id' => $id]);
        }

        $responseMessage = $request->request->get('response_message');

        if ($responseMessage) {
            $response = new CommentResponse();
            $response->setComment($comment);
            $response->setMessage($responseMessage);
            $response->setDate(new \DateTime());
            $response->setUserResponse($this->getUser());

            $entityManager->persist($response);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commentResponse', ['id' => $id]);
    }
}
