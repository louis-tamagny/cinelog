<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\CommentRepository;

class UserCommentController extends AbstractController
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    #[Route('/user/comments', name: 'user_comments')]
    public function userComments(): Response
    {
        $user = $this->getUser();
        $comments = $this->commentRepository->findCommentsByUser($user);

        return $this->render('user_comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }
}
