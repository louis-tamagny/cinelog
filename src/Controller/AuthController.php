<?php

namespace App\Controller;

use App\Form\RegisterFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class AuthController extends AbstractController
{

    public function __construct(readonly private JWTTokenManagerInterface $jwtManager){}

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository, LoggerInterface $logger): Response
    {
        $form = $this->createForm(RegisterFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $usernameAlreadyExist = $userRepository->findOneBy(['username' => $user->getUsername()]);

            if (!$usernameAlreadyExist) {
                $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($hashedPassword);

                $entityManager->persist($user);
                $entityManager->flush();
                return $this->redirect('login');

            } else {
                return $this->render('register/index.html.twig', [
                    'form' => $form->createView(),
                    'error' => "Ce nom d'utilisateur existe déjà."
                ]);
            }

        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'error' => null
        ]);
    }
}
