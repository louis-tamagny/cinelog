<?php

namespace App\Controller;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    
    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout()
    {
        // Créer un cookie expiré pour supprimer le JWT
        $jwtCookie = new Cookie(
            'JWT',
            '',            // On vide la valeur du cookie
            time() - 3600,  // On donne au cookie une date d'expiration passée pour le supprimer
            '/',           //F Le cookie est supprimé sur tout le domaine
            null,          // Pas de domaine spécifique
            false,         // Si HTTPS est requis, mettre true en production
            true           // HttpOnly pour empêcher l'accès via JavaScript
        );
        
        $sessionCookie = new Cookie(
            'PHPSESSID',
            '',            // On vide la valeur du cookie
            time() - 3600,  // On donne au cookie une date d'expiration passée pour le supprimer
            '/',           //F Le cookie est supprimé sur tout le domaine
            null,          // Pas de domaine spécifique
            false,         // Si HTTPS est requis, mettre true en production
            true           // HttpOnly pour empêcher l'accès via JavaScript
        );

        // Ajouter le cookie expiré à la réponse
        $response = new RedirectResponse($this->generateUrl('login'));  // Redirige vers la page de login après déconnexion
        $response->headers->setCookie($jwtCookie);
        $response->headers->setCookie($sessionCookie);
        return $response;
    }
}
