<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;

final class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/{id}', name:'delete_comment', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Comment $comment): Response
    {
      if ($comment) {
        $entityManager->remove($comment);
        $entityManager->flush();
        $this->addFlash('success','Le commentaire a été effacé');
        return new Response('', Response::HTTP_NO_CONTENT);
      }
      return $this->redirectToRoute('');
    }

    #[Route('/movie/details/{tmdbId}', name:'movie_new_comment', methods: ['POST'])]
    public function new(Request $request, int $tmdbId, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de l'entité User
        $comment = new Comment();

        // la définition du User est à changer une fois le JWT fait
        // $user = $this->getUser();
        $user = $entityManager->getRepository(User::class)->find(1);
        $movie = $entityManager->getRepository(Movie::class)->findOneBy(['tmdbId' => $tmdbId]);
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
}
