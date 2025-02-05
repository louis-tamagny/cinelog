<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\CommentRepository;

final class CommentController extends AbstractController
{
    #[Route('/comment', name: 'app_comment')]
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    #[Route('/comment/:id', name:'delete_comment', methods: ['DELETE'])]
    public function delete(CommentRepository $commentRepository, int $id): Response
    {
      $comment = $commentRepository->find($id);
      if ($comment) {
        $comment->delete();
        $this->addFlash('success','Le commentaire a été effacé');
        return $this->redirectToRoute('');
      }
      return $this->redirectToRoute('');
    }
}
