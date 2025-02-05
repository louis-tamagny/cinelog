<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\CommentRepository;
use App\Entity\Comment;
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
}
