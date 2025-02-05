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

    #[Route('/movie/{id}/new_comment', name:'movie_new_comment', methods: ['GET', 'POST'])]
    public function new(Request $request, Movie $movie, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de l'entité User
        $comment = new Comment();


        $user = $entityManager->getRepository(User::class)->find(1);

        // Créer le formulaire et le lier à l'entité
        $form = $this->createForm(CommentType::class, $comment);

        $comment->setCommentUser($user);
        $comment->setMovie($movie);
        // Traiter la requête HTTP
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarder l'utilisateur dans la base de données
            $entityManager->persist($comment);
            $entityManager->flush();

            // Rediriger vers une autre page ou afficher un message de succès
            return $this->redirectToRoute('app_comment');
        }

        // Rendre le formulaire pour qu'il soit affiché à l'utilisateur
        return $this->render('comment/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
