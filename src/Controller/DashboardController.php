<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use App\Repository\CommentRepository;
use App\Repository\MovieRepository;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(UserRepository $usersRepository): Response
    {
        $users = $usersRepository->findAll();
        return $this->render('dashboard/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/dashboard/comment', name: 'show_comment')]
    public function show_comment(CommentRepository $commentsRepository): Response
    {
        $comments = $commentsRepository->findAll();
        return $this->render('dashboard/show_comment.html.twig', [
            'comments' => $comments,
        ]);
    }
}
