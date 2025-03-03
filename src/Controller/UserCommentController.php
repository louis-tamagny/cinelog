<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CommentRepository;
use App\Service\TmdbService;

class UserCommentController extends AbstractController
{
    private $tmdbService;
    private $commentRepository;

    public function __construct(TmdbService $tmdbService, CommentRepository $commentRepository)
    {
        $this->tmdbService = $tmdbService;
        $this->commentRepository = $commentRepository;
    }

    #[Route('/user/comments', name: 'user_comments')]
    public function userComments(): Response
    {
        $user = $this->getUser();
        $comments = $this->commentRepository->findCommentsByUser($user);
        $commentsDetails = [];

        foreach ($comments as $comment) {
            $commentDetails = $this->tmdbService->getMovieDetails($comment['tmdbId']);
            $commentDetails['comment'] = $comment[0];
            $commentsDetails[] = $commentDetails;
        }

        return $this->render('user_comment/index.html.twig', [
            'comments' => $commentsDetails,
        ]);
    }
}