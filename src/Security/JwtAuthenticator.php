<?php
namespace App\Security;

use AllowDynamicProperties;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;


#[AllowDynamicProperties] class JwtAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private JWTTokenManagerInterface $jwtManager, private UserRepository $userRepository)
    {
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');

        // L'utilisateur est récupéré depuis la base de données
        $user = $this->userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials');
        }

        if (password_verify($password, $user->getPassword()) and $user->isDisabled()) {
          throw new CustomUserMessageAuthenticationException('Disabled account');
        }


        // Vous pouvez ajouter ici la vérification du mot de passe si nécessaire
        // Exemple : $passwordValid = password_verify($password, $user->getPassword());

//        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $username);

        return new Passport(
            new UserBadge($username),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Créer le token JWT
        $jwt = $this->jwtManager->create($token->getUser());

        // Ajouter le token dans un cookie
        $cookie = new Cookie(
            'JWT',
            $jwt,
            time() + 3600,  // Le cookie expire après 1 heure
            '/',            // Le cookie est disponible pour toute l'application
            null,           // Pas de domaine spécifique
            false,          // Le cookie n'est pas accessible en HTTPS (tu peux le définir sur true en production)
            true            // HttpOnly pour empêcher l'accès via JavaScript
        );

        // Création de la réponse avec redirection ou page d'accueil
        $response = new RedirectResponse($this->urlGenerator->generate('home'));  // Redirige vers la page d'accueil ou autre page

        // Ajouter le cookie à la réponse
        $response->headers->setCookie($cookie);

        return $response;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

}
